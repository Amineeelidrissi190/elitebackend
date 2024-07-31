<?php
namespace App\Http\Controllers;

use App\Models\client; 
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log; 
use Illuminate\Validation\ValidationException;

class clientcontroller extends Controller
{
    public function index()
    {
        $clients = client::with('User')->get(); 
        return response()->json($clients);
    }

    public function store(Request $request)
    {
        Log::info($request);
        try{
            $validatedclient = $request->validate([
                'name_client' => 'required',
                'age_client' => 'required|numeric',
                'numero_tel' => 'required|numeric',
                'specialite' => 'required',
                'users_id' => 'required',
            ],[
                'required' => 'Le champ  est requis.',
                'string' => 'Le champ  doit être une chaîne de caractères.',
                'image' => 'Le champ  doit être une image.',
                'numeric' => 'Ce champ  doit être un nombre.',
            ]);
    
            $client = new client();
            $client->name_client = $validatedclient['name_client'];
            $client->age_client = $validatedclient['age_client'];
            $client->numero_tel = $validatedclient['numero_tel'];
            $client->specialite = $validatedclient['specialite'];
            $client->users_id = $validatedclient['users_id']; 
            $client->save();  
            return response()->json($client);

        }catch (ValidationException $e) {
            return response()->json([
                'errors' => $e->errors(),
            ], 422);
        } 
        

    }

    public function show(string $id)
    {
        $client = client::findOrFail($id);
        return response()->json($client);
    }

    public function update(Request $request, string $id)
    {
        $validatedclient = $request->validate([
            'name_client' => 'required',
            'age_client' => 'required',
            'numero_tel' => 'required',
            'specialite' => 'required', 
            'users_id' => 'required',
        ]);

        $client = client::findOrFail($id);
        $client->update($validatedclient);
        
        return response()->json([$client,'msg'=>'created successefuly']);
    }

    public function destroy(string $id)
    {
        $client = client::findOrFail($id);
        if ($client->user) {
            $client->user->delete();
        }
    
        $client->delete();
    
        return response()->json(['message' => 'Client and associated user deleted successfully'], 200);
    }
}
