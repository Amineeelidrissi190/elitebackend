<?php

namespace App\Http\Controllers;
use App\Models\event;
use Illuminate\Http\Request;
use App\Models\inscription_event;
use Illuminate\Support\Facades\Log;

class inscription_event_controller extends Controller
{
    public function index(){
        $inscription_events = inscription_event::with(['user.client', 'events'])
        ->whereHas('events')
        ->get();
        return response()->json($inscription_events);
    }


    public function LastsEvents(){
        $events = inscription_event::with(['user.client', 'events'])
        ->whereHas('events')
        ->orderBy('created_at', 'desc')
        ->take(10)
        ->get();
        return response()->json($events);
    }


    public function CreatedDates()
    {
        $inscriptions = inscription_event::all();
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
    
    
    
    public function show($id){
        $inscription_event = event::findOrFail($id)->with("user.client")->get();
        return response()->json($inscription_event);

    }




    public function store(Request $request){	
        $validateinscription_event = $request->validate([	
            "paiement"=>"required",
            "id_user"=>"required",
            "id_event" =>"required",   
        ]);
        $create_inscription_event=inscription_event::Create($validateinscription_event);
        Log::info($create_inscription_event);
        return response()->json($create_inscription_event);

    }


    public function destroy(string $id)
    {
        $inscription_event = inscription_event::findOrFail($id);
        $inscription_event->delete();
    }
    public function CountLists(){
        $inscriptionEvent = inscription_event::count();
        return response()->json($inscriptionEvent);
    }
    //
}
