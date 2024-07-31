<?php

namespace App\Http\Controllers;
use App\Models\User;
use App\Models\offre;
use Illuminate\Http\Request;
use App\Models\inscription_offre;
use Illuminate\Support\Facades\Log;

class inscription_offrecontroller extends Controller
{
    public function index()
    {
        $inscriptions = inscription_offre::with('user.client', 'offre')->whereHas('offre')->get();
        return response()->json($inscriptions);
        
    }
    public function show(string $id){
        $utilisateur = User::findOrFail($id)->with(['client', 'offres'])->whereHas('offres')->get();
        return response()->json($utilisateur);
        
    }
    public function store(Request $request){
        $validateinscription_offre = $request->validate([	
            "paiement"=>"required",
            "users_id"=>"required",
            "offres_id" =>"required",   
        ]);
        $create_inscription_offre=inscription_offre::Create($validateinscription_offre);
        return response()->json([$create_inscription_offre,"message"=>"offre added successefuly"]);

    }
    public function destroy(string $id)
    {
        $inscription_offre = inscription_offre::findOrFail($id);
        $inscription_offre->delete();
    }
    public function TotalOffres(){
        $inscription_offres = inscription_offre::count();
        return response()->json($inscription_offres);
    }
    public function ChartinsOffers(){
        $inscriptions = inscription_offre::all();
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
    public function LastsOffers(){
        $offre = inscription_offre::with(['user.client', 'offre'])
        ->whereHas('offre')
        ->orderBy('created_at', 'desc')
        ->take(10)
        ->get();
        return response()->json($offre);
    }
}
