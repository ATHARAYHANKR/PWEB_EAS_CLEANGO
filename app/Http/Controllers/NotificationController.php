<?php

namespace App\Http\Controllers;

use App\Helpers\CleanGoHelper as CG;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;

class NotificationController extends Controller
{
    public function get()
    {
        $role    = Session::get('role');
        $actorId = Session::get('user_id');

        return response()->json([
            'unread'        => CG::countUnread($role, $actorId),
            'notifications' => CG::getNotifications($role, $actorId, 20),
        ]);
    }

    public function markRead(Request $request)
    {
        $role    = Session::get('role');
        $actorId = Session::get('user_id');
        CG::markAllRead($role, $actorId);
        return response()->json(['success' => true]);
    }
}
