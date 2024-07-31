<?php
namespace App\Http\Controllers;
use App\Models\Personal;
use Illuminate\Http\Request;
use Exception;
use Illuminate\Validation\ValidationException;
class personal_trainiescontroller extends Controller
{
    public function index()
    {
        $personal_trainies=Personal::all();
        return response()->json($personal_trainies);

        //
    }
    public function store(Request $request)
    {
        $validatedpersonal_trainies = $request->validate([
            'nom_personal_tr' => 'required',
            'description' => 'required',
            'prix' => 'required|numeric',
        ],[
            'required' => 'This field is required.',
            'string' => 'This field must be a string.',
            'numeric' => 'This field must be a number.',

        ]);  
        $create_personal_trainies=Personal::Create($validatedpersonal_trainies);
        return response()->json([
            $create_personal_trainies,
            'message' => 'personal training added successfully'
    ]);
    
    }
   
    public function show(string $id)
    {
        $personal_trainies = Personal::findOrFail($id);
        return response()->json($personal_trainies);
        //
    }

    public function update(Request $request, string $id)
    {
        try{
            $validatedpersonal_trainies = $request->validate([
                'nom_personal_tr' => 'required',
                'description' => 'required',
                'prix' => 'required|numeric',
             
            ],[
                'required' => 'This field is required.',
                'string' => 'This field must be a string.',
                'numeric' => 'This field must be a number.',

            ]);
            $personal_trainies = Personal::findOrFail($id);
            $personal_trainies->update($validatedpersonal_trainies);
            return response()->json([
                $personal_trainies,
                'message' => 'Personal training updated successfuly'
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
        $personal_trainies = Personal::findOrFail($id);
        $personal_trainies->delete();
    }

}
