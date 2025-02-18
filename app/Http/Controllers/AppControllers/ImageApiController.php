<?php

namespace App\Http\Controllers\AppControllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File; 
use Illuminate\Support\Facades\Log;

class ImageApiController extends Controller
{
    public function update(Request $request)
    {
        $validatedData = $request->validate([
            'Folder' => 'required|string|max:255',
            'pictures.*' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        if ($request->hasFile('pictures')) {
            $uploadedImages = [];

            foreach ($request->file('pictures') as $image) {
                $imageName = time() . '_' . $image->getClientOriginalName();
                $imagePath = 'uploads/' . $request->Folder . '/images/' . $imageName;
                $image->move(public_path('uploads/' . $request->Folder . '/images/'), $imageName);
                $uploadedImages[] = $imagePath;
            }

            return response()->json([
                'status' => 'success',
                'message' => 'Images uploaded successfully',
                'paths' => $uploadedImages,
            ], 200);
        } else {
            return response()->json([
                'status' => 'error',
                'message' => 'No picture available to upload',
            ], 400);
        }
    }

    public function destroyImage(Request $request)
    {
        $path = $request->input('path');
        $oldImagePath = public_path($path);

        if (!File::exists($oldImagePath)) {
            return response()->json(['success' => false, 'message' => 'File not found.']);
        }

        try {
            File::delete($oldImagePath);
            return response()->json(['success' => true, 'message' => 'File deleted successfully.']);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Error deleting file.'], 500);
        }
    }

    
}