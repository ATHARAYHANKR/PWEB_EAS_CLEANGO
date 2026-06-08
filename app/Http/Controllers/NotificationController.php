<?php

namespace App\Http\Controllers;

use App\Helpers\CleanGoHelper as CG;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;

class NotificationController extends Controller
{
    public function get()
    {
        // Ambil role dan user_id dari session untuk menentukan notifikasi mana yang tampil
        $role    = Session::get('role');
        $actorId = Session::get('user_id');

        // Response JSON dipakai oleh frontend AJAX/JS untuk menampilkan badge notifikasi
        return response()->json([
            'unread'        => CG::countUnread($role, $actorId),
            'notifications' => CG::getNotifications($role, $actorId, 20),
        ]);
    }

    public function markRead(Request $request)
    {
        // Tandai semua notifikasi milik actor ini sebagai sudah dibaca
        $role    = Session::get('role');
        $actorId = Session::get('user_id');
        CG::markAllRead($role, $actorId);

        // Response JSON sederhana untuk AJAX sukses
        return response()->json(['success' => true]);
    }
}
