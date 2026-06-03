<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class TrackingSeeder extends Seeder
{
    public function run(): void
    {
        // Get all orders that don't have tracking yet
        $ordersWithoutTracking = DB::table('orders as o')
            ->leftJoin('tracking as t', 't.id_order', '=', 'o.id_order')
            ->whereNull('t.id_tracking')
            ->select('o.id_order', 'o.status_order', 'o.tanggal_pesan')
            ->get();

        $statusLabels = [
            'Menunggu Konfirmasi' => 'Order masuk dari customer',
            'Dijemput' => 'Kurir menjemput barang dari customer',
            'Dicuci' => 'Proses pencucian barang',
            'Disetrika' => 'Proses penyetrikaan barang',
            'Dikirim' => 'Barang dalam pengiriman ke customer',
            'Selesai' => 'Order selesai diterima customer',
            'Dibatalkan' => 'Order dibatalkan',
        ];

        $statusSequence = ['Menunggu Konfirmasi', 'Dijemput', 'Dicuci', 'Disetrika', 'Dikirim', 'Selesai'];

        foreach ($ordersWithoutTracking as $order) {
            $orderDate = \Carbon\Carbon::parse($order->tanggal_pesan);
            
            // Determine which statuses to create based on order's current status
            $statusesToCreate = [];
            $currentIndex = array_search($order->status_order, $statusSequence);
            
            if ($currentIndex === false) {
                if ($order->status_order === 'Dibatalkan') {
                    $statusesToCreate = ['Menunggu Konfirmasi', 'Dibatalkan'];
                } else {
                    $statusesToCreate = ['Menunggu Konfirmasi'];
                }
            } else {
                // Get all statuses up to and including current status
                $statusesToCreate = array_slice($statusSequence, 0, $currentIndex + 1);
            }

            // Create tracking for each status
            $trackingTime = $orderDate->copy();
            foreach ($statusesToCreate as $status) {
                // Add some time between each status update (30 minutes to 2 hours)
                $trackingTime->addMinutes(rand(30, 120));

                DB::table('tracking')->insert([
                    'id_order' => $order->id_order,
                    'status' => $status,
                    'keterangan' => $statusLabels[$status] ?? $status,
                    'waktu_update' => $trackingTime,
                    'updated_by' => null,
                ]);
            }
        }
    }
}
