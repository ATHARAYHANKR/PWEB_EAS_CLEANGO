<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Laporan Bulanan CleanGo</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }

        body {
            font-family: 'DejaVu Sans', Arial, sans-serif;
            font-size: 11px;
            color: #1e293b;
            background: #fff;
        }

        /* ── HEADER ── */
        .header {
            background: linear-gradient(135deg, #7c3aed 0%, #a855f7 100%);
            color: #fff;
            padding: 20px 28px;
            margin-bottom: 20px;
        }
        .header-inner {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
        }
        .brand { font-size: 22px; font-weight: 700; letter-spacing: -0.5px; }
        .brand span { color: #e9d5ff; }
        .tagline { font-size: 10px; color: #ddd6fe; margin-top: 2px; }
        .report-title { text-align: right; }
        .report-title h2 { font-size: 15px; font-weight: 700; }
        .report-title p  { font-size: 10px; color: #ddd6fe; margin-top: 3px; }

        /* ── META INFO ── */
        .meta-box {
            margin: 0 28px 18px;
            background: #f5f3ff;
            border-left: 4px solid #7c3aed;
            padding: 10px 14px;
            border-radius: 4px;
            display: flex;
            gap: 40px;
        }
        .meta-box .item label { font-size: 9px; color: #6b7280; text-transform: uppercase; letter-spacing: .5px; }
        .meta-box .item p     { font-size: 11px; font-weight: 600; color: #1e293b; margin-top: 1px; }

        /* ── SUMMARY CARDS ── */
        .summary {
            margin: 0 28px 18px;
            display: flex;
            gap: 10px;
        }
        .card {
            flex: 1;
            background: #fff;
            border: 1px solid #e2e8f0;
            border-radius: 8px;
            padding: 10px 12px;
            text-align: center;
        }
        .card .value { font-size: 16px; font-weight: 700; color: #7c3aed; }
        .card .label { font-size: 9px; color: #64748b; margin-top: 2px; text-transform: uppercase; letter-spacing: .4px; }

        /* ── TABLE ── */
        .table-wrap { margin: 0 28px 20px; }
        table { width: 100%; border-collapse: collapse; }
        thead tr { background: #7c3aed; color: #fff; }
        thead th { padding: 8px 10px; text-align: left; font-size: 10px; font-weight: 600; letter-spacing: .3px; }
        thead th:last-child { text-align: right; }
        tbody tr:nth-child(even) { background: #f5f3ff; }
        tbody tr:hover           { background: #ede9fe; }
        tbody td { padding: 7px 10px; font-size: 10px; border-bottom: 1px solid #e2e8f0; }
        tbody td:last-child { text-align: right; font-weight: 600; color: #7c3aed; }
        tfoot tr { background: #1e293b; color: #fff; }
        tfoot td { padding: 8px 10px; font-size: 10px; font-weight: 700; }
        tfoot td:last-child { text-align: right; }

        .badge {
            display: inline-block;
            padding: 2px 7px;
            border-radius: 999px;
            font-size: 9px;
            font-weight: 600;
        }
        .badge-green  { background: #dcfce7; color: #16a34a; }
        .badge-orange { background: #fff7ed; color: #ea580c; }

        /* ── BAR CHART COLUMN ── */
        .bar-wrap { width: 80px; display: inline-block; vertical-align: middle; }
        .bar-bg    { background: #e2e8f0; border-radius: 4px; height: 6px; overflow: hidden; }
        .bar-fill  { background: linear-gradient(90deg, #7c3aed, #a855f7); height: 6px; border-radius: 4px; }

        /* ── FOOTER ── */
        .footer {
            margin: 0 28px;
            padding-top: 12px;
            border-top: 1px solid #e2e8f0;
            display: flex;
            justify-content: space-between;
            font-size: 9px;
            color: #94a3b8;
        }

        /* page break */
        @page { margin: 16px 0; }
    </style>
</head>
<body>

{{-- HEADER --}}
<div class="header">
    <div class="header-inner">
        <div>
            <div class="brand">🧺 Clean<span>Go</span></div>
            <div class="tagline">Laundry Management System</div>
        </div>
        <div class="report-title">
            <h2>Laporan Bulanan</h2>
            <p>Dicetak: {{ \Carbon\Carbon::now()->isoFormat('D MMMM YYYY, HH:mm') }}</p>
        </div>
    </div>
</div>

{{-- META INFO --}}
<div class="meta-box">
    <div class="item">
        <label>Periode Data</label>
        <p>
            @if($laporanBulan->count())
                {{ $laporanBulan->last()->bulan }} s/d {{ $laporanBulan->first()->bulan }}
            @else
                —
            @endif
        </p>
    </div>
    <div class="item">
        <label>Total Bulan</label>
        <p>{{ $laporanBulan->count() }} bulan</p>
    </div>
    <div class="item">
        <label>Dicetak oleh</label>
        <p>{{ $ownerName }}</p>
    </div>
</div>

{{-- SUMMARY CARDS --}}
@php
    $totalOrder  = $laporanBulan->sum('total_order');
    $totalSelesai= $laporanBulan->sum('selesai');
    $totalOmzet  = $laporanBulan->sum('total_omzet');
    $maxOmzet    = $laporanBulan->max('total_omzet') ?: 1;
@endphp
<div class="summary">
    <div class="card">
        <div class="value">{{ number_format($totalOrder) }}</div>
        <div class="label">Total Order</div>
    </div>
    <div class="card">
        <div class="value">{{ number_format($totalSelesai) }}</div>
        <div class="label">Order Selesai</div>
    </div>
    <div class="card">
        <div class="value">{{ $totalOrder > 0 ? round($totalSelesai/$totalOrder*100) : 0 }}%</div>
        <div class="label">Completion Rate</div>
    </div>
    <div class="card">
        <div class="value" style="font-size:13px;">Rp {{ number_format($totalOmzet,0,',','.') }}</div>
        <div class="label">Total Omzet</div>
    </div>
</div>

{{-- TABLE --}}
<div class="table-wrap">
    <table>
        <thead>
            <tr>
                <th>No</th>
                <th>Bulan</th>
                <th>Total Order</th>
                <th>Selesai</th>
                <th>Proses/Batal</th>
                <th>Grafik</th>
                <th>Total Omzet</th>
            </tr>
        </thead>
        <tbody>
            @forelse($laporanBulan as $i => $row)
            @php
                $batal = $row->total_order - $row->selesai;
                $pct   = $maxOmzet > 0 ? round($row->total_omzet / $maxOmzet * 100) : 0;
            @endphp
            <tr>
                <td>{{ $i + 1 }}</td>
                <td><strong>{{ $row->bulan }}</strong></td>
                <td>{{ $row->total_order }}</td>
                <td>
                    <span class="badge badge-green">✓ {{ $row->selesai }}</span>
                </td>
                <td>
                    @if($batal > 0)
                    <span class="badge badge-orange">{{ $batal }}</span>
                    @else
                    <span style="color:#94a3b8">—</span>
                    @endif
                </td>
                <td>
                    <div class="bar-wrap">
                        <div class="bar-bg">
                            <div class="bar-fill" style="width: {{ $pct }}%"></div>
                        </div>
                        <small style="font-size:8px;color:#94a3b8;">{{ $pct }}%</small>
                    </div>
                </td>
                <td>Rp {{ number_format($row->total_omzet, 0, ',', '.') }}</td>
            </tr>
            @empty
            <tr>
                <td colspan="7" style="text-align:center;padding:20px;color:#94a3b8;">Belum ada data laporan</td>
            </tr>
            @endforelse
        </tbody>
        @if($laporanBulan->count())
        <tfoot>
            <tr>
                <td colspan="2">TOTAL</td>
                <td>{{ number_format($totalOrder) }}</td>
                <td>{{ number_format($totalSelesai) }}</td>
                <td>{{ number_format($totalOrder - $totalSelesai) }}</td>
                <td></td>
                <td>Rp {{ number_format($totalOmzet, 0, ',', '.') }}</td>
            </tr>
        </tfoot>
        @endif
    </table>
</div>

{{-- FOOTER --}}
<div class="footer">
    <span>CleanGo Laundry Management System</span>
    <span>Dokumen ini dibuat otomatis oleh sistem — {{ \Carbon\Carbon::now()->isoFormat('D MMMM YYYY') }}</span>
</div>

</body>
</html>
