<!DOCTYPE html>
<html lang="id">
{{-- Template PDF untuk mencetak invoice/nota. Digunakan oleh controller PDF generator. --}}
<head>
    <meta charset="UTF-8">
    <title>Nota {{ $invoice->no_invoice }}</title>
    <style>
        body { font-family: Arial, sans-serif; color: #111; margin: 0; padding: 24px; }
        .wrapper { max-width: 720px; margin: auto; }
        .header, .section, .footer { width: 100%; margin-bottom: 16px; }
        .header { display: flex; justify-content: space-between; align-items: flex-start; }
        .title { font-size: 18px; font-weight: bold; margin: 0 0 8px; }
        .meta { font-size: 12px; color: #555; }
        .box { border: 1px solid #ddd; border-radius: 10px; padding: 16px; }
        .grid { display: flex; flex-wrap: wrap; gap: 16px; }
        .grid > div { flex: 1 1 240px; min-width: 220px; }
        .label { font-size: 11px; text-transform: uppercase; letter-spacing: .06em; color: #777; margin-bottom: 6px; }
        .value { font-size: 13px; line-height: 1.5; }
        .table { width: 100%; border-collapse: collapse; margin-top: 16px; }
        .table th, .table td { border: 1px solid #ddd; padding: 8px; font-size: 12px; }
        .table th { background: #f5f5f5; text-align: left; }
        .text-right { text-align: right; }
        .strong { font-weight: bold; }
        .total-row td { border-top: 2px solid #000; }
    </style>
</head>
<body>
    <div class="wrapper">
        <div class="header">
            <div>
                <div class="label">Nota / Struk</div>
                <div class="title">{{ $invoice->no_invoice }}</div>
            </div>
            <div class="meta">
                Tanggal: {{ \Carbon\Carbon::parse($invoice->tgl_invoice)->translatedFormat('d F Y H:i') }}
            </div>
        </div>

        <div class="section box">
            <div class="grid">
                <div>
                    <div class="label">Customer</div>
                    <div class="value">
                        {{ $invoice->nama_cust }}<br>
                        {{ $invoice->notelp_cust }}
                    </div>
                </div>
                <div>
                    <div class="label">Order</div>
                    <div class="value">
                        {{ $invoice->kode_order }}<br>
                        {{ $invoice->nama_layanan }}<br>
                        Status: {{ $invoice->status_order ?? '-' }}
                    </div>
                </div>
                <div>
                    <div class="label">Pembayaran</div>
                    <div class="value">
                        {{ $invoice->metode }}<br>
                        {{ $invoice->status_bayar ?? '-' }}
                    </div>
                </div>
            </div>
        </div>

        <div class="section box">
            <div class="label">Detail Item</div>
            <table class="table">
                <thead>
                    <tr>
                        <th>Item</th>
                        <th>Berat/Qty</th>
                        <th>Harga Satuan</th>
                        <th>Subtotal</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($details as $detail)
                    <tr>
                        <td>{{ $detail->varian ?? 'Layanan' }}</td>
                        <td>
                            @if($detail->berat)
                                {{ $detail->berat }} kg
                            @elseif($detail->qty)
                                {{ $detail->qty }} pcs
                            @else
                                -
                            @endif
                        </td>
                        <td>{{ number_format($detail->harga_satuan,0,',','.') }}</td>
                        <td>{{ number_format($detail->subtotal,0,',','.') }}</td>
                    </tr>
                    @endforeach
                </tbody>
                <tfoot>
                    <tr class="total-row">
                        <td colspan="3" class="text-right strong">Total Dibayar</td>
                        <td class="strong">{{ number_format($invoice->jumlah,0,',','.') }}</td>
                    </tr>
                </tfoot>
            </table>
        </div>

        <div class="section box">
            <div class="grid">
                <div>
                    <div class="label">Tanggal Pesan</div>
                    <div class="value">{{ \Carbon\Carbon::parse($invoice->tanggal_pesan)->translatedFormat('d F Y') }}</div>
                </div>
                <div>
                    <div class="label">Dicetak</div>
                    <div class="value">{{ \Carbon\Carbon::now()->translatedFormat('d F Y H:i') }}</div>
                </div>
            </div>
        </div>
    </div>
</body>
</html>
