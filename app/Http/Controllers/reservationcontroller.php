<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Personal;
use App\Models\reservation;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class reservationcontroller extends Controller
{
    public function index()
    {
        
        $utilisateurs = reservation::with(['user.client','personal'])->whereHas('personal')->get();
      
        return response()->json($utilisateurs);

        
    }
    public function store(Request $request)
    {
        $validatedreservation = $request->validate([
            'paiement'=>'required',
            'id_user' =>'required',
            'id_personal_trainies'=>'required',
               
        ]);

        Log::info($validatedreservation);
        $create_reservation=reservation::Create($validatedreservation);
        return response()->json([$create_reservation,"message"=>"reservation added successefully"]);
        //
    }
   

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {

        $utilisateur = User::findOrFail($id)->with(['client', 'personals'])->whereHas('personals')->get();
        return response()->json($utilisateur);
        //
    }
    /**
     * Update the
     *  specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $validatedreservation = $request->validate([
            "paiement"=>'required',
            "id_user" =>'redquired',
            "id_personal_trainies"=>"required", 
        ]);
        $reservation = reservation::findOrFail($id);
        $reservation->update($validatedreservation);
        return response()->json([$reservation,"message"=>"reservation added successfuly"]);
        
    }
    

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $reservation = reservation::findOrFail($id);
        $reservation->delete();
    }
    public function TrainiesCount(){
        $trainies = reservation::count();
        return response()->json($trainies);
    }
    public function Chartreservation(){
        $inscriptions = reservation::all();
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
    public function LastReservations(){
        $reservation = reservation::with(['user.client', 'personal'])
        ->whereHas('personal')
        ->orderBy('created_at', 'desc')
        ->take(10)
        ->get();
        return response()->json($reservation);
    }
    }

