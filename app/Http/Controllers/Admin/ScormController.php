<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Madnest\Madzipper\Madzipper;
use App\Models\Scorm;
use Illuminate\Http\Exceptions\PostTooLargeException;
use Exception;
use Illuminate\Validation\ValidationException;
use ZipArchive;
use Illuminate\Support\Facades\Log;


class ScormController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
        $scorms = Scorm::all();
        return view('admin.scorm.index', compact('scorms'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
        return view('admin.scorm.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    // public function store(Request $request)
    // {
    //     $request->validate([
    //         'name' => 'required|string|max:255',
    //         'file' => 'required|mimes:zip|max:10240'
    //     ]);

    //     $file = $request->file('file');
    //     $path = $file->storeAs('scorm', $file->getClientOriginalName());

    //     $zipper = new Madzipper;
    //     $zipper->make(storage_path("app/{$path}"))->extractTo(storage_path('app/scorm/' . pathinfo($path, PATHINFO_FILENAME)));

    //     $scorm = new Scorm;
    //     $scorm->name = $request->name;
    //     $scorm->path = 'scorm/' . pathinfo($path, PATHINFO_FILENAME);
    //     $scorm->save();

    //     return redirect()->route('admin.scorm.show', $scorm->id);
    // }
    public function store(Request $request)
    {
        try {
            $request->validate([
                'name' => 'required|string|max:255',
                'file' => 'required|mimes:zip|max:102400', // Adjust the maximum size as per your requirement (e.g., 100 MB)
            ]);

            $file = $request->file('file');

            // Check if the file was uploaded successfully
            if (!$file->isValid()) {
                throw new Exception('File upload failed.');
            }

            $fileName = $file->getClientOriginalName();
            $path = $file->storeAs('scorm', $fileName);

            $zipper = new Madzipper;
            $zipper->make(storage_path("app/{$path}"))->extractTo(storage_path('app/scorm/' . pathinfo($fileName, PATHINFO_FILENAME)));

            $scorm = new Scorm;
            $scorm->name = $request->name;
            $scorm->path = 'scorm/' . pathinfo($fileName, PATHINFO_FILENAME);

            // Save the Scorm model
            if (!$scorm->save()) {
                throw new Exception('Failed to save SCORM data.');
            }

            return redirect()->route('scorm.index')->with('success', 'SCORM package uploaded successfully.');
        } catch (ValidationException $exception) {
            // Handle validation errors
            return back()->withErrors($exception->errors())->withInput();
        } catch (Exception $exception) {
            // Handle other exceptions
            return back()->with('error', $exception->getMessage());
        }
    }

    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        $scorm = Scorm::findOrFail($id);
        $indexPath = storage_path('app/' . $scorm->path . '/index.html');

        // Check if the index file exists
        if (file_exists($indexPath)) {
            // Read the contents of the index file
            $content = file_get_contents($indexPath);
            // Return the contents as a view
            return view('admin.scorm.show', compact('content'));
        } else {
            // If index file not found, return an error message or redirect
            return back()->with('error', 'SCORM package not found.');
        }
    }

    // public function show($prefix, $id)
    // {
    //     Log::info("Entering show method with ID: $id");

    //     $scorm = Scorm::findOrFail($id);
    //     Log::info("SCORM package found: " . $scorm->path);

    //     $zipPath = storage_path('app/' . $scorm->path . '.zip'); // Path to the zip file
    //     Log::info("Zip path: " . $zipPath);

    //     // Check if the zip file exists
    //     if (!file_exists($zipPath)) {
    //         Log::error("Zip file not found: " . $zipPath);
    //         return back()->with('error', 'SCORM package not found.');
    //     }

    //     // Extract the zip file to a temporary directory
    //     $tempDir = storage_path('app/temp_scorm/' . uniqid('scorm_extract_', true));
    //     Log::info("Temporary extraction directory: " . $tempDir);

    //     if (!file_exists($tempDir)) {
    //         mkdir($tempDir, 0777, true);
    //     }

    //     $zip = new ZipArchive;
    //     if ($zip->open($zipPath) === true) {
    //         $zip->extractTo($tempDir);
    //         $zip->close();
    //         Log::info("Zip file extracted to: " . $tempDir);
    //     } else {
    //         Log::error("Failed to open zip file: " . $zipPath);
    //         return back()->with('error', 'Failed to extract SCORM package.');
    //     }

    //     // Check if index.html exists in the 'scormcontent' directory
    //     $indexPath = $tempDir . '/scormcontent/index.html';
    //     Log::info("Index path: " . $indexPath);

    //     if (!file_exists($indexPath)) {
    //         Log::error("Index file not found: " . $indexPath);
    //         $this->deleteDirectory($tempDir);
    //         return back()->with('error', 'Index file not found in SCORM package.');
    //     }

    //     // Read the contents of the index file
    //     $content = file_get_contents($indexPath);
    //     Log::info("Index file content read successfully.");

    //     // Return the contents as a view
    //     return view('admin.scorm.show', compact('content'));
    // }

    // Helper function to recursively delete a directory
    private function deleteDirectory($dir)
    {
        if (!file_exists($dir)) {
            return true;
        }

        if (!is_dir($dir)) {
            return unlink($dir);
        }

        foreach (scandir($dir) as $item) {
            if ($item == '.' || $item == '..') {
                continue;
            }

            if (!$this->deleteDirectory($dir . DIRECTORY_SEPARATOR . $item)) {
                return false;
            }
        }

        return rmdir($dir);
    }




    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
