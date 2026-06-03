<?php

namespace App\Helpers;

use Illuminate\Support\Facades\DB;

class CleanGoHelper
{
    public static function rupiah($angka): string
    {
        return 'Rp ' . number_format((float)$angka, 0, ',', '.');
    }

    public static function badgeStatus(string $status): string
    {
        return match($status) {
            'Menunggu Konfirmasi' => 'badge-warning',
            'Dijemput'           => 'badge-blue',
            'Dicuci'             => 'badge-process',
            'Disetrika'          => 'badge-purple',
            'Dikirim'            => 'badge-cyan',
            'Selesai'            => 'badge-success',
            'Dibatalkan'         => 'badge-danger',
            'Lunas'              => 'badge-success',
            'Pending'            => 'badge-warning',
            'Menunggu Konfirmasi'=> 'badge-yellow',
            'Aktif'              => 'badge-success',
            'Nonaktif'           => 'badge-danger',
            default              => 'badge-default',
        };
    }

    public static function generateKodeOrder(): string
    {
        $today = now()->format('Ymd');
        $count = DB::table('orders')->whereDate('tanggal_pesan', today())->count() + 1;
        return 'ORD-' . $today . '-' . str_pad($count, 3, '0', STR_PAD_LEFT);
    }

    public static function generateNoInvoice(): string
    {
        $today = now()->format('Ymd');
        $count = DB::table('invoice')->whereDate('tgl_invoice', today())->count() + 1;
        return 'INV-' . $today . '-' . str_pad($count, 3, '0', STR_PAD_LEFT);
    }

    public static function sendNotification(string $role, int $actorId, string $title, string $message, string $link = ''): void
    {
        DB::table('notifications')->insert([
            'role'       => $role,
            'actor_id'   => $actorId,
            'title'      => $title,
            'message'    => $message,
            'link'       => $link,
            'created_at' => now(),
        ]);
    }

    public static function notifyAllStaff(string $title, string $message, string $link = ''): void
    {
        $staffs = DB::table('staff')->where('is_active', 1)->get();
        foreach ($staffs as $s) {
            self::sendNotification('staff', $s->id_staff, $title, $message, $link);
        }
    }

    public static function notifyAllOwner(string $title, string $message, string $link = ''): void
    {
        $owners = DB::table('owner')->where('is_active', 1)->get();
        foreach ($owners as $o) {
            self::sendNotification('owner', $o->id_owner, $title, $message, $link);
        }
    }

    public static function countUnread(string $role, int $actorId): int
    {
        return DB::table('notifications')
            ->where('role', $role)
            ->where('actor_id', $actorId)
            ->where('is_read', 0)
            ->count();
    }

    public static function getNotifications(string $role, int $actorId, int $limit = 20): array
    {
        return DB::table('notifications')
            ->where('role', $role)
            ->where('actor_id', $actorId)
            ->orderByDesc('created_at')
            ->limit($limit)
            ->get()
            ->toArray();
    }

    public static function markAllRead(string $role, int $actorId): void
    {
        DB::table('notifications')
            ->where('role', $role)
            ->where('actor_id', $actorId)
            ->update(['is_read' => 1]);
    }
}
