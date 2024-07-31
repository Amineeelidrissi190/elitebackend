<?php

namespace App\Http\Controllers;

use App\Models\event;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class eventcontroller extends Controller
{
    public function index()
    {
        $event=event::all();
        return response()->json($event);

        //
    }
    public function store(Request $request)
    {
        // Validation des données de la requête
        $request->validate([
            'nom_event' => 'required|string',
            'description_event' => 'required|string',
            'date_event' => 'required|date',
            'image_Event' => 'required|image',
        ], [
            // Messages d'erreur personnalisés
            'required' => 'This field is required.',
            'string' => 'This field must be a string.',
            'date' => 'This field must be a valid date.',
            'image' => 'This field must be an image.',


        ]);
    
        // Télécharger et stocker l'image
        $image_EventName = $request->file('image_Event')->getClientOriginalName();
        $request->file('image_Event')->storeAs('public/photos/Event', $image_EventName);
    
        // Créer un nouvel événement
        event::create([
            'nom_event' => $request->nom_event,
            'description_event' => $request->description_event,
            'date_event' => $request->date_event,
            'image_Event' => $image_EventName,
        ]);
    
        // Réponse JSON en cas de succès
        return response()->json([
            'message' => 'Event added successfuly'
        ]);
    }
   

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $event = event::findOrFail($id);
        return response()->json($event);
        //
    }
    /**
     * Update the
     *  specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $validatedEvent = $request->validate([
            'nom_event' => 'required',
            'description_event' => 'required',
            'date_event' => 'required',
            'image_Event' => 'nullable|image'
        ],[
            // Messages d'erreur personnalisés
            'required' => 'This field is required.',
            'string' => 'This field must be a string.',
            'date' => 'This field must be a valid date.',
            'image' => 'This field must be an image.',
        ]);
    
        $event = Event::findOrFail($id);
        $event->update($validatedEvent);
    
        if ($request->hasFile('image_Event')) {
            // Supprimer l'image existante
            $existingImage = $event->image_Event;
            if ($existingImage) {
                Storage::disk('public')->delete("photos/Event/{$existingImage}");
            }
    
            // Enregistrer la nouvelle image
            $image_EventName = $request->file('image_Event')->getClientOriginalName();
            $request->file('image_Event')->storeAs('photos/Event', $image_EventName, 'public');
            $event->image_Event = $image_EventName;
            $event->save();
        }
    
        return response()->json([
            'message' => 'Event updated successfully'
        ]);
    }
    
    

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $event = event::findOrFail($id);

        if ($event->image_event) {
            $exists = Storage::disk('public')->exists("photos/Event/{$event->image_event}");
    
            if ($exists) {
                Storage::disk('public')->delete("photos/Event/{$event->image_Event}");
            }
        }
    
        $event->delete();
    
        return response()->json([
            'message' => 'Event deleted successfully'
        ]);
    
    }
}
