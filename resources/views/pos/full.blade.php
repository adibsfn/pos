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

<body class="bg-gray-100">

<div
    x-data="posApp(
        {{ json_encode($products ?? []) }},
        {{ json_encode($categories ?? []) }}
    )"
    class="flex h-screen"
>

    <!-- LEFT PRODUCT -->
        <div class="flex-1 flex flex-col bg-white shadow-lg border-r border-gray-200">

            <!-- SEARCH -->
            <div class="p-4 border-b border-gray-100">
                <div class="relative">
                    <svg class="absolute left-4 top-1/2 transform -translate-y-1/2 w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                    </svg>
                    <input
                        type="text"
                        class="w-full h-12 pl-12 pr-4 bg-gray-50 rounded-xl border border-gray-200 focus:border-blue-400 focus:ring-2 focus:ring-blue-100 focus:bg-white text-lg placeholder-gray-500 transition-all duration-200"
                        placeholder="Cari produk..."
                        x-model="keyword"
                    >
                </div>
            </div>

            <!-- PRODUCTS -->
            <div class="flex-1 overflow-auto p-4" x-ref="productsScroll">
                <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-4 xl:grid-cols-5 gap-3">
                    <template x-for="product in filteredProducts()" :key="product.id">
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
                                <div class="text-sm font-medium text-gray-800 truncate mb-1" x-text="product.nama"></div>
                                <div class="font-bold text-lg text-green-600">
                                    Rp <span x-text="format(product.harga_jual)"></span>
                                </div>
                            </div>
                        </div>
                    </template>

                    <div x-show="!filteredProducts().length" class="col-span-full flex flex-col items-center justify-center py-12 text-gray-400">
                        <svg class="w-16 h-16 mb-3 opacity-50" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"></path>
                        </svg>
                        <p class="text-lg font-medium">Produk tidak ditemukan</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- RIGHT CART -->
        <div class="w-[380px] bg-white shadow-2xl border-l border-gray-200 flex flex-col">

            <!-- HEADER -->
            <div class="p-4 border-b border-gray-100 bg-gradient-to-r from-green-500 to-emerald-500 text-white rounded-t-xl">
                <div class="flex items-center justify-between">
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
                    <button @click="cart = []" class="p-2 hover:bg-white/20 rounded-lg transition-all" x-show="cart.length">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                        </svg>
                    </button>
                </div>
            </div>

            <!-- ITEMS -->
            <div class="flex-1 overflow-auto p-4 space-y-3" x-ref="cartScroll">
                <template x-for="item in cart" :key="item.id">
                    <div class="flex items-center gap-3 p-3 bg-gray-50/70 hover:bg-gray-100 rounded-xl border border-gray-200 transition-all">
                        <div class="w-14 h-14 bg-gradient-to-br from-gray-200 rounded-lg overflow-hidden flex-shrink-0">
                            <img :src="item.image" class="w-full h-full object-cover" onerror="this.src='/images/no-image.png'">
                        </div>
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
                        <div class="text-right font-bold text-green-600 text-sm min-w-[65px]" x-text="format(item.qty * item.harga_jual)"></div>
                    </div>
                </template>

                <div x-show="!cart.length" class="flex flex-col items-center justify-center py-12 text-gray-400 text-center">
                    <svg class="w-16 h-16 mb-3 opacity-40" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M3 3h2l.4 2M7 13h10l4-8H5.4m0 0L7 13m0 0l-1.5 9 9.5-3.5M7 13l1.5-9"></path>
                    </svg>
                    <div class="font-medium text-lg mb-1">Keranjang kosong</div>
                    <div class="text-sm">Klik produk untuk menambahkan</div>
                </div>
            </div>

            <!-- SUMMARY -->
            <div class="p-4 border-t border-gray-200 bg-gradient-to-t from-gray-50 rounded-b-xl space-y-3">
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
                </div>

                <div class="pt-3 border-t">
                    <div class="flex justify-between text-xl font-bold mb-4 text-gray-800">
                        <span>Total</span>
                        <span class="text-green-600 text-2xl">Rp <span x-text="format(total())"></span></span>
                    </div>

                    <button
                        class="w-full bg-gradient-to-r from-green-500 to-emerald-600 hover:from-green-600 hover:to-emerald-700 text-white font-bold py-3 px-4 rounded-xl shadow-lg hover:shadow-xl transition-all duration-200 flex items-center justify-center gap-2 text-sm"
                        @click="$wire.checkout()"
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

</div>

<script>
    function posApp(products, categories) {
        return {
            products,
            categories,
            cart: [],
            keyword: '',
            discount: 0,
            tax: 0,

            init() {
                this.$watch('cart', () => {
                    this.$nextTick(() => this.scrollToBottom());
                });
            },

            filteredProducts() {
                if (!this.keyword.trim()) return this.products;
                return this.products.filter(p =>
                    p.nama.toLowerCase().includes(this.keyword.toLowerCase().trim())
                );
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
