<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Response;

Route::get('/', function () {
    return view('welcome');
});

// Route to serve weapon attachments from private storage
Route::get('/admin/attachments/{path}', function ($path) {
    try {
        // Check if user is authenticated
        if (!auth()->check()) {
            abort(403, 'Unauthorized');
        }
        
        $decodedPath = base64_decode($path);
        $disk = Storage::disk('local');
        
        if (!$disk->exists($decodedPath)) {
            abort(404, 'File not found');
        }
        
        $file = $disk->get($decodedPath);
        $mimeType = $disk->mimeType($decodedPath);
        
        return Response::make($file, 200, [
            'Content-Type' => $mimeType,
            'Content-Disposition' => 'inline; filename="' . basename($decodedPath) . '"',
        ]);
    } catch (\Exception $e) {
        abort(404, 'File not found');
    }
})->name('filament.admin.attachments.download')->middleware(['web', 'auth']);
