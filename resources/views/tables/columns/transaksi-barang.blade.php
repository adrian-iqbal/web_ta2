<ul class="space-y-1">
    @foreach ($getState() ?? [] as $item)
        <li class="border-b pb-1">
            <strong>{{ $item->barang->nama_barang ?? '-' }}</strong><br>
            Qty: {{ $item->quantity }} |
            Harga: Rp{{ number_format($item->harga, 0, ',', '.') }} |
            Subtotal: Rp{{ number_format($item->subtotal, 0, ',', '.') }}
        </li>
    @endforeach
</ul>
