<?php

namespace App\Http\Controllers;
use App\Models\coach;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\ValidationException;
class coachcontroller extends Controller
{
    public function index()
    {
        $coach=coach::with('user')->get();

        return response()->json($coach);

        //
    }
    public function store(Request $request)
    {
        try{
            $validatedcoach = $request->validate([
                'nom_coach' => 'required',
                'prenom_coach' => 'required',
                'phone_coach' => 'required',
                'image_coach' => 'required | image',
                'description' => 'required',
                'users_id' => 'required',
            ],[
                'required' => 'Le champ  est requis.',
                'string' => 'Le champ  doit être une chaîne de caractères.',
                'image' => 'Le champ  doit être une image.',
            ]);
                $image_coachName = $request->image_coach->getClientOriginalName();
                Storage::disk('public')->putFileAs('photos/coach',$request->image_coach,$image_coachName);
                coach::create($request->post() + ['image_coach'=>$image_coachName]);
            return response()->json([
                'message'=>'Coach added successefully'
            ]);

        }catch (ValidationException $e) {
            // Si la validation échoue, renvoyer les erreurs de validation
            return response()->json([
                'errors' => $e->errors(),
            ], 422); // Code de statut 422 pour les erreurs de validation
        } 

    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $coach = coach::findOrFail($id);
        return response()->json($coach);
    }
    /**
     * Update the
     *  specified resource in storage.
     */
   
   
     public function update(Request $request, $id)
     {
        try{
            $request->validate([
                'nom_coach' => 'required',
                'prenom_coach' => 'required',
                'phone_coach' => 'required',
                'image_coach' => 'nullable|image', // Assuming 'image' validation rule for image uploads
                'description' => 'required',
                'users_id' => 'required|exists:users,id', // Validate that the provided comptes_id exists in the 'comptes' table
            ]);
    
            // Find the coach by ID
            $coach = coach::findOrFail($id);
    
            // Update the coach model with the validated data
            $coach->update([
                'nom_coach' => $request->input('nom_coach'),
                'prenom_coach' => $request->input('prenom_coach'),
                'phone_coach' => $request->input('phone_coach'),
                'description' => $request->input('description'),
                'users_id' => $request->input('users_id'),
            ]);
    
            // Handle image upload if provided
            if ($request->hasFile('image_coach')) {
                $existingImage = $coach->image_coach;
    
                // Delete the existing image if it exists
                if ($existingImage) {
                    Storage::disk('public')->delete("photos/coach/{$existingImage}");
                }
    
                // Save the new image
                $image_coachName = $request->file('image_coach')->getClientOriginalName();
                $request->file('image_coach')->storeAs('photos/coach', $image_coachName, 'public');
                $coach->image_coach = $image_coachName;
                $coach->save();
            }
    
            return response()->json([
                'message' => 'Coach updated successfully'
            ]);

        }catch (ValidationException $e) {
            // Si la validation échoue, renvoyer les erreurs de validation
            return response()->json([
                'errors' => $e->errors(),
            ], 422); // Code de statut 422 pour les erreurs de validation
        }
         // Validation rules
         
     }

    
    

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        try {
            $coach = Coach::findOrFail($id);
    
            // Delete coach's image if it exists
            if ($coach->image_coach) {
                $exists = Storage::disk('public')->exists("photos/coach/{$coach->image_coach}");
    
                if ($exists) {
                    Storage::disk('public')->delete("photos/coach/{$coach->image_coach}");
                }
            }
    
            // Delete associated user
            if ($coach->user) {
                $coach->user->delete();
            }
    
            // Delete the coach
            $coach->delete();
    
            return response()->json([
                'message' => 'Coach deleted successfully'
            ]);
        } catch (\Exception $e) {
            // Log the error or handle it appropriately
            return response()->json([
                'error' => 'Failed to delete coach',
                'message' => $e->getMessage()
            ], 500);
        }
    }
}
