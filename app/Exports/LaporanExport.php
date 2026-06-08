<?php

namespace App\Exports;

use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithColumnWidths;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\Color;

class LaporanExport implements FromCollection, WithHeadings, WithTitle, WithStyles, ShouldAutoSize
{
    protected $bulan;
    protected $tahun;

    public function __construct($bulan = null, $tahun = null)
    {
        $this->bulan = $bulan;
        $this->tahun = $tahun;
    }

    public function collection()
    {
        $query = DB::table('orders as o')
            ->leftJoin('pembayaran as p', function($join) {
                $join->on('p.id_order', '=', 'o.id_order')
                     ->where('p.status_bayar', '=', 'Lunas');
            })
            ->selectRaw("
                DATE_FORMAT(o.tanggal_pesan,'%Y-%m') as Bulan,
                COUNT(*) as Total_Order,
                SUM(CASE WHEN o.status_order='Selesai' THEN 1 ELSE 0 END) as Order_Selesai,
                SUM(CASE WHEN o.status_order NOT IN ('Selesai','Dibatalkan') THEN 1 ELSE 0 END) as Order_Proses,
                SUM(CASE WHEN o.status_order='Dibatalkan' THEN 1 ELSE 0 END) as Order_Batal,
                COALESCE(SUM(p.jumlah),0) as Total_Omzet
            ")
            ->groupBy('Bulan')
            ->orderByDesc('Bulan');

        // Filter by month/year if provided
        if ($this->bulan && $this->tahun) {
            $query->whereRaw("DATE_FORMAT(tanggal_pesan,'%Y-%m') = ?", [$this->tahun . '-' . str_pad($this->bulan, 2, '0', STR_PAD_LEFT)]);
        } elseif ($this->tahun) {
            $query->whereRaw("YEAR(tanggal_pesan) = ?", [$this->tahun]);
        } else {
            $query->limit(12);
        }

        return $query->get()->map(function ($row) {
            return [
                'Bulan'         => $row->Bulan,
                'Total Order'   => $row->Total_Order,
                'Selesai'       => $row->Order_Selesai,
                'Proses'        => $row->Order_Proses,
                'Dibatalkan'    => $row->Order_Batal,
                'Total Omzet'   => 'Rp ' . number_format($row->Total_Omzet, 0, ',', '.'),
            ];
        });
    }

    public function headings(): array
    {
        return ['Bulan', 'Total Order', 'Order Selesai', 'Order Proses', 'Order Dibatalkan', 'Total Omzet (Rp)'];
    }

    public function title(): string
    {
        return 'Laporan Bulanan';
    }

    public function styles(Worksheet $sheet)
    {
        // Header row style
        $sheet->getStyle('A1:F1')->applyFromArray([
            'font' => [
                'bold'  => true,
                'color' => ['argb' => 'FFFFFFFF'],
            ],
            'fill' => [
                'fillType'   => Fill::FILL_SOLID,
                'startColor' => ['argb' => 'FF7C3AED'], // violet-600
            ],
            'alignment' => [
                'horizontal' => Alignment::HORIZONTAL_CENTER,
            ],
        ]);

        // Zebra stripe data rows
        $lastRow = $sheet->getHighestRow();
        for ($i = 2; $i <= $lastRow; $i++) {
            if ($i % 2 === 0) {
                $sheet->getStyle("A{$i}:F{$i}")->applyFromArray([
                    'fill' => [
                        'fillType'   => Fill::FILL_SOLID,
                        'startColor' => ['argb' => 'FFF5F3FF'], // violet-50
                    ],
                ]);
            }
            // Right-align omzet column
            $sheet->getStyle("F{$i}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_RIGHT);
        }

        return [];
    }
}
