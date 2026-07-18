<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Kasir POS — {{ $storeName }}</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:opsz,wght@14..32,300;14..32,400;14..32,500;14..32,600;14..32,700;14..32,800&family=JetBrains+Mono:wght@400;500;600;700&family=Outfit:wght@400;500;600;700;800;900&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('css/app.css') }}?v=3.1.0">
    <style>
        body {
            overflow: hidden;
        }

        .pos-layout {
            height: 100vh;
        }
    </style>
</head>

<body>

    <div class="pos-layout" id="posApp">

        <!-- LEFT: Products Panel -->
        <div class="pos-products-panel">

            <!-- Top Bar -->
            <div class="pos-topbar">
                <a href="{{ route('dashboard') }}" class="btn btn-sm btn-secondary btn-icon" title="Kembali ke Dashboard">
                    <svg width="16" height="16" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
                    </svg>
                </a>

                <!-- Search -->
                <div class="search-input" style="flex:1;max-width:400px">
                    <svg class="search-input-icon" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                    </svg>
                    <input type="text" id="searchInput" class="form-control" placeholder="Cari produk, SKU, scan barcode... (F2)"
                        oninput="debounceSearch(this.value)" autocomplete="off">
                </div>

                <!-- Barcode scan button -->
                <button class="btn btn-sm btn-secondary" onclick="focusSearch()" title="Scan Barcode (F2)">
                    <svg width="16" height="16" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v1m6 11h2m-6 0h-2v4m0-11v3m0 0h.01M12 12h4.01M16 20h4M4 12h4m12 4h.01M5 8h2a1 1 0 001-1V5a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1zm12 0h2a1 1 0 001-1V5a1 1 0 00-1-1h-2a1 1 0 00-1 1v2a1 1 0 001 1zM5 20h2a1 1 0 001-1v-2a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1z" />
                    </svg>
                    Barcode
                </button>

                <!-- Session Info -->
                <div style="display:flex;align-items:center;gap:10px;padding:8px 14px;background:rgba(16,185,129,0.1);border:1px solid rgba(16,185,129,0.3);border-radius:10px;font-size:12px;color:#34D399">
                    <span style="width:7px;height:7px;border-radius:50%;background:#34D399;animation:pulse-dot 2s infinite"></span>
                    {{ auth()->user()->name }}
                    <span style="color:var(--text-muted)">·</span>
                    <span id="sessionTimer">{{ $session->opened_at->diffForHumans() }}</span>
                </div>

                <!-- Close Session -->
                <button onclick="openCloseSession()" class="btn btn-sm btn-secondary" style="color:var(--color-warning)">
                    Tutup Sesi
                </button>
            </div>

            <!-- Category Tabs -->
            <div class="pos-categories">
                <button class="category-tab active" onclick="filterCategory(null, this)">
                    🏪 Semua
                </button>
                @foreach($categories as $cat)
                <button class="category-tab" onclick="filterCategory({{ $cat->id }}, this)" data-cat="{{ $cat->id }}">
                    {{ $cat->icon ?? '' }} {{ $cat->name }}
                </button>
                @foreach($cat->children ?? [] as $child)
                <button class="category-tab" onclick="filterCategory({{ $child->id }}, this)" data-cat="{{ $child->id }}"
                    style="font-size:11px;opacity:0.8">
                    └ {{ $child->name }}
                </button>
                @endforeach
                @endforeach
            </div>

            <!-- Products Grid -->
            <div class="pos-product-grid" id="productGrid">
                <div style="grid-column:1/-1;display:flex;align-items:center;justify-content:center;height:200px;color:var(--text-muted)">
                    <div style="text-align:center">
                        <div style="font-size:36px;margin-bottom:8px">🔍</div>
                        <div>Cari atau pilih kategori untuk menampilkan produk</div>
                    </div>
                </div>
            </div>
        </div>

        <!-- RIGHT: Cart Panel -->
        <div class="pos-cart-panel">

            <!-- Cart Header -->
            <div class="cart-header">
                <div class="flex-between">
                    <div class="cart-title">
                        🛒 Keranjang
                        <span class="cart-count" id="cartCount">0</span>
                    </div>
                    <div style="display:flex; gap:8px;">
                        <button onclick="holdCart()" class="btn btn-sm btn-secondary" style="font-size:11px" id="holdBtn" disabled title="Simpan Keranjang (Hold)">⏸️ Hold</button>
                        <button onclick="recallCart()" class="btn btn-sm btn-secondary" style="font-size:11px; display:none;" id="recallBtn" title="Buka Keranjang Tersimpan">🔄 Recall</button>
                        <button onclick="clearCart()" class="btn btn-sm btn-secondary" style="font-size:11px" id="clearBtn" disabled>
                            🗑️ Kosongkan
                        </button>
                    </div>
                </div>
            </div>

            <!-- Cart Items -->
            <div class="cart-items" id="cartItems">

                <!-- Customer Search Bar (inside cart, above items) -->
                <div style="padding:8px 12px;border-bottom:1px solid var(--border);background:var(--bg-elevated)">
                    <div style="font-size:11px;color:var(--text-muted);font-weight:600;margin-bottom:4px;text-transform:uppercase;letter-spacing:.5px">👤 Pelanggan</div>
                    <div style="position:relative">
                        <input type="text" id="customerSearch" class="form-control" placeholder="Cari nama / no. telp..." autocomplete="off"
                            oninput="searchCustomer(this.value)" style="height:32px;font-size:12px;padding-right:60px">
                        <div id="selectedCustomerBadge" style="display:none;position:absolute;right:4px;top:4px;background:#4F46E5;color:white;border-radius:6px;padding:2px 8px;font-size:11px;cursor:pointer" onclick="clearCustomer()">✕ hapus</div>
                    </div>
                    <div id="customerDropdown" style="display:none;position:absolute;z-index:100;background:var(--bg-card);border:1px solid var(--border);border-radius:10px;width:calc(100% - 32px);margin-top:2px;box-shadow:var(--shadow-lg)"></div>
                </div>
                <input type="hidden" id="selectedCustomerId" value="">

                <div class="empty-state" style="padding:40px 20px" id="emptyCart">
                    <div class="empty-state-icon">🛒</div>
                    <div class="empty-state-title">Keranjang Kosong</div>
                    <div class="empty-state-desc">Pilih produk atau scan barcode untuk memulai</div>
                </div>
            </div>

            <!-- Discount & Notes -->
            <div style="padding:10px 16px;border-top:1px solid var(--border)">
                <div style="display:grid;grid-template-columns:1fr 1fr;gap:8px">
                    <div>
                        <label style="font-size:11px;color:var(--text-muted);font-weight:600;display:block;margin-bottom:4px">Diskon (Rp)</label>
                        <input type="number" id="discountAmount" class="form-control" style="height:36px;font-size:13px"
                            placeholder="0" min="0" oninput="recalculate()">
                    </div>
                    <div>
                        <label style="font-size:11px;color:var(--text-muted);font-weight:600;display:block;margin-bottom:4px">Diskon (%)</label>
                        <input type="number" id="discountPercent" class="form-control" style="height:36px;font-size:13px"
                            placeholder="0" min="0" max="100" oninput="discountPercentChange()">
                    </div>
                </div>

                <!-- Coupon input -->
                <div style="margin-top: 8px; display: flex; gap: 8px;">
                    <input type="text" id="couponCode" class="form-control" style="height:36px; font-size:13px; text-transform: uppercase" placeholder="Masukkan Kode Voucher">
                    <button type="button" class="btn btn-secondary" style="height:36px; padding: 0 12px; font-size:12px;" onclick="applyCoupon()">Gunakan</button>
                </div>
                <div id="activeCouponBadge" style="display:none; align-items:center; justify-content:space-between; margin-top:8px; padding:6px 12px; background:rgba(79,70,229,0.1); border:1px solid rgba(79,70,229,0.2); border-radius:6px; font-size:12px;">
                    <span style="font-weight:600; color:var(--color-primary);" id="appliedCouponName">Voucher: -</span>
                    <button type="button" style="background:none; border:none; color:var(--color-danger); cursor:pointer; font-weight:bold;" onclick="removeCoupon()">✕</button>
                </div>
                <input type="hidden" id="appliedPromotionId" value="">
                <input type="hidden" id="promotionDiscountAmt" value="0">

                <!-- Loyalty Points Redemption -->
                <div id="loyaltyPointsGroup" style="display:none; margin-top:8px; padding:8px 12px; background:rgba(245,158,11,0.08); border:1px solid rgba(245,158,11,0.15); border-radius:6px; align-items:center; justify-content:space-between;">
                    <label style="display:flex; align-items:center; gap:8px; font-size:12px; cursor:pointer; margin:0;">
                        <input type="checkbox" id="usePointsCheckbox" style="width:16px; height:16px;" onchange="togglePointsRedemption()">
                        <span>Tukarkan Poin (<span id="availablePointsDisplay">0</span> pts)</span>
                    </label>
                    <div style="font-size:12px; font-weight:700; color:#D97706;" id="pointDiscountDisplay">Rp 0</div>
                </div>
                <input type="hidden" id="pointsUsedInput" value="0">
                <input type="hidden" id="pointDiscountAmt" value="0">

                <input type="text" id="notesInput" class="form-control" style="height:34px;font-size:12px;margin-top:8px"
                    placeholder="Catatan transaksi (opsional)">
            </div>

            <!-- Summary -->
            <div class="cart-summary">
                <div class="summary-row">
                    <span>Subtotal</span>
                    <span class="currency" id="sumSubtotal">Rp 0</span>
                </div>
                <div class="summary-row">
                    <span>Diskon</span>
                    <span class="currency" id="sumDiscount" style="color:var(--color-danger)">- Rp 0</span>
                </div>
                @if(\App\Models\StoreSetting::get('tax_enabled') === '1')
                <div class="summary-row">
                    <span>{{ \App\Models\StoreSetting::get('tax_name', 'PPN') }} {{ \App\Models\StoreSetting::get('tax_percent', '11') }}%</span>
                    <span class="currency" id="sumTax">Rp 0</span>
                </div>
                @endif
                <div class="summary-row total">
                    <span>TOTAL</span>
                    <span class="summary-amount" id="sumTotal">Rp 0</span>
                </div>
            </div>

            <!-- Payment Button -->
            <div style="padding:14px 16px">
                <button onclick="openPaymentModal()" class="btn btn-primary btn-block btn-xl" id="payBtn" disabled>
                    <svg width="18" height="18" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z" />
                    </svg>
                    Bayar
                </button>
            </div>
        </div>
    </div>

    <!-- ============================================================
     VARIANT SELECTOR MODAL
     ============================================================ -->
    <div class="modal-overlay" id="variantModal" style="display:none">
        <div class="modal" style="max-width:460px">
            <div class="modal-header">
                <div class="modal-title" id="variantModalTitle">Pilih Varian</div>
                <button onclick="closeVariantModal()" class="btn btn-sm btn-secondary btn-icon">✕</button>
            </div>
            <div class="modal-body">
                <div style="margin-bottom:16px">
                    <div style="font-size:13px;color:var(--text-muted);margin-bottom:10px;font-weight:600">Ukuran</div>
                    <div id="sizeOptions" style="display:flex;flex-wrap:wrap;gap:8px"></div>
                </div>
                <div>
                    <div style="font-size:13px;color:var(--text-muted);margin-bottom:10px;font-weight:600">Warna</div>
                    <div id="colorOptions" style="display:flex;flex-wrap:wrap;gap:8px"></div>
                </div>
                <div id="variantDetails" style="margin-top:16px;padding:14px;background:var(--bg-elevated);border-radius:10px;border:1px solid var(--border);display:none">
                    <div class="flex-between">
                        <div>
                            <div id="variantLabel" style="font-size:14px;font-weight:600;color:var(--text-primary)"></div>
                            <div id="variantStock" style="font-size:12px;color:var(--text-muted);margin-top:2px"></div>
                        </div>
                        <div id="variantPrice" style="font-size:18px;font-weight:800;color:var(--color-primary-light);font-family:monospace"></div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button onclick="closeVariantModal()" class="btn btn-secondary">Batal</button>
                <button onclick="addSelectedVariant()" class="btn btn-primary" id="addVariantBtn" disabled>
                    + Tambah ke Keranjang
                </button>
            </div>
        </div>
    </div>

    <!-- ============================================================
     PAYMENT MODAL
     ============================================================ -->
    <div class="modal-overlay" id="paymentModal" style="display:none">
        <div class="modal" style="max-width:520px">
            <div class="modal-header">
                <div class="modal-title">💳 Pembayaran</div>
                <button onclick="closePaymentModal()" class="btn btn-sm btn-secondary btn-icon">✕</button>
            </div>
            <div class="modal-body">
                <!-- Total Due -->
                <div style="text-align:center;margin-bottom:16px;padding:16px;background:var(--bg-elevated);border-radius:12px;border:1px solid var(--border)">
                    <div style="font-size:12px;color:var(--text-muted);font-weight:600;text-transform:uppercase;letter-spacing:0.5px">Total Pembayaran</div>
                    <div style="font-size:32px;font-weight:900;color:var(--text-primary);font-family:monospace;margin-top:4px" id="payTotalDisplay">Rp 0</div>
                </div>

                <!-- Payment Mode Toggle -->
                <div style="display:flex;gap:8px;margin-bottom:14px">
                    <button id="modeSingleBtn" onclick="setPaymentMode('single')" class="btn btn-primary" style="flex:1;font-size:12px">💵 Satu Metode</button>
                    <button id="modeSplitBtn" onclick="setPaymentMode('split')" class="btn btn-secondary" style="flex:1;font-size:12px">🔀 Split Payment</button>
                </div>

                <!-- SINGLE PAYMENT -->
                <div id="singlePaySection">
                    <div style="font-size:12px;color:var(--text-muted);font-weight:700;text-transform:uppercase;letter-spacing:0.5px;margin-bottom:10px">Metode Pembayaran</div>
                    <div style="display:grid;grid-template-columns:repeat(3,1fr);gap:8px">
                        @foreach($paymentMethods as $pm)
                        <button class="payment-method-btn" data-id="{{ $pm->id }}" data-name="{{ $pm->name }}" data-type="{{ $pm->type }}"
                            onclick="selectPaymentMethod(this)">
                            {{ $pm->type_icon }} {{ $pm->name }}
                        </button>
                        @endforeach
                    </div>
                    <!-- Cash Amount -->
                    <div id="cashSection" style="margin-top:12px">
                        <div style="font-size:12px;color:var(--text-muted);font-weight:700;text-transform:uppercase;letter-spacing:0.5px;margin-bottom:8px">Nominal Diterima</div>
                        <input type="number" id="cashInput" class="form-control" placeholder="Masukkan nominal bayar"
                            oninput="calculateChange()" style="font-size:18px;font-weight:700;text-align:center;height:52px">
                        <div id="quickAmounts" style="display:grid;grid-template-columns:repeat(3,1fr);gap:6px;margin-top:8px"></div>
                        <div style="margin-top:12px;padding:14px;border-radius:10px;border:1px solid var(--border)" id="changeDisplay">
                            <div class="flex-between">
                                <span style="font-size:14px;color:var(--text-secondary)">Kembalian</span>
                                <span style="font-size:22px;font-weight:800;font-family:monospace;color:#34D399" id="changeAmount">Rp 0</span>
                            </div>
                        </div>
                    </div>
                    <!-- QRIS Generator Section -->
                    <div id="qrisSection" style="display:none;margin-top:12px;text-align:center;padding:16px;background:var(--bg-elevated);border:1px solid var(--border);border-radius:12px;">
                        <div style="font-size:12px;color:var(--text-muted);font-weight:700;text-transform:uppercase;margin-bottom:8px">QRIS Dinamis (Midtrans)</div>
                        <div id="qrisQrContainer" style="display:none;margin-bottom:12px;">
                            <img id="qrisQrImage" src="" alt="QRIS QR Code" style="width:200px;height:200px;border-radius:8px;border:1px solid var(--border);box-shadow:0 4px 12px rgba(0,0,0,0.05);margin:0 auto;display:block;">
                            <div style="font-size:11px;color:var(--text-muted);margin-top:8px;">Scan QR di atas untuk membayar</div>
                            <div style="font-size:13px;font-weight:700;color:var(--color-primary);margin-top:6px;" id="qrisStatusText">Menunggu pembayaran...</div>
                        </div>
                        <button type="button" class="btn btn-primary" id="qrisGenerateBtn" onclick="generateQrisPayment()" style="width:100%">
                            ⚡ Generate QRIS QR
                        </button>
                    </div>

                    <div id="referenceSection" style="display:none;margin-top:8px">
                        <label class="form-label">No. Referensi (opsional)</label>
                        <input type="text" id="referenceInput" class="form-control" placeholder="Mis: no. otorisasi kartu">
                    </div>
                </div>

                <!-- SPLIT PAYMENT -->
                <div id="splitPaySection" style="display:none">
                    <div style="font-size:12px;color:var(--text-muted);font-weight:700;text-transform:uppercase;letter-spacing:0.5px;margin-bottom:10px">Rincian Pembayaran Kombinasi</div>
                    <div id="splitPaymentRows">
                        <!-- Rows generated by JS -->
                    </div>
                    <button onclick="addSplitRow()" class="btn btn-secondary btn-sm" style="width:100%;margin-top:8px;font-size:12px">+ Tambah Metode Lain</button>
                    <div style="margin-top:12px;padding:14px;border-radius:10px;border:1px solid var(--border)">
                        <div class="flex-between">
                            <span style="font-size:13px;color:var(--text-secondary)">Total Dibayar</span>
                            <span style="font-size:16px;font-weight:800;font-family:monospace;color:#34D399" id="splitPaidDisplay">Rp 0</span>
                        </div>
                        <div class="flex-between" style="margin-top:6px">
                            <span style="font-size:13px;color:var(--text-secondary)">Sisa</span>
                            <span style="font-size:16px;font-weight:800;font-family:monospace" id="splitRemainingDisplay" style="color:#FB7185">Rp 0</span>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button onclick="closePaymentModal()" class="btn btn-secondary">Batal</button>
                <button onclick="processPayment()" class="btn btn-success btn-lg" id="processPayBtn" disabled>
                    ✓ Proses Pembayaran
                </button>
            </div>
        </div>
    </div>

    <!-- ============================================================
     CLOSE SESSION MODAL
     ============================================================ -->
    <div class="modal-overlay" id="closeSessionModal" style="display:none">
        <div class="modal" style="max-width:460px">
            <div class="modal-header">
                <div class="modal-title">Tutup Sesi Kasir</div>
                <button onclick="document.getElementById('closeSessionModal').style.display='none'" class="btn btn-sm btn-secondary btn-icon">✕</button>
            </div>
            <form method="POST" action="{{ route('pos.session.close') }}">
                @csrf
                <div class="modal-body">
                    <div style="background:var(--bg-elevated);border-radius:10px;padding:16px;margin-bottom:16px;border:1px solid var(--border)">
                        <div class="flex-between mb-2">
                            <span style="font-size:13px;color:var(--text-secondary)">Sesi dibuka</span>
                            <span style="font-size:13px;color:var(--text-primary)">{{ $session->opened_at->format('H:i, d M Y') }}</span>
                        </div>
                        <div class="flex-between mb-2">
                            <span style="font-size:13px;color:var(--text-secondary)">Modal awal</span>
                            <span class="currency" style="font-size:13px;color:var(--text-primary)">Rp {{ number_format($session->opening_balance, 0, ',', '.') }}</span>
                        </div>
                        <div class="flex-between">
                            <span style="font-size:13px;color:var(--text-secondary)">Total transaksi</span>
                            <span style="font-size:13px;color:#34D399;font-weight:700">{{ $session->total_transactions }} transaksi</span>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="form-label">Uang Tunai di Laci (Rp)</label>
                        <input type="number" name="closing_balance" class="form-control" placeholder="Masukkan jumlah uang di laci" min="0" required>
                    </div>
                    <div class="form-group">
                        <label class="form-label">Catatan</label>
                        <textarea name="notes" class="form-control" rows="2" placeholder="Catatan penutupan sesi (opsional)"></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" onclick="document.getElementById('closeSessionModal').style.display='none'" class="btn btn-secondary">Batal</button>
                    <button type="submit" class="btn btn-warning">Tutup Sesi</button>
                </div>
            </form>
        </div>
    </div>

    <!-- Success Receipt Modal -->
    <div class="modal-overlay" id="receiptModal" style="display:none">
        <div class="modal" style="max-width:400px;text-align:center">
            <div class="modal-body" style="padding:32px">
                <div style="font-size:56px;margin-bottom:16px">✅</div>
                <div style="font-size:18px;font-weight:800;color:var(--text-primary);margin-bottom:4px">Transaksi Berhasil!</div>
                <div style="font-size:13px;color:var(--text-muted);margin-bottom:8px" id="receiptInvoice"></div>
                <div style="font-size:28px;font-weight:900;font-family:monospace;color:var(--color-primary-light);margin-bottom:20px" id="receiptChange"></div>
                <div style="display:grid;grid-template-columns:1fr 1fr;gap:10px">
                    <button onclick="printReceipt()" class="btn btn-secondary">
                        🖨️ Cetak Struk
                    </button>
                    <button onclick="newTransaction()" class="btn btn-primary">
                        + Transaksi Baru
                    </button>
                </div>
                <div style="margin-top:10px">
                    <button onclick="sendWhatsApp()" class="btn btn-secondary" id="waBtn" style="width:100%;background:rgba(37,211,102,0.15);border-color:#25D366;color:#25D366;display:none">
                        📱 Kirim Struk via WhatsApp
                    </button>
                </div>
            </div>
        </div>
    </div>

    <script>
        // ==========================================================
        // STATE
        // ==========================================================
        const TAX_ENABLED = {
            {
                \
                App\ Models\ StoreSetting::get('tax_enabled', '0') === '1' ? 'true' : 'false'
            }
        };
        const TAX_PERCENT = {
            {
                \
                App\ Models\ StoreSetting::get('tax_percent', '11')
            }
        };
        const CSRF = document.querySelector('meta[name="csrf-token"]').content;

        let cart = [];
        let currentProduct = null;
        let selectedVariant = null;
        let selectedPaymentMethod = null;
        let lastTransactionId = null;
        let lastTransactionInvoice = null;
        let searchTimeout = null;
        let paymentMode = 'single'; // 'single' or 'split'
        let splitPayments = [];
        let selectedCustomer = null;
        let customerSearchTimeout = null;
        const PAYMENT_METHODS = @json($paymentMethods);

        // ==========================================================
        // SEARCH
        // ==========================================================
        function focusSearch() {
            document.getElementById('searchInput').focus();
            document.getElementById('searchInput').select();
        }

        function debounceSearch(val) {
            clearTimeout(searchTimeout);
            searchTimeout = setTimeout(() => searchProducts(val), 300);
        }

        function filterCategory(categoryId, btn) {
            document.querySelectorAll('.category-tab').forEach(t => t.classList.remove('active'));
            btn.classList.add('active');
            searchProducts(document.getElementById('searchInput').value, categoryId);
        }

        async function searchProducts(query = '', categoryId = null) {
            const grid = document.getElementById('productGrid');
            grid.innerHTML = '<div style="grid-column:1/-1;display:flex;align-items:center;justify-content:center;height:150px;color:var(--text-muted)"><div>Memuat...</div></div>';

            let url = `/pos/search-products?q=${encodeURIComponent(query)}`;
            if (categoryId) url += `&category_id=${categoryId}`;

            try {
                const resp = await fetch(url, {
                    headers: {
                        'X-CSRF-TOKEN': CSRF
                    }
                });
                const products = await resp.json();
                renderProducts(products);
            } catch (e) {
                grid.innerHTML = '<div style="grid-column:1/-1;color:var(--color-danger);text-align:center;padding:40px">Gagal memuat produk</div>';
            }
        }

        function renderProducts(products) {
            const grid = document.getElementById('productGrid');
            if (!products.length) {
                grid.innerHTML = `<div style="grid-column:1/-1;text-align:center;padding:60px;color:var(--text-muted)">
                <div style="font-size:36px;margin-bottom:8px">🔍</div>
                <div>Produk tidak ditemukan</div>
            </div>`;
                return;
            }

            grid.innerHTML = products.map(p => {
                const totalStock = p.variants.reduce((s, v) => s + v.stock_qty, 0);
                const minPrice = Math.min(...p.variants.map(v => v.sell_price));
                const maxPrice = Math.max(...p.variants.map(v => v.sell_price));
                const priceStr = minPrice === maxPrice ? formatCurrency(minPrice) : `${formatCurrency(minPrice)}+`;
                const img = p.image_url ? `<img src="${p.image_url}" alt="${p.name}" loading="lazy">` : `<span style="font-size:40px">👕</span>`;

                return `<div class="product-card" onclick="openProduct(${JSON.stringify(p).replace(/'/g, "\\'")})" title="${p.name}">
                <div class="product-card-img">${img}</div>
                <div class="product-card-body">
                    <div class="product-card-name">${p.name}</div>
                    <div class="product-card-price">${priceStr}</div>
                    <div class="product-card-stock">${p.brand ? p.brand + ' · ' : ''}${totalStock} stok</div>
                </div>
            </div>`;
            }).join('');
        }

        // ==========================================================
        // VARIANT SELECTOR
        // ==========================================================
        function openProduct(product) {
            currentProduct = product;
            selectedVariant = null;

            document.getElementById('variantModalTitle').textContent = product.name;

            // Get unique sizes and colors
            const sizes = [...new Set(product.variants.map(v => v.size).filter(Boolean))];
            const colors = [...new Set(product.variants.map(v => v.color).filter(Boolean))];

            // Render sizes
            document.getElementById('sizeOptions').innerHTML = sizes.map(size => {
                const sizeVariants = product.variants.filter(v => v.size === size);
                const inStock = sizeVariants.some(v => v.stock_qty > 0);
                return `<button class="variant-chip ${!inStock ? 'disabled' : ''}" data-size="${size}" onclick="selectSize('${size}')">
                ${size}
                ${!inStock ? '<span style="color:var(--color-danger);font-size:9px;display:block">Habis</span>' : ''}
            </button>`;
            }).join('');

            // Render colors
            document.getElementById('colorOptions').innerHTML = colors.map(color => {
                const colorVariant = product.variants.find(v => v.color === color);
                const hex = colorVariant?.color_hex ?? '#888';
                return `<button class="variant-chip" data-color="${color}" onclick="selectColor('${color}')">
                <span style="width:12px;height:12px;border-radius:50%;background:${hex};border:2px solid var(--border);flex-shrink:0;display:inline-block;vertical-align:middle;margin-right:4px"></span>
                ${color}
            </button>`;
            }).join('');

            // If only one variant, select it automatically
            if (product.variants.length === 1) {
                selectVariantDirect(product.variants[0]);
            }

            document.getElementById('variantModal').style.display = 'flex';
            document.getElementById('variantDetails').style.display = 'none';
            document.getElementById('addVariantBtn').disabled = true;
        }

        function selectSize(size) {
            document.querySelectorAll('#sizeOptions .variant-chip').forEach(b => {
                b.classList.toggle('active', b.dataset.size === size);
            });
            tryMatchVariant();
        }

        function selectColor(color) {
            document.querySelectorAll('#colorOptions .variant-chip').forEach(b => {
                b.classList.toggle('active', b.dataset.color === color);
            });
            tryMatchVariant();
        }

        function tryMatchVariant() {
            const activeSize = document.querySelector('#sizeOptions .variant-chip.active')?.dataset.size;
            const activeColor = document.querySelector('#colorOptions .variant-chip.active')?.dataset.color;

            const matched = currentProduct.variants.find(v => {
                const sizeMatch = !activeSize || v.size === activeSize;
                const colorMatch = !activeColor || v.color === activeColor;
                return sizeMatch && colorMatch;
            });

            if (matched) {
                selectVariantDirect(matched);
            }
        }

        function selectVariantDirect(variant) {
            selectedVariant = variant;
            document.getElementById('variantLabel').textContent = variant.label;
            document.getElementById('variantStock').textContent = `Stok tersedia: ${variant.stock_qty}`;
            document.getElementById('variantPrice').textContent = formatCurrency(variant.sell_price);
            document.getElementById('variantDetails').style.display = 'block';
            document.getElementById('addVariantBtn').disabled = variant.stock_qty <= 0;
        }

        function addSelectedVariant() {
            if (!selectedVariant) return;
            addToCart(selectedVariant);
            closeVariantModal();
        }

        function closeVariantModal() {
            document.getElementById('variantModal').style.display = 'none';
            selectedVariant = null;
            currentProduct = null;
        }

        // ==========================================================
        // CART
        // ==========================================================
        function addToCart(variant) {
            const existing = cart.find(item => item.id === variant.id);
            if (existing) {
                if (existing.quantity < variant.stock_qty) {
                    existing.quantity++;
                } else {
                    showToast(`Stok ${variant.label} hanya ${variant.stock_qty}`, 'warning');
                    return;
                }
            } else {
                cart.push({
                    id: variant.id,
                    product_id: variant.product_id || currentProduct?.id || null,
                    category_id: variant.category_id || currentProduct?.category_id || null,
                    name: variant.product_name ?? currentProduct?.name ?? 'Produk',
                    variant_label: variant.label,
                    unit_price: variant.sell_price,
                    stock_qty: variant.stock_qty,
                    quantity: 1,
                    discount_amount: 0,
                });
            }
            renderCart();
            showToast(`${variant.label} ditambahkan`, 'success');
        }

        function renderCart() {
            const container = document.getElementById('cartItems');
            const emptyState = document.getElementById('emptyCart');

            if (cart.length === 0) {
                container.innerHTML = '';
                container.appendChild(emptyState || createEmptyState());
                emptyState.style.display = '';
                document.getElementById('cartCount').textContent = '0';
                document.getElementById('clearBtn').disabled = true;
                document.getElementById('holdBtn').disabled = true;
                document.getElementById('payBtn').disabled = true;
                return;
            }

            document.getElementById('cartCount').textContent = cart.reduce((s, i) => s + i.quantity, 0);
            document.getElementById('clearBtn').disabled = false;
            document.getElementById('holdBtn').disabled = false;
            document.getElementById('payBtn').disabled = false;

            container.innerHTML = cart.map((item, idx) => `
            <div class="cart-item" id="cartItem${idx}">
                <div style="flex:1;min-width:0">
                    <div class="cart-item-name">${item.name}</div>
                    <div class="cart-item-variant">${item.variant_label}</div>
                    <div class="cart-item-price">${formatCurrency(item.unit_price * item.quantity - item.discount_amount)}</div>
                    <div class="qty-control">
                        <button class="qty-btn" onclick="changeQty(${idx}, -1)">−</button>
                        <input class="qty-input" type="number" value="${item.quantity}" min="1" max="${item.stock_qty}"
                               onchange="setQty(${idx}, parseInt(this.value))">
                        <button class="qty-btn" onclick="changeQty(${idx}, 1)">+</button>
                    </div>
                </div>
                <button onclick="removeFromCart(${idx})" style="background:none;border:none;color:var(--color-danger);cursor:pointer;font-size:16px;padding:4px;flex-shrink:0" title="Hapus">✕</button>
            </div>
        `).join('');

            recalculate();
        }

        function changeQty(idx, delta) {
            const item = cart[idx];
            const newQty = item.quantity + delta;
            if (newQty < 1) {
                removeFromCart(idx);
                return;
            }
            if (newQty > item.stock_qty) {
                showToast('Melebihi stok tersedia', 'warning');
                return;
            }
            item.quantity = newQty;
            renderCart();
        }

        function setQty(idx, qty) {
            const item = cart[idx];
            if (qty < 1) {
                removeFromCart(idx);
                return;
            }
            if (qty > item.stock_qty) {
                showToast('Melebihi stok tersedia', 'warning');
                qty = item.stock_qty;
            }
            item.quantity = qty;
            renderCart();
        }

        function removeFromCart(idx) {
            cart.splice(idx, 1);
            renderCart();
        }

        function clearCart() {
            if (confirm('Hapus semua item dari keranjang?')) {
                cart = [];
                renderCart();
            }
        }

        // HOLD & RECALL
        function holdCart() {
            if (cart.length === 0) return;
            localStorage.setItem('heldCart', JSON.stringify(cart));
            cart = [];
            renderCart();
            showToast('Keranjang disimpan (Hold).', 'info');
            document.getElementById('recallBtn').style.display = 'inline-block';
        }

        function recallCart() {
            const held = localStorage.getItem('heldCart');
            if (held) {
                cart = JSON.parse(held);
                localStorage.removeItem('heldCart');
                renderCart();
                showToast('Keranjang dipulihkan (Recall).', 'success');
                document.getElementById('recallBtn').style.display = 'none';
            }
        }

        // CHECK HELD CART ON LOAD
        if (localStorage.getItem('heldCart')) {
            document.getElementById('recallBtn').style.display = 'inline-block';
        }

        let activeCoupon = null;
        const POINT_VALUE = 100; // Rp 100 per point

        async function applyCoupon() {
            const codeInput = document.getElementById('couponCode');
            const code = codeInput.value.trim().toUpperCase();
            if (!code) {
                showToast('Masukkan kode voucher terlebih dahulu.', 'warning');
                return;
            }

            try {
                const resp = await fetch('/pos/coupon/check', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': CSRF
                    },
                    body: JSON.stringify({
                        code
                    }),
                });
                const data = await resp.json();
                if (data.success) {
                    activeCoupon = data.promotion;
                    document.getElementById('activeCouponBadge').style.display = 'flex';
                    document.getElementById('appliedCouponName').textContent = `Voucher: ${activeCoupon.name}`;
                    document.getElementById('appliedPromotionId').value = activeCoupon.id;
                    codeInput.value = '';
                    showToast('Voucher berhasil digunakan!', 'success');
                    recalculate();
                } else {
                    showToast(data.message || 'Voucher tidak valid.', 'error');
                }
            } catch (e) {
                showToast('Gagal memverifikasi voucher.', 'error');
            }
        }

        function removeCoupon() {
            activeCoupon = null;
            document.getElementById('activeCouponBadge').style.display = 'none';
            document.getElementById('appliedPromotionId').value = '';
            document.getElementById('promotionDiscountAmt').value = '0';
            showToast('Voucher dihapus.', 'info');
            recalculate();
        }

        function togglePointsRedemption() {
            recalculate();
        }

        function recalculate() {
            const subtotal = cart.reduce((s, i) => s + i.unit_price * i.quantity - i.discount_amount, 0);

            // 1. Manual Discounts
            const discountAmt = parseFloat(document.getElementById('discountAmount').value) || 0;
            const discountPct = parseFloat(document.getElementById('discountPercent').value) || 0;
            const discountFromPct = discountPct > 0 ? subtotal * (discountPct / 100) : 0;
            const manualDiscount = discountAmt + discountFromPct;

            // 2. Promotion Discounts
            let promoDiscount = 0;
            if (activeCoupon) {
                let reqMet = false;
                if (activeCoupon.min_requirement_type === 'none') {
                    reqMet = true;
                } else if (activeCoupon.min_requirement_type === 'min_spend') {
                    if (subtotal >= activeCoupon.min_requirement_value) reqMet = true;
                } else if (activeCoupon.min_requirement_type === 'min_qty') {
                    const totalQty = cart.reduce((s, i) => s + i.quantity, 0);
                    if (totalQty >= activeCoupon.min_requirement_value) reqMet = true;
                }

                if (reqMet) {
                    if (activeCoupon.type === 'discount_percent') {
                        if (activeCoupon.target_type === 'all') {
                            promoDiscount = subtotal * (activeCoupon.value / 100);
                        } else {
                            const targetIds = activeCoupon.targets.map(id => parseInt(id));
                            promoDiscount = cart.reduce((s, i) => {
                                const isTarget = (activeCoupon.target_type === 'product' && targetIds.includes(i.product_id)) ||
                                    (activeCoupon.target_type === 'category' && targetIds.includes(i.category_id));
                                if (isTarget) {
                                    return s + (i.unit_price * i.quantity - i.discount_amount) * (activeCoupon.value / 100);
                                }
                                return s;
                            }, 0);
                        }
                    } else if (activeCoupon.type === 'discount_fixed') {
                        if (activeCoupon.target_type === 'all') {
                            promoDiscount = activeCoupon.value;
                        } else {
                            const targetIds = activeCoupon.targets.map(id => parseInt(id));
                            const targetSubtotal = cart.reduce((s, i) => {
                                const isTarget = (activeCoupon.target_type === 'product' && targetIds.includes(i.product_id)) ||
                                    (activeCoupon.target_type === 'category' && targetIds.includes(i.category_id));
                                return isTarget ? s + (i.unit_price * i.quantity - i.discount_amount) : s;
                            }, 0);
                            promoDiscount = Math.min(activeCoupon.value, targetSubtotal);
                        }
                    } else if (activeCoupon.type === 'bogo') {
                        const targetIds = activeCoupon.targets.map(id => parseInt(id));
                        promoDiscount = cart.reduce((s, i) => {
                            const isTarget = activeCoupon.target_type === 'all' ||
                                (activeCoupon.target_type === 'product' && targetIds.includes(i.product_id)) ||
                                (activeCoupon.target_type === 'category' && targetIds.includes(i.category_id));
                            if (isTarget) {
                                const freeUnits = Math.floor(i.quantity / 2);
                                return s + (freeUnits * i.unit_price);
                            }
                            return s;
                        }, 0);
                    }
                } else {
                    promoDiscount = 0;
                }
            }
            document.getElementById('promotionDiscountAmt').value = promoDiscount;

            // 3. Loyalty Points Discount
            let pointDiscount = 0;
            let pointsUsed = 0;
            if (selectedCustomer && document.getElementById('usePointsCheckbox').checked) {
                const currentSubtotal = Math.max(0, subtotal - manualDiscount - promoDiscount);
                const maxPointsRedeemable = Math.floor(currentSubtotal / POINT_VALUE);
                pointsUsed = Math.min(selectedCustomer.points, maxPointsRedeemable);
                pointDiscount = pointsUsed * POINT_VALUE;

                document.getElementById('pointDiscountDisplay').textContent = `- ${formatCurrency(pointDiscount)}`;
            } else {
                document.getElementById('pointDiscountDisplay').textContent = 'Rp 0';
            }
            document.getElementById('pointsUsedInput').value = pointsUsed;
            document.getElementById('pointDiscountAmt').value = pointDiscount;

            // Grand Total Calculations
            const totalDiscount = manualDiscount + promoDiscount + pointDiscount;
            const afterDiscount = Math.max(0, subtotal - totalDiscount);
            const tax = TAX_ENABLED ? afterDiscount * (TAX_PERCENT / 100) : 0;
            const grandTotal = afterDiscount + tax;

            document.getElementById('sumSubtotal').textContent = formatCurrency(subtotal);
            document.getElementById('sumDiscount').textContent = `- ${formatCurrency(totalDiscount)}`;
            if (document.getElementById('sumTax')) document.getElementById('sumTax').textContent = formatCurrency(tax);
            document.getElementById('sumTotal').textContent = formatCurrency(grandTotal);
        }

        function discountPercentChange() {
            const pct = parseFloat(document.getElementById('discountPercent').value) || 0;
            if (pct > 0) {
                document.getElementById('discountAmount').value = '';
            }
            recalculate();
        }

        function getGrandTotal() {
            const text = document.getElementById('sumTotal').textContent;
            return parseInt(text.replace(/[^0-9]/g, '')) || 0;
        }

        // ==========================================================
        // PAYMENT
        // ==========================================================
        function openPaymentModal() {
            if (cart.length === 0) return;
            const total = getGrandTotal();
            document.getElementById('payTotalDisplay').textContent = formatCurrency(total);
            document.getElementById('cashInput').value = '';
            document.getElementById('changeAmount').textContent = 'Rp 0';
            selectedPaymentMethod = null;
            paymentMode = 'single';
            splitPayments = [];

            // Reset button states
            document.querySelectorAll('.payment-method-btn').forEach(b => b.classList.remove('active'));
            document.getElementById('processPayBtn').disabled = true;
            document.getElementById('modeSingleBtn').className = 'btn btn-primary';
            document.getElementById('modeSplitBtn').className = 'btn btn-secondary';
            document.getElementById('singlePaySection').style.display = 'block';
            document.getElementById('splitPaySection').style.display = 'none';

            // Generate quick amount buttons
            const quickDiv = document.getElementById('quickAmounts');
            const amounts = [total, roundUp(total, 5000), roundUp(total, 10000), roundUp(total, 20000), roundUp(total, 50000), roundUp(total, 100000)];
            const uniqueAmounts = [...new Set(amounts)].slice(0, 6);
            quickDiv.innerHTML = uniqueAmounts.map(a => `
            <button class="btn btn-secondary btn-sm" onclick="setQuickAmount(${a})" style="font-size:11px">
                ${formatCurrency(a)}
            </button>
        `).join('');

            document.getElementById('paymentModal').style.display = 'flex';
        }

        function setPaymentMode(mode) {
            paymentMode = mode;
            if (mode === 'single') {
                document.getElementById('modeSingleBtn').className = 'btn btn-primary';
                document.getElementById('modeSplitBtn').className = 'btn btn-secondary';
                document.getElementById('singlePaySection').style.display = 'block';
                document.getElementById('splitPaySection').style.display = 'none';
                document.getElementById('processPayBtn').disabled = !selectedPaymentMethod;
            } else {
                document.getElementById('modeSingleBtn').className = 'btn btn-secondary';
                document.getElementById('modeSplitBtn').className = 'btn btn-primary';
                document.getElementById('singlePaySection').style.display = 'none';
                document.getElementById('splitPaySection').style.display = 'block';
                splitPayments = [];
                document.getElementById('splitPaymentRows').innerHTML = '';
                addSplitRow();
                recalcSplit();
            }
        }

        function addSplitRow() {
            const rowId = Date.now();
            const row = document.createElement('div');
            row.id = `split-row-${rowId}`;
            row.style.cssText = 'display:grid;grid-template-columns:1fr 130px 36px;gap:8px;margin-bottom:8px;align-items:center';
            const methodOptions = PAYMENT_METHODS.map(pm =>
                `<option value="${pm.id}" data-type="${pm.type}">${pm.type_icon ?? ''} ${pm.name}</option>`
            ).join('');
            row.innerHTML = `
            <select class="form-control split-method" data-rowid="${rowId}" onchange="recalcSplit()" style="height:38px;font-size:12px">
                ${methodOptions}
            </select>
            <input type="number" class="form-control split-amount" placeholder="Nominal" min="0"
                   data-rowid="${rowId}" oninput="recalcSplit()" style="height:38px;font-size:13px;text-align:right">
            <button onclick="removeSplitRow('${rowId}')" class="btn btn-sm" style="color:#FB7185;background:rgba(251,113,133,.1);border:1px solid rgba(251,113,133,.3);height:38px">&times;</button>
        `;
            document.getElementById('splitPaymentRows').appendChild(row);

            // Auto-fill remaining for last row
            const total = getGrandTotal();
            const alreadyEntered = [...document.querySelectorAll('.split-amount')].slice(0, -1).reduce((s, e) => s + (parseFloat(e.value) || 0), 0);
            const remaining = total - alreadyEntered;
            if (remaining > 0) {
                row.querySelector('.split-amount').value = remaining;
            }
            recalcSplit();
        }

        function removeSplitRow(rowId) {
            const row = document.getElementById(`split-row-${rowId}`);
            if (row) row.remove();
            recalcSplit();
        }

        function recalcSplit() {
            const total = getGrandTotal();
            const paid = [...document.querySelectorAll('.split-amount')].reduce((s, e) => s + (parseFloat(e.value) || 0), 0);
            const remaining = total - paid;
            document.getElementById('splitPaidDisplay').textContent = formatCurrency(paid);
            const remEl = document.getElementById('splitRemainingDisplay');
            remEl.textContent = remaining > 0 ? `Kurang ${formatCurrency(remaining)}` : 'Lunas ✓';
            remEl.style.color = remaining > 0 ? '#FB7185' : '#34D399';
            document.getElementById('processPayBtn').disabled = remaining > 0;
        }

        function roundUp(n, to) {
            return Math.ceil(n / to) * to;
        }

        function setQuickAmount(amount) {
            document.getElementById('cashInput').value = amount;
            calculateChange();
        }

        function selectPaymentMethod(btn) {
            document.querySelectorAll('.payment-method-btn').forEach(b => b.classList.remove('active'));
            btn.classList.add('active');

            selectedPaymentMethod = {
                id: parseInt(btn.dataset.id),
                name: btn.dataset.name,
                type: btn.dataset.type,
            };

            const isCash = btn.dataset.type === 'cash';
            const isQris = btn.dataset.type === 'qris';

            document.getElementById('cashSection').style.display = isCash ? 'block' : 'none';
            document.getElementById('qrisSection').style.display = isQris ? 'block' : 'none';
            document.getElementById('referenceSection').style.display = (!isCash && !isQris) ? 'block' : 'none';

            resetQrisSection();

            if (isQris) {
                document.getElementById('processPayBtn').disabled = true;
            } else if (!isCash) {
                document.getElementById('processPayBtn').disabled = false;
            } else {
                calculateChange();
            }
        }

        function calculateChange() {
            const total = getGrandTotal();
            const paid = parseFloat(document.getElementById('cashInput').value) || 0;
            const change = paid - total;
            document.getElementById('changeAmount').textContent = change >= 0 ? formatCurrency(change) : `Kurang ${formatCurrency(-change)}`;
            document.getElementById('changeDisplay').style.display = 'block';
            document.getElementById('changeAmount').style.color = change >= 0 ? '#34D399' : '#FB7185';
            document.getElementById('processPayBtn').disabled = change < 0 || !selectedPaymentMethod;
        }

        async function processPayment() {
            const total = getGrandTotal();
            const discountAmt = parseFloat(document.getElementById('discountAmount').value) || 0;
            const discountPct = parseFloat(document.getElementById('discountPercent').value) || 0;
            const customerId = document.getElementById('selectedCustomerId').value || null;

            let payments = [];
            let change = 0;

            if (paymentMode === 'single') {
                const isCash = selectedPaymentMethod?.type === 'cash';
                const paid = isCash ? parseFloat(document.getElementById('cashInput').value) : total;
                change = isCash ? paid - total : 0;
                const refNum = document.getElementById('referenceInput')?.value || null;
                payments = [{
                    payment_method_id: selectedPaymentMethod.id,
                    amount: paid,
                    reference_number: refNum
                }];
            } else {
                // Split payment
                [...document.querySelectorAll('#splitPaymentRows > div')].forEach(row => {
                    const methodId = parseInt(row.querySelector('.split-method').value);
                    const amount = parseFloat(row.querySelector('.split-amount').value) || 0;
                    if (amount > 0) payments.push({
                        payment_method_id: methodId,
                        amount
                    });
                });
            }

            const payload = {
                items: cart.map(i => ({
                    product_variant_id: i.id,
                    quantity: i.quantity,
                    unit_price: i.unit_price,
                    discount_amount: i.discount_amount,
                })),
                payments,
                discount_amount: discountAmt,
                discount_percent: discountPct,
                promotion_id: activeCoupon ? activeCoupon.id : null,
                promotion_discount: activeCoupon ? (parseFloat(document.getElementById('promotionDiscountAmt').value) || 0) : 0,
                points_used: parseInt(document.getElementById('pointsUsedInput').value) || 0,
                point_discount: parseFloat(document.getElementById('pointDiscountAmt').value) || 0,
                notes: document.getElementById('notesInput').value,
                customer_id: customerId,
            };

            document.getElementById('processPayBtn').disabled = true;
            document.getElementById('processPayBtn').textContent = 'Memproses...';

            try {
                const resp = await fetch('/pos/transaction', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': CSRF
                    },
                    body: JSON.stringify(payload),
                });

                const data = await resp.json();

                if (data.success) {
                    lastTransactionId = data.transaction.id;
                    lastTransactionInvoice = data.invoice_number;
                    closePaymentModal();
                    showReceiptModal(data.invoice_number, change, data.customer_phone);
                    // Clear cart and customer
                    cart = [];
                    clearCustomer();
                    removeCoupon();
                    document.getElementById('discountAmount').value = '';
                    document.getElementById('discountPercent').value = '';
                    document.getElementById('notesInput').value = '';
                    renderCart();
                } else {
                    showToast(data.error || 'Terjadi kesalahan', 'error');
                    document.getElementById('processPayBtn').disabled = false;
                    document.getElementById('processPayBtn').textContent = '✓ Proses Pembayaran';
                }
            } catch (e) {
                showToast('Gagal menghubungi server', 'error');
                document.getElementById('processPayBtn').disabled = false;
                document.getElementById('processPayBtn').textContent = '✓ Proses Pembayaran';
            }
        }

        function closePaymentModal() {
            document.getElementById('paymentModal').style.display = 'none';
            document.getElementById('processPayBtn').textContent = '✓ Proses Pembayaran';
            resetQrisSection();
        }

        function showReceiptModal(invoiceNumber, change, customerPhone) {
            document.getElementById('receiptInvoice').textContent = `Invoice: ${invoiceNumber}`;
            document.getElementById('receiptChange').textContent = change > 0 ? `Kembalian: ${formatCurrency(change)}` : 'Lunas ✓';
            // Show WA button if customer has a phone number
            const waBtn = document.getElementById('waBtn');
            if (customerPhone) {
                waBtn.style.display = 'block';
            } else {
                waBtn.style.display = 'none';
            }
            document.getElementById('receiptModal').style.display = 'flex';
        }

        function sendWhatsApp() {
            if (!lastTransactionId) return;
            const url = `/pos/transaction/${lastTransactionId}/whatsapp`;
            window.open(url, '_blank');
        }

        function printReceipt() {
            if (lastTransactionId) {
                window.open(`/pos/transaction/${lastTransactionId}/receipt`, '_blank');
            }
        }

        function newTransaction() {
            document.getElementById('receiptModal').style.display = 'none';
            lastTransactionId = null;
            focusSearch();
        }

        function openCloseSession() {
            document.getElementById('closeSessionModal').style.display = 'flex';
        }

        // ==========================================================
        // CUSTOMER SEARCH
        // ==========================================================
        function searchCustomer(query) {
            clearTimeout(customerSearchTimeout);
            if (!query || query.length < 2) {
                document.getElementById('customerDropdown').style.display = 'none';
                return;
            }
            customerSearchTimeout = setTimeout(async () => {
                const resp = await fetch(`/customers/search?q=${encodeURIComponent(query)}`, {
                    headers: {
                        'X-CSRF-TOKEN': CSRF
                    }
                });
                const customers = await resp.json();
                const dd = document.getElementById('customerDropdown');
                if (!customers.length) {
                    dd.style.display = 'none';
                    return;
                }
                dd.innerHTML = customers.map(c => `
                <div onclick="selectCustomer(${JSON.stringify(c).replace(/"/g, '&quot;')})" style="padding:10px 14px;cursor:pointer;border-bottom:1px solid var(--border);font-size:13px" onmouseover="this.style.background='var(--bg-elevated)'" onmouseout="this.style.background=''">
                    <strong>${c.name}</strong>
                    <span style="color:var(--text-muted);margin-left:8px">${c.phone || ''}</span>
                    ${c.is_member ? '<span style="margin-left:8px;font-size:10px;background:#4F46E5;color:white;border-radius:4px;padding:1px 6px">MEMBER</span>' : ''}
                    <span style="color:#F59E0B;font-size:11px;float:right">${c.points} pts</span>
                </div>
            `).join('');
                dd.style.display = 'block';
            }, 300);
        }

        function selectCustomer(c) {
            selectedCustomer = c;
            document.getElementById('selectedCustomerId').value = c.id;
            document.getElementById('customerSearch').value = c.name;
            document.getElementById('selectedCustomerBadge').style.display = 'block';
            document.getElementById('customerDropdown').style.display = 'none';

            if (c.is_member && c.points > 0) {
                document.getElementById('loyaltyPointsGroup').style.display = 'flex';
                document.getElementById('availablePointsDisplay').textContent = c.points;
                document.getElementById('usePointsCheckbox').checked = false;
                document.getElementById('pointDiscountDisplay').textContent = 'Rp 0';
            } else {
                document.getElementById('loyaltyPointsGroup').style.display = 'none';
            }
            recalculate();
        }

        function clearCustomer() {
            selectedCustomer = null;
            document.getElementById('selectedCustomerId').value = '';
            document.getElementById('customerSearch').value = '';
            document.getElementById('selectedCustomerBadge').style.display = 'none';
            document.getElementById('customerDropdown').style.display = 'none';

            document.getElementById('loyaltyPointsGroup').style.display = 'none';
            document.getElementById('usePointsCheckbox').checked = false;
            document.getElementById('pointsUsedInput').value = '0';
            document.getElementById('pointDiscountAmt').value = '0';
            recalculate();
        }

        // ==========================================================
        // QRIS DYNAMIC HANDLERS (Midtrans)
        // ==========================================================
        let qrisPollInterval = null;
        let currentQrisOrderId = null;

        function resetQrisSection() {
            if(qrisPollInterval) {
                clearInterval(qrisPollInterval);
                qrisPollInterval = null;
            }
            currentQrisOrderId = null;
            document.getElementById('qrisQrContainer').style.display = 'none';
            document.getElementById('qrisGenerateBtn').style.display = 'inline-block';
            document.getElementById('qrisGenerateBtn').disabled = false;
            document.getElementById('qrisGenerateBtn').textContent = '⚡ Generate QRIS QR';
        }

        async function generateQrisPayment() {
            const total = getGrandTotal();
            const genBtn = document.getElementById('qrisGenerateBtn');
            genBtn.disabled = true;
            genBtn.textContent = 'Menghubungkan Midtrans...';

            try {
                const resp = await fetch('/pos/qris/generate', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': CSRF },
                    body: JSON.stringify({ amount: total }),
                });
                const data = await resp.json();
                if (data.success) {
                    currentQrisOrderId = data.order_id;
                    document.getElementById('qrisQrImage').src = data.qr_image_url;
                    document.getElementById('qrisQrContainer').style.display = 'block';
                    document.getElementById('qrisStatusText').textContent = 'Menunggu pembayaran...';
                    document.getElementById('qrisStatusText').style.color = 'var(--text-primary)';
                    genBtn.style.display = 'none';
                    
                    startQrisStatusPolling(data.order_id);
                } else {
                    showToast(data.message || 'Gagal membuat QRIS.', 'error');
                    genBtn.disabled = false;
                    genBtn.textContent = '⚡ Generate QRIS QR';
                }
            } catch(e) {
                showToast('Koneksi internet bermasalah.', 'error');
                genBtn.disabled = false;
                genBtn.textContent = '⚡ Generate QRIS QR';
            }
        }

        function startQrisStatusPolling(orderId) {
            if(qrisPollInterval) clearInterval(qrisPollInterval);
            
            qrisPollInterval = setInterval(async () => {
                if (currentQrisOrderId !== orderId) {
                    clearInterval(qrisPollInterval);
                    return;
                }

                try {
                    const resp = await fetch(`/pos/qris/check/${orderId}`, {
                        headers: { 'X-CSRF-TOKEN': CSRF }
                    });
                    const data = await resp.json();
                    
                    if (data.success) {
                        if (data.settled) {
                            clearInterval(qrisPollInterval);
                            document.getElementById('qrisStatusText').textContent = '✓ Pembayaran Lunas!';
                            document.getElementById('qrisStatusText').style.color = '#34D399';
                            
                            if (document.getElementById('referenceInput')) {
                                document.getElementById('referenceInput').value = orderId;
                            }
                            
                            document.getElementById('processPayBtn').disabled = false;
                            showToast('QRIS Lunas! Menyelesaikan transaksi...', 'success');
                            
                            setTimeout(() => {
                                processPayment();
                            }, 1000);
                        } else {
                            document.getElementById('qrisStatusText').textContent = data.message;
                        }
                    }
                } catch(e) {
                    console.error('Error checking QRIS status:', e);
                }
            }, 3000);
        }

        // ==========================================================
        // KEYBOARD SHORTCUTS
        // ==========================================================
        document.addEventListener('keydown', e => {
            if (e.key === 'F2') {
                e.preventDefault();
                focusSearch();
            }
            if (e.key === 'F9' && cart.length > 0) {
                e.preventDefault();
                openPaymentModal();
            }
            if (e.key === 'Escape') {
                document.querySelectorAll('.modal-overlay').forEach(m => {
                    if (m.style.display === 'flex') m.style.display = 'none';
                });
                document.getElementById('customerDropdown').style.display = 'none';
            }
        });

        // Close dropdown when clicking outside
        document.addEventListener('click', e => {
            if (!e.target.closest('#customerSearch') && !e.target.closest('#customerDropdown')) {
                document.getElementById('customerDropdown').style.display = 'none';
            }
        });

        // ==========================================================
        // HELPERS
        // ==========================================================
        function formatCurrency(amount) {
            return 'Rp ' + new Intl.NumberFormat('id-ID').format(Math.round(amount));
        }

        function showToast(message, type = 'success') {
            const container = document.getElementById('toastContainer') || createToastContainer();
            const icons = {
                success: '✅',
                error: '❌',
                warning: '⚠️',
                info: 'ℹ️'
            };
            const toast = document.createElement('div');
            toast.className = `toast toast-${type}`;
            toast.style.cssText = 'position:fixed;bottom:24px;right:24px;background:var(--bg-card);border:1px solid var(--border);border-radius:12px;padding:12px 16px;display:flex;align-items:center;gap:10px;animation:slideInRight 0.3s ease;box-shadow:var(--shadow-lg);z-index:9999;max-width:320px;';
            toast.innerHTML = `<span style="font-size:16px">${icons[type]}</span><span style="font-size:13px;color:var(--text-primary)">${message}</span>`;
            document.body.appendChild(toast);
            setTimeout(() => toast.remove(), 3000);
        }

        function createToastContainer() {
            const div = document.createElement('div');
            div.id = 'toastContainer';
            document.body.appendChild(div);
            return div;
        }

        // Add CSS for variant chips
        const style = document.createElement('style');
        style.textContent = `
        .variant-chip {
            padding: 7px 14px;
            border: 1px solid var(--border);
            border-radius: var(--radius-md);
            background: var(--bg-elevated);
            color: var(--text-secondary);
            font-size: 13px;
            font-weight: 600;
            cursor: pointer;
            transition: var(--transition);
            display: flex;
            align-items: center;
            gap: 4px;
        }
        .variant-chip:hover { border-color: var(--color-primary); color: var(--text-primary); }
        .variant-chip.active { border-color: var(--color-primary); background: rgba(79,70,229,0.15); color: #A5B4FC; }
        .variant-chip.disabled { opacity: 0.4; cursor: not-allowed; }
        .payment-method-btn {
            padding: 10px 8px;
            border: 1px solid var(--border);
            border-radius: var(--radius-md);
            background: var(--bg-elevated);
            color: var(--text-secondary);
            font-size: 12px;
            font-weight: 600;
            cursor: pointer;
            transition: var(--transition);
            text-align: center;
        }
        .payment-method-btn:hover { border-color: var(--color-primary); color: var(--text-primary); }
        .payment-method-btn.active { border-color: var(--color-primary); background: rgba(79,70,229,0.15); color: #A5B4FC; }
        @keyframes slideInRight { from { opacity:0; transform:translateX(20px); } to { opacity:1; transform:translateX(0); } }
    `;
        document.head.appendChild(style);

        // Load products on start
        searchProducts('');
    </script>
</body>

</html>