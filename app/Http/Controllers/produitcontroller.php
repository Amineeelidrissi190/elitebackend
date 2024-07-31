<?php

namespace App\Http\Controllers;

use Exception;
use App\Models\produit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\ValidationException;

class produitcontroller extends Controller
{
    public function index()
    {
        $produit=produit::all();
        return response()->json($produit);

        //
    }
    public function store(Request $request)
    {
        $validatedProduit = $request->validate([
            'nom_produit' => 'required|string',
            'img_produit' => 'required|image',
            'desc_produit' => 'required|string',
            'prix' => 'required|numeric',
        ],[
            'required' => 'This field is required.',
            'image' => 'This field must be an image.',
            'string' => 'This field must be a string.',
            'numeric' => 'This field must be a number.',

        ]);
        
        $img_produit = $request->img_produit->getClientOriginalName();
        Storage::disk('public')->putFileAs('photos/product',$request->img_produit, $img_produit);
        produit::create($request->post() + ['img_produit'=>$img_produit]);
        return response()->json([
            'message'=>'Product added successefully'
        ]);

        

    }
   

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $produit = produit::findOrFail($id);
        return response()->json($produit);
        //
    }
    /**
     * Update the
     *  specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        try {
            $validatedProduit = $request->validate([
                'nom_produit' => 'required|string',
                'img_produit' => 'nullable|image',
                'desc_produit' => 'required|string',
                'prix' => 'required|numeric',
            ], [
                'required' => 'This field is required.',
                'image' => 'This field must be an image.',
                'string' => 'This field must be a string.',
                'numeric' => 'This field must be a number.',
            ]);
            
            $produit = Produit::findOrFail($id);
            $produit->update($validatedProduit);
            
            if ($request->hasFile('img_produit')) {
                $existingImage = $produit->img_produit;
                if ($existingImage) {
                    Storage::disk('public')->delete("photos/product/{$existingImage}");
                }
                $image_produit = $request->file('img_produit')->getClientOriginalName();
                $request->file('img_produit')->storeAs('photos/product', $image_produit, 'public');
                $produit->img_produit = $image_produit;
                $produit->save();
            }
    
            return response()->json([
                'message' => 'Produit mis à jour avec succès',
                'produit' => $produit 
            ]);
        } catch (ValidationException $e) {
            
            return response()->json([
                'errors' => $e->errors(),
            ], 422);
        } catch (Exception $e) {
            
            return response()->json([
                'message' => 'Une erreur est survenue lors de la mise à jour du produit.',
                'error' => $e->getMessage()
            ], 500);
        }
    }
    
    

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $produit = produit::findOrFail($id);
    if ($produit->img_produit) {
        $exists = Storage::disk('public')->exists("photos/product/{$produit->img_produit}");

        if ($exists) {
            Storage::disk('public')->delete("photos/product/{$produit->img_product}");
        }
    }

    $produit->delete();

    return response()->json([
        'message' => 'product deleted successfully'
    ]);
    }
}
