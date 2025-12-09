<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AuditLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class SystemSettingsController extends Controller
{
    public function __construct()
    {
        $this->middleware('role:admin');
    }

    /**
     * Display the system settings page.
     */
    public function index()
    {
        $settings = [
            'app_name' => config('app.name'),
            'app_url' => config('app.url'),
            'max_file_size' => ini_get('upload_max_filesize'),
            'queue_connection' => config('queue.default'),
            'cache_driver' => config('cache.default'),
            'session_driver' => config('session.driver'),
        ];

        return view('admin.settings.index', compact('settings'));
    }

    /**
     * Update system settings.
     */
    public function update(Request $request)
    {
        // Note: In a real application, you would use a settings package or database table
        // For now, this is a placeholder that shows the structure
        
        $validated = $request->validate([
            'app_name' => 'required|string|max:255',
            'max_upload_size' => 'nullable|integer|min:1',
            // Add more settings as needed
        ]);

        // Log the action
        AuditLog::log(
            'settings.updated',
            null,
            "System settings updated by admin"
        );

        return back()->with('success', 'Pengaturan sistem berhasil diperbarui.');
    }

    /**
     * Display file management page.
     */
    public function files(Request $request)
    {
        $path = $request->get('path', 'public');
        $files = Storage::files($path);
        $directories = Storage::directories($path);

        return view('admin.settings.files', compact('files', 'directories', 'path'));
    }

    /**
     * Delete a file.
     */
    public function deleteFile(Request $request)
    {
        $validated = $request->validate([
            'file_path' => 'required|string',
        ]);

        if (Storage::exists($validated['file_path'])) {
            Storage::delete($validated['file_path']);

            AuditLog::log(
                'file.deleted',
                null,
                "File deleted: {$validated['file_path']}"
            );

            return back()->with('success', 'File berhasil dihapus.');
        }

        return back()->with('error', 'File tidak ditemukan.');
    }
}
