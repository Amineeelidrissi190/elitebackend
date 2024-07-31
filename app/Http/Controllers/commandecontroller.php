<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\client;
use App\Models\commande;
use Illuminate\Http\Request;

class commandecontroller extends Controller
{
    public function index()
    {
        $utilisateurs = commande::with(['user.client', "produit"])->whereHas('produit')->get();

        return response()->json($utilisateurs);
    

        //
    }
    public function LastCommands(){
        $utilisateurs = commande::with(['user.client', "produit"])->whereHas('produit')->orderBy("created_at","desc")->take(10)->get();
        return response()->json($utilisateurs);

    }
    public function Chatcommand(){
        $inscriptions = commande::all();
        $datesCreated = [];
        foreach ($inscriptions as $inscription) {
            $createdAt = $inscription->created_at->toDateString();
            
            if (array_key_exists($createdAt, $datesCreated)) {
                $datesCreated[$createdAt]++;
            } else {
                $datesCreated[$createdAt] = 1;
            }
        }
        
        $formattedData = [];
        foreach ($datesCreated as $date => $count) {
            $formattedData[] = [
                'x' => strtotime($date) * 1000, // Convertir la date en millisecondes UNIX
                'y' => $count
            ];
        }
        
        return response()->json($formattedData);
    }
    public function Total(){
        $total = commande::count();
        return response()->json($total);
    }
    public function store(Request $request)
    {
        $validatedcommande = $request->validate([
            'paiement' => 'required', 
            'id_user' => 'required',   
            'produits_id' => 'required',        
        ]);
        $create_commande=commande::Create($validatedcommande);
        return response()->json($create_commande);
        //
    }
   

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $commande = User::findOrFail($id)->with(['client', 'commandes'])->whereHas('commandes')->get();
        return response()->json($commande);
        //
    }
    /**
     * Update the
     *  specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $validatedcommande = $request->validate([
            'paiement' => 'required', 
            'id_user' => 'required',   
            'produits_id' => 'required',
        ]);
        $commande = commande::findOrFail($id);
        $commande->update($validatedcommande);
        return response()->json($commande);
        
    }
    

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $commande = commande::findOrFail($id);
        $commande->delete();
    }
}
