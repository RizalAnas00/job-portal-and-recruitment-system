<?php

namespace App\Http\Controllers;

use App\Models\Notification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class NotificationController extends Controller
{
    /**
     * Display a listing of notifications for the current user.
     */
    public function index()
    {
        $user = Auth::user();
        $query = Notification::with(['jobSeeker.user', 'company'])->latest();

        // Filter notifikasi berdasarkan role
        if ($user->hasRole('user')) {
            // Job seeker melihat notifikasi mereka
            $jobSeekerId = $user->jobSeeker?->id;
            if ($jobSeekerId) {
                $query->where('id_job_seeker', $jobSeekerId);
            } else {
                $query->whereRaw('1 = 0'); // Tidak ada notifikasi
            }
        } elseif ($user->hasRole('company')) {
            // Company melihat notifikasi mereka
            $companyId = $user->company?->id;
            if ($companyId) {
                $query->where('id_company', $companyId);
            } else {
                $query->whereRaw('1 = 0'); // Tidak ada notifikasi
            }
        } elseif ($user->hasRole('admin')) {
            // Admin bisa melihat semua notifikasi
            // Tidak perlu filter
        }

        $notifications = $query->paginate(15);
        
        // Get unread count for the current page efficiently
        $unreadCount = $notifications->where('is_read', false)->count();

        return view('notifications.index', compact('notifications', 'unreadCount'));
    }

    /**
     * Mark a notification as read.
     */
    public function markAsRead(Notification $notification)
    {
        $user = Auth::user();

        // Otorisasi: pastikan notifikasi milik user
        $canUpdate = false;
        if ($user->hasRole('user') && $notification->id_job_seeker === $user->jobSeeker?->id) {
            $canUpdate = true;
        } elseif ($user->hasRole('company') && $notification->id_company === $user->company?->id) {
            $canUpdate = true;
        } elseif ($user->hasRole('admin')) {
            $canUpdate = true;
        }

        if (!$canUpdate) {
            abort(403, 'AKSES DITOLAK');
        }

        $notification->update(['is_read' => true]);

        if ($notification->link_url) {
            return redirect($notification->link_url);
        }

        return redirect()->route('notifications.index')->with('success', 'Notifikasi ditandai sebagai sudah dibaca.');
    }

    /**
     * Mark all notifications as read.
     */
    public function markAllAsRead()
    {
        $user = Auth::user();
        
        // Admin tidak memiliki notifikasi personal
        if ($user->hasRole('admin')) {
            return redirect()->route('notifications.index')
                ->with('error', 'Admin tidak dapat menandai semua notifikasi.');
        }
        
        $query = Notification::where('is_read', false);

        if ($user->hasRole('user')) {
            $jobSeekerId = $user->jobSeeker?->id;
            if ($jobSeekerId) {
                $query->where('id_job_seeker', $jobSeekerId);
            } else {
                return redirect()->route('notifications.index')
                    ->with('error', 'Tidak ada notifikasi untuk ditandai.');
            }
        } elseif ($user->hasRole('company')) {
            $companyId = $user->company?->id;
            if ($companyId) {
                $query->where('id_company', $companyId);
            } else {
                return redirect()->route('notifications.index')
                    ->with('error', 'Tidak ada notifikasi untuk ditandai.');
            }
        }

        $query->update(['is_read' => true]);

        return redirect()->route('notifications.index')->with('success', 'Semua notifikasi telah ditandai sebagai sudah dibaca.');
    }

    /**
     * Get unread notification count for the current user.
     */
    public function getUnreadCount()
    {
        $user = Auth::user();
        
        // Admin tidak memiliki notifikasi personal
        if ($user->hasRole('admin')) {
            return response()->json(['count' => 0]);
        }
        
        $query = Notification::where('is_read', false);

        if ($user->hasRole('user')) {
            $jobSeekerId = $user->jobSeeker?->id;
            if ($jobSeekerId) {
                $query->where('id_job_seeker', $jobSeekerId);
            } else {
                return response()->json(['count' => 0]);
            }
        } elseif ($user->hasRole('company')) {
            $companyId = $user->company?->id;
            if ($companyId) {
                $query->where('id_company', $companyId);
            } else {
                return response()->json(['count' => 0]);
            }
        }

        $count = $query->count();

        return response()->json(['count' => $count]);
    }
}

