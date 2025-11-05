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
        
        $query = Notification::latest();

        // Filter notifikasi berdasarkan role
        if ($user->hasRole('user')) {
            // Job seeker melihat notifikasi mereka (where id_job_seeker is set)
            $jobSeekerId = $user->jobSeeker?->id;
            if ($jobSeekerId) {
                $query->where('id_job_seeker', $jobSeekerId)
                      ->whereNull('id_company'); // Notifications TO job seekers
            } else {
                $query->whereRaw('1 = 0'); // Tidak ada notifikasi
            }
        } elseif ($user->hasRole('company')) {
            // Company melihat notifikasi mereka (where id_company is set and id_job_seeker is null)
            $companyId = $user->company?->id;
            if ($companyId) {
                $query->where('id_company', $companyId)
                      ->whereNull('id_job_seeker'); // Notifications TO companies
            } else {
                $query->whereRaw('1 = 0'); // Tidak ada notifikasi
            }
        } elseif ($user->hasRole('admin')) {
            // Admin bisa melihat semua notifikasi
            // Tidak perlu filter
        }

        // Get total unread count BEFORE pagination
        $unreadCount = (clone $query)->where('is_read', false)->count();

        $notifications = $query->paginate(15);

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
        
        if ($user->hasRole('user') && $notification->id_job_seeker === $user->jobSeeker?->id && is_null($notification->id_company)) {
            // Job seeker owns this notification (id_job_seeker is set, id_company is null)
            $canUpdate = true;
        } elseif ($user->hasRole('company') && $notification->id_company === $user->company?->id && is_null($notification->id_job_seeker)) {
            // Company owns this notification (id_company is set, id_job_seeker is null)
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
        
        $query = Notification::where('is_read', false);

        if ($user->hasRole('user')) {
            $jobSeekerId = $user->jobSeeker?->id;
            if ($jobSeekerId) {
                $query->where('id_job_seeker', $jobSeekerId)
                      ->whereNull('id_company');
            } else {
                return redirect()->route('notifications.index')
                    ->with('error', 'Tidak ada notifikasi untuk ditandai.');
            }
        } elseif ($user->hasRole('company')) {
            $companyId = $user->company?->id;
            if ($companyId) {
                $query->where('id_company', $companyId)
                      ->whereNull('id_job_seeker');
            } else {
                return redirect()->route('notifications.index')
                    ->with('error', 'Tidak ada notifikasi untuk ditandai.');
            }
        } elseif ($user->hasRole('admin')) {
            // Admin marks all as read
        } else {
            return redirect()->route('dashboard')
                ->with('error', 'Tidak dapat menandai notifikasi.');
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
        
        $query = Notification::where('is_read', false);

        if ($user->hasRole('user')) {
            $jobSeekerId = $user->jobSeeker?->id;
            if ($jobSeekerId) {
                $query->where('id_job_seeker', $jobSeekerId)
                      ->whereNull('id_company');
            } else {
                return response()->json(['count' => 0]);
            }
        } elseif ($user->hasRole('company')) {
            $companyId = $user->company?->id;
            if ($companyId) {
                $query->where('id_company', $companyId)
                      ->whereNull('id_job_seeker');
            } else {
                return response()->json(['count' => 0]);
            }
        } elseif ($user->hasRole('admin')) {
            // Admin sees all unread
        } else {
            return response()->json(['count' => 0]);
        }
        
        $count = $query->count();

        return response()->json(['count' => $count]);
    }
}

