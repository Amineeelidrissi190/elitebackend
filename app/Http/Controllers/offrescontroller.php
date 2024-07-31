<?php

namespace App\Http\Controllers;

use App\Models\offre;
use Illuminate\Http\Request;

class offrescontroller extends Controller
{
    public function index()
    {
        $offre=offre::all();
        return response()->json($offre);

        //
    }
    public function store(Request $request)
    {
        try{
            $validatedoffre = $request->validate([
                "nom_offre"=>"required",
                "date_offre_deb"=>"required",
                "date_offre_fin"=>"required",
                "content_offre"=>"required",
                "date_offre_fin"=>"required",
                "specialite_id"=>"required"
            ],[
                'required' => 'Ce champ est requis.',
                'date' => 'Ce champ  doit être une date.',
                'String' => 'Ce champ doit être une chaine de caractere.'
    
            ]);
            $offre = offre::create($validatedoffre);
            return response()->json([$offre,
            'message' => 'Offer added successfully'
        ]);

        }
        catch(ValidationException $e){
            return response()->json([
                'errors' => $e->errors(),
            ], 422); 

        }
        
    }
   

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $offre = offre::findOrFail($id);
        return response()->json($offre);
        //
    }
    /**
     * Update the
     *  specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        try{
            $validatedoffre = $request->validate([
                "nom_offre"=>"required",
                "date_offre_deb"=>"required",
                "date_offre_fin"=>"required",
                "content_offre"=>"required",
                "date_offre_fin"=>"required",
                "specialite_id"=>"required"
            ],[
                'required' => 'Ce champ est requis.',
                'date' => 'Ce champ  doit être une date.',
                'String' => 'Ce champ doit être une chaine de caractere.'
    
            ]);
            $offre = offre::findOrFail($id);
            $offre->update($validatedoffre);
            return response()->json([$offre,
            'message' => 'Offer updated successfully'
        ]);
    }
    catch(ValidationException $e){
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
        $offre = offre::find($id);
        $offre->delete();
        info($offre);
    }
}
