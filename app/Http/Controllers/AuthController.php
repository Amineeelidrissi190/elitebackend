<?php

namespace App\Http\Controllers;

use Cache;
use App\Models\User;
use App\Models\admin;
use App\Models\coach;
use App\Models\client;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Facades\Hash; // Import Hash facade

class AuthController extends Controller
{
    /**
     * Create a new AuthController instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth:api', ['except' => ['login', 'register']]);
    }
    public function UpdateProfil($id, Request $request) {
        // Valider les données de la requête
        $validate = $request->validate([
            'nom_admin' => 'required|string|max:255',
            'prenom_admin' => 'required|string|max:255',
            'phone_admin' => 'required|numeric',
            'image_admin' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
            'email' => 'required|email|unique:users,email,' . $id,
            'password' => 'required|string|min:8',
        ]);
    
        try {
            // Mettre à jour les informations de l'utilisateur
            $user = User::findOrFail($id);
            $user->update([
                'email' => $validate['email'],
                'password' => Hash::make($validate['password']),
            ]);
    
            // Mettre à jour les informations de l'admin
            $admin = admin::where('id_users', $id)->firstOrFail();
            $admin->update([
                'nom_admin' => $validate['nom_admin'],
                'prenom_admin' => $validate['prenom_admin'],
                'phone_admin' => $validate['phone_admin'],
            ]);
    
            // Gestion de l'image de l'admin
            if ($request->hasFile('image_admin')) {
                // Supprimer l'ancienne image si elle existe
                $existingImage = $admin->image_admin;
                if ($existingImage) {
                    Storage::disk('public')->delete("photos/admin/{$existingImage}");
                }
    
                // Enregistrer la nouvelle image
                $image_adminName = time() . '_' . $request->file('image_admin')->getClientOriginalName();
                $request->file('image_admin')->storeAs('photos/admin', $image_adminName, 'public');
                $admin->image_admin = $image_adminName;
                $admin->save();
            }
    
            return response()->json([
                'message' => 'Admin updated successfully'
            ], 200);
    
        } catch (\Exception $e) {
            return response()->json([
                'error' => 'An error occurred while updating the profile.',
                'details' => $e->getMessage(),
            ], 500);
        }
    }

    public function store(Request $request)
    {
        $role = $request->input('role', 'client');
        switch ($role) {
            case 'client':
                return $this->register($request);
                break;
            case 'admin':
                return $this->storeAdmin($request);
                break;
            case 'coach':
                return $this->storeCoach($request);
                break;
            default:
                return response()->json(['error' => 'Invalid role'], 422);
        }
    }
    
    

    public function register(Request $request)
    {
        try {
            $data = $request->validate([
                "email" => "required|email|unique:users,email",
                "password" => "required",
            ]);
            $data["password"] = bcrypt($data["password"]);
            $role = $request->input('role', 'client');
            $user = User::create([
                'email' => $request->email,
                'password' => Hash::make($request->input('password')),
                'role' => $role,
            ]);

            $userId = $user->id;
            if ($role === 'client') {
                return $this->storeClient($request, $userId);
            }
            if ($role === 'admin') {
                return $this->storeAdmin($request, $userId);
            }
            if ($role === 'coach') {
                return $this->storeCoach($request, $userId);
            }

            $token = auth()->login($user);
            return $this->respondWithToken($token);
        } catch (ValidationException $e) {
            return response()->json([
                'errors' => $e->errors(),
            ]);
        }
    }

    public function storeClient(Request $request, $userId)
    {
        try {
            // Récupérer les données validées
            $validatedclient = $request->validate([
                'name_client' => 'required',
                'age_client' => 'required|numeric',
                'numero_tel' => 'required|numeric',
                'specialite' => 'required',
            ], [
                'required' => 'Le champ  est requis.',
                'string' => 'Le champ  doit être une chaîne de caractères.',
                'image' => 'Le champ  doit être une image.',
                'numeric' => 'Ce champ  doit être un nombre.',
            ]);

            client::create(array_merge($validatedclient, ['users_id' => $userId]));

            return response()->json([
                'message' => 'Client added successfully'
            ]);
        } catch (ValidationException $e) {
            
            return response()->json([
                'errors' => $e->errors(),
            ], 422); 
        }
    }    public function storeAdmin(Request $request, $userId)
    {
        try {
            // Récupérer les données validées
            $validatedadmin = $request->validate([
                'nom_admin' => 'required',
                'prenom_admin' => 'required',
                'phone_admin' => 'required|numeric',
                'image_admin' => 'required|image',
            ], [
                'required' => 'Le champ  est requis.',
                'string' => 'Le champ  doit être une chaîne de caractères.',
                'image' => 'Le champ  doit être une image.',
                'numeric' => 'Ce champ  doit être un nombre.',
            ]);

            $image_adminName = $request->image_admin->getClientOriginalName();
            Storage::disk('public')->putFileAs('photos/admin', $request->image_admin, $image_adminName);
            admin::create(array_merge($validatedadmin, ['image_admin' => $image_adminName, 'id_users' => $userId]));

            return response()->json([
                'message' => 'admin added successefully'
            ]);
        } catch (ValidationException $e) {
            return response()->json([
                'errors' => $e->errors(),
            ], 422); 
        }
    }

    public function storeCoach(Request $request, $userId)
    {
        try {
            // Récupérer les données validées
            $validatedcoach = $request->validate([
                'nom_coach' => 'required',
                'prenom_coach' => 'required',
                'phone_coach' => 'required',
                'image_coach' => 'required | image',
                'description' => 'required',
            ], [
                'required' => 'Le champ  est requis.',
                'string' => 'Le champ  doit être une chaîne de caractères.',
                'image' => 'Le champ  doit être une image.',
            ]);
            // Créer le modèle Coach en incluant l'ID de l'utilisateur
            $image_coachName = $request->image_coach->getClientOriginalName();
            Storage::disk('public')->putFileAs('photos/coach', $request->image_coach, $image_coachName);
            coach::create(array_merge($validatedcoach, ['image_coach' => $image_coachName, 'users_id' => $userId]));

            return response()->json([
                'message' => 'Coach added successefully'
            ]);
        } catch (ValidationException $e) {
            
            return response()->json([
                'errors' => $e->errors(),
            ], 422); 
        }
    }

 

    /**
     * Get a JWT via given credentials.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function login(Request $request)
{
    $credentials = $request->only('email', 'password');
    
    if ($token = auth()->attempt($credentials)) {
        $user = auth()->user();
        Cache::put('user-is-online-' . $user->id, true, now()->addMinutes(config('session.lifetime')));
        if ($user->role == 'admin') {
            $userData = User::join('admins', 'users.id', '=', 'admins.id_users')
                            ->where('users.id', $user->id)
                            ->first();
        } elseif ($user->role == 'client') {
            $userData = User::join('clients', 'users.id', '=', 'clients.users_id')
                            ->where('users.id', $user->id)
                            ->first();
        } else {
            $userData = $user;
        }

        // Get token response
        $tokenResponse = $this->respondWithToken($token)->getData();

        return response()->json([
            'user' => $userData,
            'token' => $tokenResponse
        ], 200);
    } else {
        // Authentification échouée
        return response()->json([
            'error' => 'Unauthorized',
        ], 401);
    }
}

    /**
     * Get the authenticated User.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function me()
    {
        $user = Auth::user();

        if ($user->role == 'admin') {
            
            $userData = User::join('admins', 'users.id', '=', 'admins.id_users')
                            ->where('users.id', $user->id)
                            ->first();
        } elseif ($user->role == 'client') {
            
            $userData = User::join('clients', 'users.id', '=', 'clients.users_id')
                            ->where('users.id', $user->id)
                            ->first();
        }
        return response()->json([
            'user' => $userData,
        ]);
    }

    /**
     * Log the user out (Invalidate the token).
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function logout(Request $request)
    {
        // Récupération de l'utilisateur authentifié
        $user = Auth::user();
        
        if ($user) {
            Cache::forget('user-is-online-' . $user->id);
            Auth::logout();
            return response()->json(['message' => 'Successfully logged out'], 200);
        }
        return response()->json(['message' => 'No user logged in'], 401);
    }
    public function getConnectedUsers()
    {
        $users = User::all();
        $connectedUsers = $users->filter(function($user) {
            return Cache::has('user-is-online-' . $user->id);
        });
    
        $connectedUsersData = $connectedUsers->map(function($user) {
            if ($user->role == 'admin') {
                $userData = User::join('admins', 'users.id', '=', 'admins.id_users')
                                ->where('users.id', $user->id)
                                ->first();
            } elseif ($user->role == 'client') {
                $userData = User::join('clients', 'users.id', '=', 'clients.users_id')
                                ->where('users.id', $user->id)
                                ->first();
            } else {
                $userData = $user;
            }
    
            return $userData;
        });
    
        return $connectedUsersData->values()->all();
    }
    
    
    

    /**
     * Refresh a token.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function refresh()
    {
        return $this->respondWithToken(auth()->refresh());
    }

    /**
     * Get the token array structure.
     *
     * @param  string $token
     *
     * @return \Illuminate\Http\JsonResponse
     */
    protected function respondWithToken($token)
    {
        return response()->json([
            'access_token' => $token,
            'token_type' => 'bearer',
            'expires_in' => auth()->factory()->getTTL()
        ]);
    }
}
