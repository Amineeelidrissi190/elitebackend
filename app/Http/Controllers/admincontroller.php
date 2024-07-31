<?php

namespace App\Http\Controllers;

use App\Models\admin;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\ValidationException;
class admincontroller extends Controller
{
    public function index()
    {
        $admin=admin::with('User')->get();
        Log::info($admin);
        return response()->json($admin);
        

        //
    }
    public function store(Request $request)
    {
        try{

            $validatedadmin = $request->validate([
                'nom_admin' => 'required',
                'prenom_admin' => 'required',
                'phone_admin' => 'required|numeric',
                'image_admin' => 'required|image',
                'id_users' => 'required',
             
            ],[
                'required' => 'Le champ  est requis.',
                'string' => 'Le champ  doit être une chaîne de caractères.',
                'image' => 'Le champ  doit être une image.',
                'numeric' => 'Ce champ  doit être un nombre.',
            ]);
            $image_adminName = $request->image_admin->getClientOriginalName();
            Storage::disk('public')->putFileAs('photos/admin',$request->image_admin,$image_adminName);
            admin::create($request->post() + ['image_admin'=>$image_adminName]);
        return response()->json([
            'message'=>'admin added successefully'
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
        $admin = admin::findOrFail($id);
        return response()->json($admin);
        //
    }
    /**
     * Update the
     *  specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        try{
            $validatedadmin = $request->validate([
                'nom_admin' => 'required',
                'prenom_admin' => 'required',
                'phone_admin' => 'required|numeric',
                'image_admin' => 'required',
                'id_users' => 'required',
             
            ],[
                'required' => 'Le champ  est requis.',
                'string' => 'Le champ  doit être une chaîne de caractères.',
                'image' => 'Le champ  doit être une image.',
                'numeric' => 'Ce champ  doit être un nombre.',
            ]);
            $admin = admin::findOrFail($id);
            $admin->update($validatedadmin);
    
            if ($request->hasFile('image_admin')) {
                $existingImage = $admin->image_admin;

                if ($existingImage) {
                    Storage::disk('public')->delete("photos/admin/{$existingImage}");
                }
    
                $image_adminName = $request->file('image_admin')->getClientOriginalName();
                $request->file('image_admin')->storeAs('photos/admin', $image_adminName, 'public');
                $admin->image_admin = $image_adminName;
                $admin->save();
            }
            return response()->json([
                'message' => 'admin updated successfully'
            ]);

        }catch (ValidationException $e) {
            return response()->json([
                'errors' => $e->errors(),
            ], 422); 
        } 
       
        
    }
    

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {

        $admin = admin::findOrFail($id);

        if ($admin->image_admin) {
            $exists = Storage::disk('public')->exists("photos/admin/{$admin->image_admin}");
    
            if ($exists) {
                Storage::disk('public')->delete("photos/admin/{$admin->image_admin}");
            }
        }
    
        $admin->user->delete(); 
        return response()->json([
            'message' => 'admin deleted successfully'
        ]);
    }
}
