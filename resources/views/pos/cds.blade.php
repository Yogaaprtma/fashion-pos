<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Customer Display — {{ $storeName }}</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800;900&family=JetBrains+Mono:wght@500;700;800&family=Outfit:wght@700;800;900&display=swap" rel="stylesheet">
    <style>
        * { box-sizing: border-box; margin: 0; padding: 0; font-family: 'Inter', -apple-system, BlinkMacSystemFont, sans-serif; }
        
        :root {
            --bg-body: #F8FAFC;
            --bg-card: #FFFFFF;
            --border: #E5E7EB;
            --color-primary: #4F46E5;
            --color-primary-light: #EEF2FF;
            --text-primary: #111827;
            --text-muted: #64748B;
        }

        body { 
            background: var(--bg-body); 
            color: var(--text-primary); 
            height: 100vh; 
            display: flex; 
            flex-direction: column; 
            overflow: hidden; 
        }
        
        /* Top Header */
        .header { 
            background: #FFFFFF; 
            padding: 18px 36px; 
            display: flex; 
            justify-content: space-between; 
            align-items: center; 
            border-bottom: 1px solid var(--border); 
            box-shadow: 0 1px 3px rgba(0,0,0,0.03);
            position: relative;
            z-index: 5;
        }

        .store-brand {
            display: flex;
            align-items: center;
            gap: 12px;
        }

        .store-logo-icon {
            width: 40px;
            height: 40px;
            background: linear-gradient(135deg, #4F46E5, #3B82F6);
            border-radius: 12px;
            display: grid;
            place-items: center;
            color: white;
            box-shadow: 0 4px 12px rgba(79, 70, 229, 0.3);
        }

        .store-name { 
            font-family: 'Outfit', sans-serif;
            font-size: 26px; 
            font-weight: 800; 
            background: linear-gradient(135deg, #3730A3, #4F46E5, #2563EB); 
            -webkit-background-clip: text; 
            color: transparent; 
            letter-spacing: -0.02em;
        }
        
        .clock-badge {
            font-family: 'JetBrains Mono', monospace;
            font-size: 16px;
            font-weight: 700;
            color: var(--color-primary);
            background: var(--color-primary-light);
            padding: 6px 16px;
            border-radius: 999px;
            border: 1px solid #C7D2FE;
        }

        /* Main Content Layout */
        .content { display: flex; flex: 1; overflow: hidden; }
        
        /* Left: Cart Items List */
        .cart-section { flex: 1; display: flex; flex-direction: column; background: #F8FAFC; border-right: 1px solid var(--border); }
        
        .cart-header { 
            padding: 20px 36px; 
            font-family: 'Outfit', sans-serif;
            font-size: 20px; 
            font-weight: 800; 
            color: var(--text-primary); 
            background: #FFFFFF;
            border-bottom: 1px solid var(--border); 
            display: flex;
            align-items: center;
            justify-content: space-between;
        }

        .cart-list { flex: 1; overflow-y: auto; padding: 24px 36px; }
        
        .cart-item { 
            display: flex; 
            align-items: center; 
            justify-content: space-between; 
            padding: 20px 24px; 
            background: #FFFFFF;
            border: 1px solid var(--border);
            border-radius: 16px;
            margin-bottom: 14px;
            box-shadow: 0 2px 8px rgba(15, 23, 42, 0.03);
            animation: slideIn 0.3s ease; 
        }

        @keyframes slideIn { from { opacity: 0; transform: translateY(10px); } to { opacity: 1; transform: translateY(0); } }
        
        .item-qty-badge { 
            font-family: 'JetBrains Mono', monospace;
            font-size: 16px; 
            font-weight: 800; 
            color: #4F46E5; 
            background: #EEF2FF; 
            border: 1px solid #C7D2FE;
            padding: 6px 14px; 
            border-radius: 10px; 
            margin-right: 18px; 
            flex-shrink: 0;
        }

        .item-info { flex: 1; min-width: 0; margin-right: 16px; }
        .item-name { font-size: 18px; font-weight: 800; color: var(--text-primary); margin-bottom: 4px; line-height: 1.3; }
        .item-variant { font-size: 13px; font-weight: 600; color: var(--text-muted); }
        .item-price { font-family: 'JetBrains Mono', monospace; font-size: 20px; font-weight: 800; color: var(--text-primary); text-align: right; flex-shrink: 0; }
        
        /* Right: Total Side Panel */
        .total-section { 
            width: 440px; 
            background: linear-gradient(180deg, #FFFFFF 0%, #F8FAFC 100%); 
            display: flex; 
            flex-direction: column; 
            justify-content: space-between; 
            align-items: center; 
            padding: 40px 32px; 
        }
        
        .customer-card { 
            background: #EEF2FF; 
            border: 1px solid #C7D2FE; 
            padding: 16px 20px; 
            border-radius: 16px; 
            width: 100%; 
            text-align: center; 
            margin-bottom: 24px;
        }
        .customer-label { font-size: 11px; font-weight: 800; color: #4F46E5; text-transform: uppercase; letter-spacing: 1.5px; margin-bottom: 3px; }
        .customer-name { font-size: 18px; font-weight: 800; color: #1E1B4B; }
        
        .total-card-box {
            background: #FFFFFF;
            border: 1px solid var(--border);
            border-radius: 24px;
            padding: 36px 24px;
            width: 100%;
            text-align: center;
            box-shadow: 0 10px 30px rgba(79, 70, 229, 0.06);
            position: relative;
            overflow: hidden;
        }

        .total-card-box::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 5px;
            background: linear-gradient(90deg, #4F46E5, #3B82F6);
        }

        .total-label { 
            font-size: 13px; 
            color: var(--text-muted); 
            margin-bottom: 12px; 
            font-weight: 800; 
            text-transform: uppercase; 
            letter-spacing: 2px; 
        }

        .total-value { 
            font-family: 'Outfit', sans-serif;
            font-size: 48px; 
            font-weight: 900; 
            color: #4F46E5; 
            line-height: 1.1; 
            text-align: center;
            letter-spacing: -0.03em;
        }

        .thankyou-box {
            text-align: center;
            margin-top: auto;
            padding-top: 30px;
        }

        .thankyou-icon {
            width: 56px;
            height: 56px;
            background: #EEF2FF;
            border-radius: 50%;
            display: grid;
            place-items: center;
            margin: 0 auto 12px auto;
            color: #4F46E5;
        }

        .thankyou-text {
            font-size: 14px;
            font-weight: 600;
            color: var(--text-muted);
        }
        
        /* Standby / Idle Screen Overlay */
        .idle-screen { 
            display: flex; 
            flex-direction: column; 
            align-items: center; 
            justify-content: center; 
            height: 100%; 
            width: 100%; 
            position: absolute; 
            top: 0;
            left: 0;
            background: linear-gradient(135deg, #F8FAFC 0%, #EEF2FF 50%, #E0E7FF 100%); 
            z-index: 10; 
            text-align: center;
            padding: 40px;
        }

        .idle-icon-wrapper {
            width: 120px;
            height: 120px;
            background: #FFFFFF;
            border-radius: 32px;
            display: grid;
            place-items: center;
            box-shadow: 0 20px 40px rgba(79, 70, 229, 0.12);
            margin-bottom: 32px;
            border: 1px solid #C7D2FE;
        }

        .idle-title { 
            font-family: 'Outfit', sans-serif;
            font-size: 42px; 
            font-weight: 900; 
            margin-bottom: 12px; 
            color: #1E1B4B;
            letter-spacing: -0.03em;
        }

        .idle-subtitle { 
            font-size: 20px; 
            color: var(--text-muted); 
            font-weight: 500;
            max-width: 500px;
        }
        
    </style>
</head>
<body>
    
    <!-- Standby / Idle Screen -->
    <div class="idle-screen" id="idleScreen">
        <div class="idle-icon-wrapper">
            <svg width="60" height="60" viewBox="0 0 24 24" fill="none" stroke="#4F46E5" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">
                <path d="M6 2L3 6v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2V6l-3-4z"></path>
                <line x1="3" y1="6" x2="21" y2="6"></line>
                <path d="M16 10a4 4 0 0 1-8 0"></path>
            </svg>
        </div>
        <h1 class="idle-title">Selamat Datang di {{ $storeName }}</h1>
        <p class="idle-subtitle">Silakan serahkan barang belanjaan Anda kepada kasir kami</p>
    </div>

    <!-- Header -->
    <div class="header">
        <div class="store-brand">
            <div class="store-logo-icon">
                <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M6 2L3 6v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2V6l-3-4z"></path>
                    <line x1="3" y1="6" x2="21" y2="6"></line>
                    <path d="M16 10a4 4 0 0 1-8 0"></path>
                </svg>
            </div>
            <div class="store-name">{{ $storeName }}</div>
        </div>
        <div class="clock-badge" id="clock">12:00:00</div>
    </div>
    
    <!-- Main Content -->
    <div class="content">
        <!-- Cart List -->
        <div class="cart-section">
            <div class="cart-header">
                <span>Daftar Belanja</span>
                <span id="cartCountBadge" style="font-size:14px; background:#EEF2FF; color:#4F46E5; padding:4px 12px; border-radius:999px; border:1px solid #C7D2FE;">0 Item</span>
            </div>
            <div class="cart-list" id="cartList">
                <!-- Items injected here via JS -->
            </div>
        </div>

        <!-- Total Side Panel -->
        <div class="total-section">
            <div style="width:100%;">
                <div class="customer-card" id="customerBox" style="display:none;">
                    <div class="customer-label">Pelanggan</div>
                    <div class="customer-name" id="customerName">-</div>
                </div>
                
                <div class="total-card-box">
                    <div class="total-label">Total Pembayaran</div>
                    <div class="total-value" id="totalValue">Rp 0</div>
                </div>
            </div>
            
            <div class="thankyou-box">
                <div class="thankyou-icon">
                    <svg width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M20.84 4.61a5.5 5.5 0 0 0-7.78 0L12 5.67l-1.06-1.06a5.5 5.5 0 0 0-7.78 7.78l1.06 1.06L12 21.23l7.78-7.78 1.06-1.06a5.5 5.5 0 0 0 0-7.78z"></path>
                    </svg>
                </div>
                <div class="thankyou-text">Terima kasih telah berbelanja!</div>
            </div>
        </div>
    </div>

    <script>
        function formatRupiah(amount) {
            return new Intl.NumberFormat('id-ID', { style: 'currency', currency: 'IDR', minimumFractionDigits: 0 }).format(amount);
        }

        function updateClock() {
            document.getElementById('clock').innerText = new Date().toLocaleTimeString('id-ID');
        }
        setInterval(updateClock, 1000);
        updateClock();

        let lastCartState = '';

        function syncCDS() {
            const cartStr = localStorage.getItem('cds_cart');
            const totalStr = localStorage.getItem('cds_total');
            const custStr = localStorage.getItem('cds_customer');
            const currentState = `${cartStr}_${totalStr}_${custStr}`;
            
            const idleScreen = document.getElementById('idleScreen');
            
            if (!cartStr || cartStr === '[]') {
                idleScreen.style.display = 'flex';
                lastCartState = currentState;
                return;
            }
            
            idleScreen.style.display = 'none';

            if (lastCartState === currentState) return;
            lastCartState = currentState;
            
            let cart = [];
            try {
                cart = JSON.parse(cartStr);
            } catch(e) {
                idleScreen.style.display = 'flex';
                return;
            }

            if (!cart.length) {
                idleScreen.style.display = 'flex';
                return;
            }

            const total = parseFloat(totalStr || 0);
            
            // Render Items
            const cartList = document.getElementById('cartList');
            let itemsHtml = '';
            let totalQty = 0;
            
            cart.forEach(item => {
                totalQty += item.qty;
                itemsHtml += `
                    <div class="cart-item">
                        <div class="item-qty-badge">${item.qty}x</div>
                        <div class="item-info">
                            <div class="item-name">${item.name}</div>
                            <div class="item-variant">${item.variant_name && item.variant_name !== 'Default' ? item.variant_name : 'Reguler'}</div>
                        </div>
                        <div class="item-price">${formatRupiah(item.price * item.qty)}</div>
                    </div>
                `;
            });
            
            cartList.innerHTML = itemsHtml;
            document.getElementById('cartCountBadge').innerText = `${totalQty} Item`;
            
            // Render Total
            document.getElementById('totalValue').innerText = formatRupiah(total);
            
            // Render Customer
            const custBox = document.getElementById('customerBox');
            if (custStr && custStr !== '-') {
                custBox.style.display = 'block';
                document.getElementById('customerName').innerText = custStr;
            } else {
                custBox.style.display = 'none';
            }
        }

        // Event listener for Storage
        window.addEventListener('storage', function(e) {
            syncCDS();
        });

        // Interval polling fallback
        setInterval(syncCDS, 500);

        // Init
        syncCDS();
    </script>
</body>
</html>
