<?php

namespace App\Http\Controllers;

use App\Models\emploi;
use Illuminate\Http\Request;

class emploicontroller extends Controller
{
    public function index()
    {
        $emploi=emploi::all();
        return response()->json($emploi);

        //
    }
    public function store(Request $request)
    {
        $validatedemploi = $request->validate([
            'img_emploi' => 'required',
         
        ]);
        $create_emploi=emploi::Create($validatedemploi);
        return response()->json($create_emploi);
        //
    }
    
   

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $emploi = emploi::findOrFail($id);
        return response()->json($emploi);
        //
    }
    /**
     * Update the
     *  specified resource in storage.
     */
    public function update(Request $request, string $id)
    { 
        $validatedemploi = $request->validate([
            'img_emploi' => 'required',
     
        ]);
        $emploi = emploi::findOrFail($id);
        $emploi->update($validatedemploi);
        return response()->json($emploi);
        
    }
    

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $emploi = emploi::findOrFail($id);
        $emploi->delete();
    }
}
