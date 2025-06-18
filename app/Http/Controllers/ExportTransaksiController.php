<?php

namespace App\Http\Controllers;

use App\Models\Transaksi;
use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade\Pdf;

class ExportTransaksiController extends Controller
{
    public function export($id)
    {
        $transaksi = Transaksi::with('transaksiItems.barang')->findOrFail($id);
        $pdf = Pdf::loadView('pdf.transaksi', compact('transaksi'));

        return $pdf->download("transaksi-{$transaksi->no_transaksi}.pdf");
    }
}
