<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">
    <title>Laporan Transaksi</title>
    <style>
        body {
            font-family: sans-serif;
            font-size: 12px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 1em;
        }

        th,
        td {
            border: 1px solid #444;
            padding: 4px;
        }

        th {
            background: #eee;
        }
    </style>
</head>

<body>
    <h2 style="text-align: center;">Laporan Transaksi UD Rizky</h2>
    <h3 style="text-align: center;">Jl. Raya Medan No.26 KM 23, Tj. Baru, Kec. Tj. Morawa, Kabupaten Deli Serdang,
        Sumatera Utara 20551
        Telepon: (061) 7943575</h3>
    <table>
        <thead>
            <tr>
                <th>No. Transaksi</th>
                <th>Tanggal</th>
                <th>Items</th>
                <th>Total</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($transaksis as $trx)
                <tr>
                    <td>{{ $trx->no_transaksi }}</td>
                    <td>{{ \Carbon\Carbon::parse($trx->tanggal)->format('d-m-Y H:i') }}</td>
                    <td>
                        <ul style="padding-left: 1em; margin: 0;">
                            @foreach ($trx->transaksiItems as $item)
                                <li>
                                    {{ $item->barang->nama_barang }}
                                    ({{ $item->quantity }} ×
                                    Rp{{ number_format($item->harga, 0, ',', '.') }})
                                </li>
                            @endforeach
                        </ul>
                    </td>
                    <td style="text-align: right;">
                        Rp{{ number_format($trx->total, 0, ',', '.') }}
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>
    <p>Dicetak pada: {{ \Carbon\Carbon::now()->format('d-m-Y H:i') }}</p>
</body>

</html>
