<!DOCTYPE html>
<html>
<head>
    <title>POS</title>

    <script>
        tailwind.config = {
            darkMode: 'class'
        }
    </script>
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js" defer></script>
</head>

<body class="bg-gray-100 overflow-hidden h-screen">

<div
    x-data="posApp(
        {{ json_encode($products ?? []) }},
        {{ json_encode($categories ?? []) }}
    )"
    class="flex h-screen relative"
    x-init="$watch('isCartMinimized', value => {
        if (value) {
            $nextTick(() => {
                // Auto scroll ke item terbaru saat minimize
                scrollToBottom();
            });
        }
    })"
>

    <!-- LEFT PRODUCT -->
    <div class="flex-1 flex flex-col bg-white shadow-lg border-r border-gray-200" :class="{ 'flex-[2]': isCartMinimized }">
        <!-- SEARCH & CART BUTTON -->
        <div class="p-4 border-b border-gray-100 flex flex-col sm:flex-row gap-2">
            <!-- SEARCH -->
            <div class="relative flex-1">
                <svg class="absolute left-4 top-1/2 transform -translate-y-1/2 w-5 h-5 text-gray-400" fill="none" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                </svg>
                <input
                    type="text"
                    class="w-full h-12 pl-12 pr-4 bg-gray-50 rounded-xl border border-gray-200 focus:border-blue-400 focus:ring-2 focus:ring-blue-100"
                    placeholder="Cari produk..."
                    x-model="keyword"
                >
            </div>

            <!-- FILTER CATEGORY -->
            <select
                x-model="selectedCategory"
                class="h-12 px-4 rounded-xl border border-gray-200 bg-white shadow-sm focus:ring-2 focus:ring-blue-100 focus:border-blue-400 text-sm font-medium flex-1 sm:w-auto sm:flex-none"
                @change="console.log('Selected category:', $event.target.value)"
            >
                <option value="">📦 Semua Kategori</option>
                <template x-for="cat in categories" :key="cat.id">
                    <option :value="cat.id" x-text="`${cat.nama} (${cat.count || 0})`"></option>
                </template>
            </select>

            <!-- CART TOGGLE BUTTON -->
            <button
                @click="toggleCart()"
                class="h-12 w-12 bg-gradient-to-r from-green-500 to-emerald-600 hover:from-green-600 hover:to-emerald-700 text-white rounded-xl shadow-lg hover:shadow-xl transition-all duration-200 flex items-center justify-center flex-shrink-0 relative"
                :title="cart.length + ' item | Rp ' + format(subtotal())"
            >
                <!-- Icon Cart -->
                <svg x-show="!isCartMinimized" class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 5H10v13a1 1 0 01-1 1H6a1 1 0 01-1-1V5H4a2 2 0 00-2 2v11a3 3 0 003 3h10a3 3 0 003-3V7a2 2 0 00-2-2z"></path>
                </svg>
                <svg x-show="isCartMinimized" class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 8V4m0 0h4M4 4l5 5m11-1V4m0 0h-4m4 0l-5 5M4 16v4m0 0h4m-4 0l5-5m11 5l-5-5m5 5v-4m0 4h-4"></path>
                </svg>

                <!-- Badge jumlah item DI DALAM ICON -->
                <div
                    class="absolute -top-1 -right-1 w-5 h-5 bg-red-500 text-white text-xs font-bold rounded-full flex items-center justify-center shadow-lg ring-2 ring-white"
                    x-show="cart.length > 0"
                    x-text="cart.length"
                ></div>
            </button>
        </div>

        <!-- PRODUCTS -->
        <div class="flex-1 overflow-auto p-4" x-ref="productsScroll">
            <!-- GRID PRODUCTS -->
            <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-4 xl:grid-cols-5 gap-3 mb-6">
                <template x-for="product in paginatedProducts()" :key="product.id">
                    <div
                        class="bg-white rounded-xl shadow-sm hover:shadow-md hover:bg-blue-50 border border-gray-100 cursor-pointer transition-all duration-200 hover:-translate-y-0.5 group"
                        @click="addToCart(product)"
                    >
                        <div class="h-28 sm:h-32 bg-gradient-to-br from-gray-100 to-gray-200 rounded-t-xl overflow-hidden relative">
                            <img
                                :src="product.image"
                                class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-300"
                                onerror="this.src='/images/no-image.png'"
                            >
                            <div class="absolute inset-0 bg-blue-500 bg-opacity-0 group-hover:bg-opacity-10 transition-all flex items-center justify-center">
                                <svg class="w-8 h-8 text-blue-500 opacity-0 group-hover:opacity-100 transition-all" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4m0 0L7 13m0 0l-1.5 7 9.5-3.5M7 13l1.5-7"></path>
                                </svg>
                            </div>
                        </div>

                        <div class="p-3">
                            <div class="flex items-start justify-between mb-2">
                                <div class="text-sm font-medium text-gray-800 truncate flex-1" x-text="product.nama"></div>
                                <div :class="'ml-2 px-2 py-1 rounded-full text-xs font-semibold flex-shrink-0 ' + (product.stock < product.minimum_stock ? 'bg-red-100 text-red-800' : 'bg-green-100 text-green-800')">
                                    <span x-text="format(product.stock)"></span>
                                </div>
                            </div>
                            <div class="font-bold text-lg text-green-600">
                                Rp <span x-text="format(product.harga_jual)"></span>
                            </div>
                        </div>
                    </div>
                </template>

                <div x-show="!paginatedProducts().length" class="col-span-full flex flex-col items-center justify-center py-12 text-gray-400">
                    <svg class="w-16 h-16 mb-3 opacity-50" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"></path>
                    </svg>
                    <p class="text-lg font-medium">Produk tidak ditemukan</p>
                </div>
            </div>

            <!-- PAGINATION CONTROLS - STICKY BOTTOM -->
            <div class="pt-4 border-t border-gray-100 bg-white/80 backdrop-blur-sm sticky bottom-0">
                <div class="flex flex-col sm:flex-row gap-4 items-center justify-between p-4 bg-white rounded-xl shadow-sm border">
                    <!-- LIMIT SELECTOR -->
                    <div class="flex items-center gap-2 text-sm text-gray-600 flex-wrap">
                        <span>Tampilkan:</span>
                        <select
                            x-model="limit"
                            @change="currentPage = 1"
                            class="px-3 py-2 rounded-lg border border-gray-200 focus:ring-2 focus:ring-blue-100 focus:border-blue-400 bg-white text-sm shadow-sm"
                        >
                            <option value="15" selected>15</option>
                            <option value="50">50</option>
                            <option value="1000">Semua</option>
                        </select>
                        <span class="font-medium" x-text="`dari ${filteredProducts().length} produk`"></span>
                    </div>

                    <!-- PAGINATION BUTTONS -->
                    <div class="flex items-center gap-2">
                        <button
                            @click="currentPage = 1"
                            :disabled="currentPage <= 1"
                            class="p-2 text-gray-500 hover:text-blue-600 disabled:opacity-50 disabled:cursor-not-allowed rounded-lg hover:bg-blue-50 transition-all flex items-center gap-1 text-sm"
                        >
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path>
                            </svg>
                            Prev
                        </button>

                        <span class="px-4 py-2 bg-gray-100 border rounded-lg text-sm font-semibold text-gray-800 min-w-[80px] text-center">
                            <span x-text="currentPage"></span> /
                            <span x-text="totalPages() || 1"></span>
                        </span>

                        <button
                            @click="currentPage = totalPages()"
                            :disabled="currentPage >= totalPages()"
                            class="p-2 text-gray-500 hover:text-blue-600 disabled:opacity-50 disabled:cursor-not-allowed rounded-lg hover:bg-blue-50 transition-all flex items-center gap-1 text-sm"
                        >
                            Next
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                            </svg>
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- RIGHT CART - MINIMIZE FEATURE -->
    <div
        class="w-[380px] bg-white shadow-2xl border-l border-gray-200 flex flex-col transition-all duration-300"
        :class="{
            'w-[380px] shadow-2xl translate-x-0': !isCartMinimized,
            'w-0 shadow-none -translate-x-full overflow-hidden': isCartMinimized
        }"
    >
        <!-- HEADER WITH CLEAR BUTTON -->
        <div class="p-4 border-b border-gray-100 bg-gradient-to-r from-green-500 to-emerald-500 text-white rounded-t-xl flex items-center justify-between">
            <div class="flex items-center gap-3">
                <div class="w-12 h-12 bg-white/20 rounded-xl flex items-center justify-center">
                    <svg class="w-6 h-6" fill="currentColor" viewBox="0 0 20 20">
                        <path d="M3 1a1 1 0 000 2h1.22l.305 1.808a.5.5 0 00.49.392h9.79c.42 0 .773-.272.92-.639l.541-3.184a.5.5 0 00-.92-.382L13.36 6H6.64L4.94 2H4a1 1 0 000-2zm0 4a1 1 0 000 2h1.22l.305 1.808a.5.5 0 00.49.392h9.79c.42 0 .773-.272.92-.639l.541-3.184a.5.5 0 00-.92-.382L13.36 10H6.64L4.94 6H4a1 1 0 000-2zM3 11a1 1 0 100 2h8a1 1 0 100-2H3zm9 2a1 1 0 100 2h2a1 1 0 100-2H12z"/>
                    </svg>
                </div>
                <div>
                    <h2 class="text-xl font-bold">Keranjang Belanja</h2>
                    <div class="text-sm opacity-90" x-text="cart.length + ' item | Rp ' + format(subtotal())"></div>
                </div>
            </div>

            <!-- CLEAR BUTTON ONLY -->
            <div class="flex items-center gap-1">
                <button @click="cart = []" class="p-2 hover:bg-white/20 rounded-lg transition-all" x-show="cart.length">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                    </svg>
                </button>
            </div>
        </div>

        <!-- ITEMS -->
        <div class="flex-1 overflow-auto p-4 space-y-3" x-ref="cartScroll" x-show="!isCartMinimized">
            <template x-for="item in cart" :key="item.id">
                <div class="flex items-start gap-3 p-3 bg-gray-50/70 hover:bg-gray-100 rounded-xl border border-gray-200 transition-all group/item">
                    <!-- Image -->
                    <div class="w-14 h-14 bg-gradient-to-br from-gray-200 rounded-lg overflow-hidden flex-shrink-0 mt-0.5">
                        <img :src="item.image" class="w-full h-full object-cover" onerror="this.src='/images/no-image.png'">
                    </div>

                    <!-- Product Info & Quantity -->
                    <div class="flex-1 min-w-0">
                        <div class="font-semibold text-sm text-gray-800 truncate mb-1" x-text="item.nama"></div>
                        <div class="text-xs text-gray-500 mb-2">Rp <span x-text="format(item.harga_jual)"></span></div>
                        <div class="flex items-center gap-2">
                            <div class="flex bg-white px-2 py-1 rounded-full border shadow-sm">
                                <button
                                    @click="qty(item, -1)"
                                    class="w-7 h-7 flex items-center justify-center text-gray-600 hover:text-red-500 hover:bg-red-50 rounded transition-all"
                                    :disabled="item.qty <= 1"
                                >−</button>
                                <span class="w-6 text-center font-bold text-sm mx-1" x-text="item.qty"></span>
                                <button
                                    @click="qty(item, 1)"
                                    class="w-7 h-7 flex items-center justify-center text-gray-600 hover:text-green-500 hover:bg-green-50 rounded transition-all"
                                >+</button>
                            </div>
                        </div>
                    </div>

                    <!-- Price & Delete Button -->
                    <div class="flex flex-col items-end gap-1 min-w-[65px]">
                        <div class="text-right font-bold text-green-600 text-sm" x-text="format(item.qty * item.harga_jual)"></div>
                        <button
                            @click="removeFromCart(item)"
                            class="w-7 h-7 flex items-center justify-center text-gray-400 hover:text-red-500 hover:bg-red-50 rounded-lg transition-all group/item-hover"
                            title="Hapus item"
                        >
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                            </svg>
                        </button>
                    </div>
                </div>
            </template>

            <div x-show="!cart.length && !isCartMinimized" class="flex flex-col items-center justify-center py-12 text-gray-400 text-center">
                <svg class="w-16 h-16 mb-3 opacity-40" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M3 3h2l.4 2M7 13h10l4-8H5.4m0 0L7 13m0 0l-1.5 9 9.5-3.5M7 13l1.5-9"></path>
                </svg>
                <div class="font-medium text-lg mb-1">Keranjang kosong</div>
                <div class="text-sm">Klik produk untuk menambahkan</div>
            </div>
        </div>

        <!-- SUMMARY - Hanya tampil saat tidak diminimalkan -->
        <div x-show="!isCartMinimized" class="p-4 border-t border-gray-200 bg-gradient-to-t from-gray-50 rounded-b-xl space-y-3">
            <div class="space-y-2 text-sm">
                <div class="flex justify-between">
                    <span class="text-gray-600">Subtotal</span>
                    <span class="font-semibold">Rp <span x-text="format(subtotal())"></span></span>
                </div>
                <div class="flex justify-between items-center p-2 bg-white rounded-lg border">
                    <span class="text-gray-600">Diskon</span>
                    <div class="flex items-center gap-1">
                        <input type="number" x-model.number="discount" class="w-20 text-right p-1 border-none bg-transparent focus:ring-0 font-semibold text-sm">
                        <span class="text-xs text-gray-500">Rp</span>
                    </div>
                </div>
                <div class="flex justify-between items-center p-2 bg-white rounded-lg border">
                    <span class="text-gray-600">Pajak</span>
                    <div class="flex items-center gap-1">
                        <input type="number" x-model.number="tax" class="w-16 text-right p-1 border-none bg-transparent focus:ring-0 font-semibold text-sm">
                        <span class="text-xs text-gray-500">%</span>
                    </div>
                </div>
                <!-- JUMLAH PPN -->
                <div class="flex justify-between pl-2 pr-12 text-xs text-gray-500">
                    <span>Total Pajak:</span>
                    <span class="font-medium text-green-600" x-text="format(subtotal() * (tax / 100))"></span>
                </div>
            </div>

            <div class="pt-3 border-t">
                <div class="flex justify-between text-xl font-bold mb-4 text-gray-800">
                    <span>Total</span>
                    <span class="text-green-600 text-2xl">Rp <span x-text="format(total())"></span></span>
                </div>

                <button
                    class="w-full bg-gradient-to-r from-green-500 to-emerald-600 hover:from-green-600 hover:to-emerald-700 text-white font-bold py-3 px-4 rounded-xl shadow-lg hover:shadow-xl transition-all duration-200 flex items-center justify-center gap-2 text-sm"
                    @click="showPaymentModal = true"
                    :disabled="!cart.length"
                >
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"></path>
                    </svg>
                    PROSES PEMBAYARAN
                </button>
            </div>
        </div>
    </div>
    <!-- PAYMENT MODAL -->
<div
    x-show="showPaymentModal"
    x-transition:enter="transition ease-out duration-300"
    x-transition:enter-start="opacity-0"
    x-transition:enter-end="opacity-100"
    x-transition:leave="transition ease-in duration-200"
    x-transition:leave-start="opacity-100"
    x-transition:leave-end="opacity-0"
    class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50 p-4"
    @click.away="showPaymentModal = false"
    @keydown.escape.window="showPaymentModal = false"
>
    <div class="bg-white rounded-2xl shadow-2xl w-full max-w-2xl max-h-[90vh] overflow-y-auto" @click.stop>
        <!-- HEADER MODAL -->
        <div class="p-6 border-b border-gray-200 bg-gradient-to-r from-green-500 to-emerald-500 text-white rounded-t-2xl">
            <div class="flex items-center justify-between">
                <h2 class="text-2xl font-bold">Proses Pembayaran</h2>
                <button @click="showPaymentModal = false" class="p-2 hover:bg-white/20 rounded-xl transition-all">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                    </svg>
                </button>
            </div>
        </div>

        <!-- BODY MODAL -->
        <div class="p-6 space-y-6">
            <!-- TOTAL FINAL -->
            <div class="bg-gradient-to-r from-gray-50 to-blue-50 p-6 rounded-2xl border-2 border-green-200">
                <div class="flex justify-between items-center mb-2">
                    <span class="text-lg font-semibold text-gray-800">Total Harus Dibayar</span>
                </div>
                <div class="text-3xl font-bold text-green-600 flex items-baseline">
                    Rp <span x-text="format(total())"></span>
                </div>
            </div>

            <!-- FORM INPUT -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- CUSTOMER & TANGGAL -->
                <div class="space-y-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Customer</label>
                        <input
                            type="text"
                            x-model="selectedCustomer"
                            placeholder="Nama customer (opsional)"
                            class="w-full px-4 py-3 border border-gray-200 rounded-xl focus:ring-2 focus:ring-blue-200 focus:border-blue-400"
                        >
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Tanggal</label>
                        <input
                            type="date"
                            x-model="paymentDate"
                            class="w-full px-4 py-3 border border-gray-200 rounded-xl focus:ring-2 focus:ring-blue-200 focus:border-blue-400"
                        >
                    </div>
                </div>

                <!-- METODE BAYAR & NOMINAL -->
                <div class="space-y-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Metode Pembayaran</label>
                        <select
                            x-model="paymentMethod"
                            class="w-full px-4 py-3 border border-gray-200 rounded-xl focus:ring-2 focus:ring-blue-200 focus:border-blue-400"
                        >
                            <option value="tunai">💵 Tunai</option>
                            <option value="debit">💳 Debit</option>
                            <option value="kredit">💳 Kredit</option>
                            <option value="transfer">🏦 Transfer</option>
                            <option value="qris">📱 QRIS</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Nominal Bayar</label>
                        <div class="flex gap-2">
                            <input
                                type="number"
                                x-model.number="nominalBayar"
                                min="0"
                                class="flex-1 px-4 py-3 border border-gray-200 rounded-xl focus:ring-2 focus:ring-blue-200 focus:border-blue-400 text-right font-semibold"
                                placeholder="0"
                            >
                            <div class="flex flex-col gap-1">
                                <template x-for="option in pembayaranOptions" :key="option">
                                    <button
                                        @click="nominalBayar = total()"
                                        x-show="option === 'uang pas'"
                                        class="w-12 h-10 bg-green-500 hover:bg-green-600 text-white rounded-lg font-bold text-sm transition-all shadow-sm"
                                        title="Uang Pas"
                                    >
                                        Pas
                                    </button>
                                    <button
                                        @click="nominalBayar += 10000"
                                        x-show="option === '10rb'"
                                        class="w-12 h-10 bg-blue-500 hover:bg-blue-600 text-white rounded-lg font-bold text-xs transition-all shadow-sm"
                                        title="10 Ribu"
                                    >
                                        10K
                                    </button>
                                    <button
                                        @click="nominalBayar += 20000"
                                        x-show="option === '20rb'"
                                        class="w-12 h-10 bg-blue-500 hover:bg-blue-600 text-white rounded-lg font-bold text-xs transition-all shadow-sm"
                                        title="20 Ribu"
                                    >
                                        20K
                                    </button>
                                    <button
                                        @click="nominalBayar += 50000"
                                        x-show="option === '50rb'"
                                        class="w-12 h-10 bg-blue-500 hover:bg-blue-600 text-white rounded-lg font-bold text-xs transition-all shadow-sm"
                                        title="50 Ribu"
                                    >
                                        50K
                                    </button>
                                    <button
                                        @click="nominalBayar += 100000"
                                        x-show="option === '100rb'"
                                        class="w-12 h-10 bg-blue-500 hover:bg-blue-600 text-white rounded-lg font-bold text-xs transition-all shadow-sm"
                                        title="100 Ribu"
                                    >
                                        100K
                                    </button>
                                </template>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- KEMBALIAN -->
            <div class="p-4 bg-gradient-to-r from-yellow-50 to-orange-50 border-2 border-yellow-200 rounded-2xl">
                <div class="flex justify-between items-center">
                    <div>
                        <div class="font-semibold text-lg text-gray-800">Kembalian</div>
                        <div class="text-sm text-gray-600" x-show="paymentMethod === 'tunai'">
                            (Hanya untuk pembayaran tunai)
                        </div>
                    </div>
                    <div :class="{
                        'text-2xl font-bold text-green-600': kembalian() >= 0,
                        'text-2xl font-bold text-red-600 animate-pulse': kembalian() < 0
                    }">
                        <span x-show="kembalian() >= 0">Rp <span x-text="format(Math.abs(kembalian()))"></span></span>
                        <span x-show="kembalian() < 0">
                            <span>Rp <span x-text="format(Math.abs(kembalian()))"></span></span>
                            <span class="ml-2 text-sm bg-red-100 text-red-800 px-2 py-1 rounded-full font-bold">KURANG</span>
                        </span>
                    </div>
                </div>
            </div>
        </div>

        <!-- FOOTER MODAL -->
        <div class="p-6 border-t border-gray-200 bg-gray-50 rounded-b-2xl flex gap-3">
            <button
                @click="showPaymentModal = false"
                class="flex-1 bg-gray-200 hover:bg-gray-300 text-gray-800 font-semibold py-4 px-6 rounded-xl transition-all"
            >
                Batal
            </button>
            <button
                @click="processPayment()"
                :disabled="nominalBayar < total() || !cart.length"
                :class="{
                    'bg-green-500 hover:bg-green-600 flex-1 text-white font-bold py-4 px-6 rounded-xl shadow-lg hover:shadow-xl transition-all flex items-center justify-center gap-2':
                        nominalBayar >= total() && cart.length,
                    'bg-gray-400 cursor-not-allowed flex-1 text-white font-bold py-4 px-6 rounded-xl flex items-center justify-center gap-2':
                        nominalBayar < total() || !cart.length
                }"
            >
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                </svg>
                <span>Bayar Sekarang</span>
            </button>
        </div>
    </div>
</div>
</div>

<script>
function posApp(products, categories) {
    return {
        products,
        categories,
        cart: [],
        keyword: '',
        selectedCategory: '',
        discount: 0,
        tax: 0,
        isCartMinimized: false,
        currentPage: 1,
        limit: 15,
        newItemsCount: 0,
        showPaymentModal: false,
        selectedCustomer: '',
        paymentDate: new Date().toISOString().split('T')[0],
        paymentMethod: 'tunai',
        nominalBayar: 0,
        pembayaranOptions: ['uang pas', '10rb', '20rb', '50rb', '100rb'],
        kembalian() {
            return this.nominalBayar - this.total();
        },
        processPayment() {
            if (this.nominalBayar < this.total()) {
                alert('Uang tidak cukup!');
                return;
            }

            const data = {
                cart: this.cart,
                total: this.total(),
                bayar: this.nominalBayar,
                kembalian: this.kembalian(),
                metode: this.paymentMethod,
                customer: this.selectedCustomer,
                tanggal: this.paymentDate,
            };

            console.log('TRANSAKSI:', data);

            // 🔥 nanti bisa kirim ke backend pakai fetch / axios
            // contoh:
            /*
            fetch('/api/checkout', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                },
                body: JSON.stringify(data)
            });
            */

            // reset
            this.cart = [];
            this.nominalBayar = 0;
            this.selectedCustomer = '';
            this.paymentMethod = 'tunai';
            this.showPaymentModal = false;

            alert('Pembayaran berhasil!');
        },

        init() {
            console.log('Products:', products);
            console.log('Categories:', categories);
            console.log('Sample product kategori_id:', products[0]?.kategori_id);

            this.$watch('cart', (newCart, oldCart) => {
                const oldLength = oldCart ? oldCart.length : 0;
                const newLength = newCart.length;

                if (newLength > oldLength) {
                    this.$nextTick(() => {
                        this.scrollToBottom();
                    });
                }
            });
        },

        toggleCart() {
            this.isCartMinimized = !this.isCartMinimized;
        },

        filteredProducts() {
            return this.products.filter(p => {
                const matchKeyword = !this.keyword ||
                    p.nama.toLowerCase().includes(this.keyword.toLowerCase().trim());

                const matchCategory = !this.selectedCategory ||
                    Number(p.category_id) === Number(this.selectedCategory);

                return matchKeyword && matchCategory;
            });
        },
        paginatedProducts() {
            const start = (this.currentPage - 1) * this.limit;
            const end = start + this.limit;
            return this.filteredProducts().slice(start, end);
        },

        totalPages() {
            return Math.ceil(this.filteredProducts().length / this.limit);
        },

        addToCart(product) {
            let item = this.cart.find(i => i.id === product.id);
            if (item) {
                item.qty++;
            } else {
                this.cart.push({...product, qty: 1});
            }
        },

        qty(item, change) {
            item.qty += change;
            if (item.qty <= 0) {
                this.cart = this.cart.filter(i => i.id !== item.id);
            }
        },

        removeFromCart(item) {
            this.cart = this.cart.filter(i => i.id !== item.id);
        },

        scrollToBottom() {
            const cartScroll = this.$refs.cartScroll;
            if (cartScroll) {
                cartScroll.scrollTop = cartScroll.scrollHeight;
            }
        },

        subtotal() {
            return this.cart.reduce((total, item) => total + (item.qty * item.harga_jual), 0);
        },

        total() {
            const sub = this.subtotal();
            const taxAmount = sub * (this.tax / 100);
            return sub + taxAmount - this.discount;
        },

        format(value) {
            return new Intl.NumberFormat('id-ID').format(Math.round(value));
        }
    }
}
</script>

</body>
</html>
