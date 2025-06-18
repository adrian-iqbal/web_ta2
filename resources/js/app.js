import './bootstrap';
document.addEventListener('DOMContentLoaded', () => {
    const cartContainer = document.querySelector('.cart-items');
    const totalDisplay = document.querySelector('.total-display');

    let cart = [];

    function renderCart() {
        cartContainer.innerHTML = '';
        let total = 0;

        cart.forEach(item => {
            total += item.harga * item.jumlah;

            const cartItem = document.createElement('div');
            cartItem.className = 'flex items-center justify-between bg-white p-3 rounded-lg mb-3 shadow';

            cartItem.innerHTML = `
                <div class="flex items-center gap-3">
                    <img src="${item.gambar}" class="w-10 h-10 rounded" />
                    <div>
                        <p class="text-sm font-medium">${item.nama}</p>
                        <p class="text-xs text-gray-500">Rp ${item.harga.toLocaleString()}</p>
                    </div>
                </div>
                <div class="flex items-center gap-2">
                    <button class="decrement px-2 py-1 rounded bg-gray-200 text-lg font-bold" data-id="${item.id}">-</button>
                    <div class="bg-gray-300 px-3 py-1 rounded text-sm font-bold">${item.jumlah}</div>
                    <button class="increment px-2 py-1 rounded bg-gray-200 text-lg font-bold" data-id="${item.id}">+</button>
                </div>
            `;

            cartContainer.appendChild(cartItem);
        });

        totalDisplay.textContent = `Rp ${total.toLocaleString()}`;
    }

    document.addEventListener('click', (e) => {
        if (e.target.closest('.add-to-cart')) {
            const el = e.target.closest('.add-to-cart');
            const id = el.dataset.id;
            const nama = el.dataset.nama;
            const harga = parseFloat(el.dataset.harga);
            const gambar = el.dataset.gambar;

            const existing = cart.find(i => i.id === id);
            if (existing) {
                existing.jumlah++;
            } else {
                cart.push({ id, nama, harga, jumlah: 1, gambar });
            }
            renderCart();
        }

        if (e.target.classList.contains('increment')) {
            const id = e.target.dataset.id;
            const item = cart.find(i => i.id === id);
            if (item) item.jumlah++;
            renderCart();
        }

        if (e.target.classList.contains('decrement')) {
            const id = e.target.dataset.id;
            const item = cart.find(i => i.id === id);
            if (item) {
                item.jumlah--;
                if (item.jumlah <= 0) {
                    cart = cart.filter(i => i.id !== id);
                }
                renderCart();
            }
        }
    });
});
