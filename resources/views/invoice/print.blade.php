<!DOCTYPE html>
<html lang="id">
{{-- Halaman export untuk print invoice. Menampilkan tombol download PDF dan kembali. --}}
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cetak Invoice {{ $invoice->no_invoice }}</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    @vite(['resources/css/app.css'])
    <style>
        body { background: #f8fafc; }
        .print-only { display: none; }
        @media print {
            body { background: white; }
            .no-print { display: none !important; }
            .print-only { display: block !important; }
            .invoice-card { box-shadow: none !important; }
        }
    </style>
</head>
<body class="min-h-screen px-4 py-6">
    <div class="max-w-3xl mx-auto bg-white rounded-3xl shadow-xl invoice-card overflow-hidden">
        <div class="p-6 border-b border-slate-200">
            <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4">
                <div>
                    <div class="text-sm uppercase tracking-[0.25em] text-slate-500 font-semibold">Nota / Struk</div>
                    <div class="text-2xl font-bold text-slate-900 mt-2">{{ $invoice->no_invoice }}</div>
                </div>
                <div class="text-right space-y-3">
                    <div>
                        <div class="text-sm text-slate-500">Tanggal</div>
                        <div class="text-base font-semibold">{{ \Carbon\Carbon::parse($invoice->tgl_invoice)->translatedFormat('d F Y H:i') }}</div>
                    </div>
                    <div class="flex flex-wrap justify-end gap-2 no-print">
                        <a href="{{ $downloadRoute }}" class="inline-flex items-center gap-2 px-4 py-2 rounded-2xl bg-slate-700 text-white text-sm font-semibold hover:bg-slate-600 transition">
                            <i class="fas fa-file-download"></i> Download PDF
                        </a>
                        <a href="{{ $backRoute }}" class="inline-flex items-center gap-2 px-4 py-2 rounded-2xl border border-slate-200 text-slate-700 text-sm font-semibold hover:bg-slate-100 transition">
                            <i class="fas fa-arrow-left"></i> Kembali
                        </a>
                    </div>
                </div>
            </div>
        </div>

        <div class="p-6 grid grid-cols-1 md:grid-cols-3 gap-6 border-b border-slate-200 text-sm text-slate-700">
            <div>
                <div class="text-slate-500 uppercase tracking-[0.2em] mb-2">Detail Order</div>
                <div class="font-semibold">{{ $invoice->kode_order }}</div>
                <div>{{ $invoice->nama_layanan }}</div>
                <div class="mt-2 text-slate-500">Status order: {{ $invoice->status_order ?? '-' }}</div>
            </div>
            <div>
                <div class="text-slate-500 uppercase tracking-[0.2em] mb-2">Pembayaran</div>
                <div class="font-semibold">{{ $invoice->metode }}</div>
                <div class="mt-2">{{ $invoice->status_bayar ?? '-' }}</div>
            </div>
            <div>
                <div class="text-slate-500 uppercase tracking-[0.2em] mb-2">Kustomer</div>
                <div class="font-semibold">{{ $invoice->nama_cust }}</div>
                <div>{{ $invoice->notelp_cust }}</div>
            </div>
        </div>

        <div class="p-6 grid grid-cols-1 md:grid-cols-3 gap-6 text-sm text-slate-700">
            <div>
                <div class="text-slate-500 uppercase tracking-[0.2em] mb-2">Berat / Qty</div>
                <div>{{ data_get($invoice, 'berat') ? data_get($invoice, 'berat') . ' kg' : (data_get($invoice, 'qty') ? data_get($invoice, 'qty') . ' pcs' : '-') }}</div>
            </div>
            <div>
                <div class="text-slate-500 uppercase tracking-[0.2em] mb-2">Tanggal pesan</div>
                <div>{{ \Carbon\Carbon::parse($invoice->tanggal_pesan)->translatedFormat('d F Y') }}</div>
            </div>
            <div>
                <div class="text-slate-500 uppercase tracking-[0.2em] mb-2">Jumlah</div>
                <div class="text-xl font-bold text-emerald-600">{{ \App\Helpers\CleanGoHelper::rupiah($invoice->jumlah) }}</div>
            </div>
        </div>

        <div class="p-6 border-t border-slate-200 bg-slate-50">
            <div class="text-slate-500 text-xs">Cetak struk ini untuk bukti pembayaran Anda.</div>
        </div>
    </div>
</body>
</html>
