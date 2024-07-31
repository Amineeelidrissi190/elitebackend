<?php

namespace App\Http\Controllers;

use App\Models\specialite;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\ValidationException;

class controllerspecialite extends Controller
{
    public function index()
    {
        $specialites = specialite::all();
        return response()->json($specialites);
    }

    public function store(Request $request)
    {
        try {
            $validatedData = $request->validate([
                'nom_specialité' => 'required',
                'video_intro' => 'required|mimetypes:video/avi,video/mpeg,video/quicktime,video/mp4', // Ensure video_intro is a file
                'price' => 'required|numeric',
                'description' => 'required',
                'emploi_sp' => 'required|image',
            ], [
                'required' => 'This field is required.',
                'string' => 'This field must be a string.',
                'image' => 'This field must be an image.',
                'numeric' => 'This field must be a number.',
                'file' => 'This field must be a file.',

            ]);
    
            $videoIntroFile = $request->file('video_intro');
            $emploiSpFile = $request->file('emploi_sp');
    
            if ($videoIntroFile && $emploiSpFile) {
                $videosp = $videoIntroFile->getClientOriginalName();
                $emp = $emploiSpFile->getClientOriginalName();
    
                Storage::disk('public')->putFileAs('videos/specialities', $videoIntroFile, $videosp);
                Storage::disk('public')->putFileAs('photos/specialities', $emploiSpFile, $emp);
    
                $specialiteData = $request->except(['video_intro', 'emploi_sp']);
                $specialiteData['video_intro'] = $videosp;
                $specialiteData['emploi_sp'] = $emp;
    
                // Log the specialiteData for debugging
                info($specialiteData);
    
                specialite::create($specialiteData);
    
                return response()->json([
                    'message' => 'Category added successfully'
                ]);
            } else {
                return response()->json([
                    'errors' => [
                        'video_intro' => ['Video intro file is required.'],
                        'emploi_sp' => ['Emploi specialite file is required.']
                    ]
                ], 400);
            }
        } catch (ValidationException $e) {
            return response()->json([
                'errors' => $e->errors(),
            ], 422);
        }
    }
    


    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        $specialite = specialite::findOrFail($id);
        return response()->json($specialite);
    }
    /**
     * Update the
     *  specified resource in storage.
     */
    public function update(Request $request, $id)
{
    // Validation rules
    $request->validate([
        'nom_specialité' => 'required',
        'description' => 'required',
        'price' => 'required',
        'emploi_sp' => 'nullable|image|mimes:jpeg,png,jpg,gif',
        'video_intro' => 'nullable|mimes:mp4', 
    ],[
        'required' => 'This field is required.',
        'string' => 'This field must be a string.',
        'image' => 'This field must be an image.',
        'numeric' => 'This field must be a number.',
        'file' => 'This field must be a file.',
    ]);

    $specialite = Specialite::findOrFail($id);

    // Create an array to hold the updated data
    $updatedData = [
        'nom_specialité' => $request->input('nom_specialité'),
        'description' => $request->input('description'),
        'price' => $request->input('price'),
    ];

    // Handle emploi_sp image file update if provided
    if ($request->hasFile('emploi_sp')) {
        $emploiSpFile = $request->file('emploi_sp');
        $emploiSpName = $emploiSpFile->getClientOriginalName();

        // Existing emploi_sp image handling code
        if ($specialite->emploi_sp) {
            Storage::disk('public')->delete("photos/specialities/{$specialite->emploi_sp}");
        }

        $emploiSpFile->storeAs('photos/specialities', $emploiSpName, 'public');
        $updatedData['emploi_sp'] = $emploiSpName;
    }
    if ($request->hasFile('video_intro')) {
        $videoIntroFile = $request->file('video_intro');
        $videoIntroName = $videoIntroFile->getClientOriginalName();

        // Existing video_intro handling code
        if ($specialite->video_intro) {
            Storage::disk('public')->delete("videos/specialities/{$specialite->video_intro}");
        }

        $videoIntroFile->storeAs('videos/specialities', $videoIntroName, 'public');
        $updatedData['video_intro'] = $videoIntroName;
    }

    // Update the specialite model with the updated data
    $specialite->update($updatedData);

    return response()->json([
        'message' => 'Category updated successfully',
        'specialite' => $specialite
    ]);
}

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        // Find the specialite by ID
        $specialite = specialite::findOrFail($id);

        // Delete the associated video_intro and emploi_sp files from storage
        $videoIntro = $specialite->video_intro;
        $emploiSp = $specialite->emploi_sp;

        if ($videoIntro) {
            Storage::disk('public')->delete("videos/specialities/{$videoIntro}");
        }

        if ($emploiSp) {
            Storage::disk('public')->delete("photos/specialities/{$emploiSp}");
        }

        // Delete the specialite record
        $specialite->delete();

        return response()->json([
            'message' => 'Speciality deleted successfully'
        ]);
    }
}
