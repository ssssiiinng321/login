<?php
require 'session.php';
$username = $_SESSION['username'] ?? 'Cashier';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>POS - YourPurpose</title>
    <link rel="stylesheet" href="../style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        body { 
            overflow: hidden; 
            background: transparent; 
        } 
        
        .pos-layout {
            display: flex;
            height: 100vh;
            width: 100vw;
            overflow: hidden;
            position: relative;
            z-index: 10;
        }
        
        .product-grid-section {
            flex: 1;
            padding: 2rem;
            display: flex;
            flex-direction: column;
            border-right: none;
            overflow: hidden; 
            /* Theme Panel Style */
            background: var(--panel-bg);
            margin: 2rem; 
            border-radius: var(--radius-lg);
            box-shadow: var(--shadow-soft);
            color: var(--text-primary);
            transition: background 0.3s, color 0.3s;
        }

        .cart-section {
            width: 380px; 
            min-width: 350px;
            padding: 2rem;
            display: flex;
            flex-direction: column;
            background: var(--sidebar-bg); /* Glassy Sidebar */
            backdrop-filter: blur(10px);
            -webkit-backdrop-filter: blur(10px);
            border-left: 1px solid var(--border-color);
            z-index: 100;
            color: var(--text-primary);
            box-shadow: -10px 0 30px rgba(0,0,0,0.2);
            transition: background 0.3s;
        }

        /* Search Bar */
        .search-bar {
            margin-bottom: 1rem;
            position: relative;
            flex-shrink: 0;
        }
        .search-bar input {
            width: 100%;
            padding: 0.8rem 1rem 0.8rem 2.8rem;
            background: var(--input-bg); 
            border: 1px solid var(--input-border);
            border-radius: 0.5rem;
            color: var(--text-primary);
            font-size: 1rem;
            transition: all 0.2s;
        }
        .search-bar input:focus {
            background: var(--card-bg);
            border-color: var(--accent);
            box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.1);
            outline: none;
        }
        .search-bar i {
            position: absolute;
            left: 1rem;
            top: 50%;
            transform: translateY(-50%);
            color: var(--text-secondary);
        }

        /* Grid */
        .product-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(140px, 1fr));
            gap: 1rem;
            overflow-y: auto;
            padding-bottom: 2rem;
            flex: 1; 
        }
        .product-card {
            background: var(--card-bg); /* Use card bg variable, might need specific overrides if 'white paper' logic differs */
            border: 1px solid var(--border-color);
            border-radius: var(--radius-md);
            padding: 1rem;
            cursor: pointer;
            transition: all 0.2s;
            display: flex;
            flex-direction: column;
            align-items: center;
            text-align: center;
            height: fit-content;
            box-shadow: 0 4px 6px rgba(0,0,0,0.02);
            color: var(--text-primary);
        }
        .product-card:hover {
            transform: translateY(-4px);
            box-shadow: 0 10px 15px rgba(0,0,0,0.1);
            border-color: var(--accent);
            background: rgba(255,255,255,0.08); /* Slight highlight on dark */
        }
        /* Light mode specific hover handling via body.light-mode if needed, or stick to refined vars */
        
        .product-card img {
            width: 100%;
            aspect-ratio: 1;
            object-fit: cover;
            border-radius: 0.5rem;
            margin-bottom: 0.75rem;
            background: rgba(0,0,0,0.2); /* Dark placeholder */
        }
        .product-name { font-weight: 600; font-size: 0.95rem; margin-bottom: 0.25rem; line-height: 1.3; }
        .product-price { color: var(--accent); font-weight: 700; font-size: 1.1rem; }
        .product-stock { font-size: 0.8rem; color: var(--text-muted); margin-top: 0.25rem; }
        
        /* Mobile Responsive */
        @media (max-width: 768px) {
            body { overflow: auto; } /* Allow full page scroll on mobile if needed, though we try to contain it */
            .pos-layout {
                flex-direction: column;
                height: 100vh; /* Keep it acting like an app */
            }
            .product-grid-section {
                border-right: none;
                flex: 1;
                order: 1;
            }
            .cart-section {
                width: 100%;
                height: 300px; /* Partial height for cart */
                border-top: 1px solid rgba(255,255,255,0.1);
                border-left: none;
                order: 2;
                box-shadow: 0 -4px 20px rgba(0,0,0,0.3);
            }
            .product-grid {
                grid-template-columns: repeat(auto-fill, minmax(120px, 1fr));
            }
        }

        /* Cart Styles */
        .cart-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 1rem;
            padding-bottom: 0.5rem;
            border-bottom: 1px solid rgba(255,255,255,0.1);
            flex-shrink: 0;
        }
        .cart-items {
            flex: 1;
            overflow-y: auto;
            padding-right: 0.5rem;
        }
        .cart-item {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 0.75rem;
            background: rgba(0,0,0,0.2);
            margin-bottom: 0.5rem;
            border-radius: var(--radius-md);
        }
        /* Scrollbar styling */
        ::-webkit-scrollbar { width: 6px; }
        ::-webkit-scrollbar-track { background: rgba(255,255,255,0.02); }
        ::-webkit-scrollbar-thumb { background: rgba(255,255,255,0.1); border-radius: 3px; }
        ::-webkit-scrollbar-thumb:hover { background: rgba(255,255,255,0.2); }

        .cart-footer {
            margin-top: auto; /* Push to bottom */
            border-top: 1px solid rgba(255,255,255,0.1);
            padding-top: 1rem;
            flex-shrink: 0;
        }

        /* Modal Responsive */
        .modal {
            position: fixed;
            top: 0; left: 0;
            width: 100%; height: 100%;
            background: rgba(0,0,0,0.8);
            display: none; /* Hidden by default */
            justify-content: center;
            align-items: center;
            z-index: 1000;
            backdrop-filter: blur(5px);
        }
        .modal.active {
            display: flex;
        }

        /* Payment Options */
        .payment-options {
            display: flex;
            gap: 1rem;
            justify-content: center;
            margin-top: 1rem;
        }
        .pay-option {
            background: rgba(255,255,255,0.03);
            border: 2px solid rgba(255,255,255,0.1);
            padding: 1.5rem;
            border-radius: var(--radius-lg);
            cursor: pointer;
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            display: flex;
            flex-direction: column;
            align-items: center;
            gap: 1rem;
            min-width: 140px;
            flex: 1;
        }
        .pay-option:hover { 
            background: rgba(255,255,255,0.08); 
            transform: translateY(-2px);
        }
        .pay-option.selected {
            border-color: #10b981;
            background: rgba(16, 185, 129, 0.1);
            box-shadow: 0 0 20px rgba(16, 185, 129, 0.2);
        }
        .pay-option i { font-size: 2rem; margin-bottom: 0.5rem; }
        .modal-content {
            background: var(--card-bg);
            padding: 2rem;
            border-radius: var(--radius-lg);
            width: 100%; 
            max-width: 500px;
            margin: 1rem;
        }

        .btn-pay {
            width: 100%;
            padding: 1rem;
            background: linear-gradient(135deg, #10b981 0%, #059669 100%);
            color: white;
            border: none;
            border-radius: 50px; /* Pill */
            font-size: 1.2rem;
            font-weight: bold;
            cursor: pointer;
            transition: all 0.3s;
            margin-top: 2rem;
            box-shadow: 0 4px 15px rgba(16, 185, 129, 0.3);
            letter-spacing: 0.5px;
        }
        .btn-pay:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 25px rgba(16, 185, 129, 0.5);
            background: linear-gradient(135deg, #34d399 0%, #10b981 100%);
        }
        .btn-pay:disabled {
            background: #4b5563;
            cursor: not-allowed;
            opacity: 0.7;
        }
        .btn-pay:hover:not(:disabled) {
            filter: brightness(1.1);
            transform: translateY(-1px);
        }

    </style>
</head>
<body>
    <canvas id="canvas1"></canvas>

<div class="pos-layout">
    <!-- Left: Product Grid -->
    <div class="product-grid-section">
        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom:1rem;">
            <div style="display: flex; align-items: center; gap: 1rem;">
                <a href="dashboard.php" style="color: var(--text-secondary); text-decoration:none;"><i class="fas fa-arrow-left"></i> <span data-i18n="back">Back</span></a>
                
                <!-- Theme Toggle -->
                <button onclick="toggleTheme()" id="themeBtn" style="background:none; border:none; color: var(--text-primary); cursor: pointer; font-size: 1.2rem;">
                    <i class="fas fa-moon"></i>
                </button>

                <!-- Lang Switcher -->
                <select class="lang-select" onchange="updateLanguage(this.value)" style="padding: 0.3rem; border-radius: 5px; background: rgba(255,255,255,0.1); color: var(--text-secondary); border: 1px solid rgba(255,255,255,0.2);">
                    <option value="en">EN</option>
                    <option value="km">KM</option>
                    <option value="cn">CN</option>
                </select>
            </div>
            <h2 style="margin:0;" data-i18n="pos_terminal">POS Terminal</h2>
            <div class="text-secondary"><?php echo htmlspecialchars($username); ?></div>
        </div>
        
        <div class="search-bar">
            <i class="fas fa-search"></i>
            <input type="text" id="search" data-i18n="search_products" placeholder="Search products by name or barcode... (Press / to focus)" onkeyup="filterProducts()">
        </div>

        <div class="product-grid" id="productGrid">
            <!-- Loaded via JS -->
        </div>
    </div>

    <!-- Right: Cart -->
    <div class="cart-section">
        <div class="cart-header">
            <h3 data-i18n="current_order">Current Order</h3>
            <button onclick="clearCart()" style="background:none; border:none; color: #ef4444; cursor:pointer;"><i class="fas fa-trash"></i> <span data-i18n="clear">Clear</span></button>
        </div>
        <div class="cart-items" id="cartItems">
            <!-- Loaded via JS -->
            <div style="text-align: center; color: var(--text-secondary); margin-top: 2rem;" data-i18n="cart_empty">
                Cart is empty
            </div>
        </div>
        <div class="cart-footer">
            <div class="total-row">
                <span data-i18n="total">Total</span>
                <span id="cartTotal">$0.00</span>
            </div>
            <button class="btn-pay" onclick="openPaymentModal()" id="btnPay" disabled>Charge $0.00</button>
        </div>
    </div>
</div>

<!-- Payment Modal -->
<div class="modal" id="paymentModal">
    <div class="modal-content" style="width: 500px; text-align: center;">
        <h2 data-i18n="payment_method">Payment Method</h2>
        <div class="payment-options">
            <div class="pay-option selected" onclick="selectPayment('cash', this)">
                <i class="fas fa-money-bill-wave"></i> <span data-i18n="cash">Cash</span>
            </div>
            <div class="pay-option" onclick="selectPayment('card', this)">
                <i class="fas fa-credit-card"></i> <span data-i18n="card">Card</span>
            </div>
        </div>
        <div style="margin-top: 2rem; text-align: left;">
            <label data-i18n="customer_name">Customer Name (Optional)</label>
            <input type="text" id="customerName" class="form-input" placeholder="Walk-in Customer" style="margin-top: 0.5rem; border-radius: 50px; padding-left: 1.5rem;">
        </div>
        <div class="form-footer" style="margin-top: 2rem; justify-content: space-between;">
            <button class="btn-purchase" onclick="closePaymentModal()" style="border:1px solid #555;" data-i18n="cancel">Cancel</button>
            <button class="btn-submit" onclick="processPayment()" style="width: auto; padding: 0.75rem 3rem;" data-i18n="complete_order">Complete Order</button>
        </div>
    </div>
</div>

<script>
    let products = [];
    let cart = [];
    let selectedPayment = 'cash';

    // Hotkeys
    document.addEventListener('keydown', (e) => {
        if(e.key === '/') {
            e.preventDefault();
            document.getElementById('search').focus();
        }
    });

    async function loadProducts() {
        const res = await fetch('products.php');
        products = await res.json();
        renderProducts(products);
    }

    function renderProducts(list) {
        const grid = document.getElementById('productGrid');
        grid.innerHTML = '';
        list.forEach((p, index) => {
            if(p.stock <= 0) return; // Don't show out of stock
            grid.innerHTML += `
                <div class="product-card animate-pop" style="animation-delay: ${index * 50}ms" onclick="addToCart(${p.id})">
                    <img src="${p.image_url || 'https://via.placeholder.com/80?text=Product'}" alt="">
                    <div class="product-name">${p.name}</div>
                    <div class="product-price">$${parseFloat(p.price).toFixed(2)}</div>
                    <div class="product-stock">${p.stock} in stock</div>
                </div>
            `;
        });
    }

    function filterProducts() {
        const query = document.getElementById('search').value.toLowerCase();
        const filtered = products.filter(p => 
            p.name.toLowerCase().includes(query) || 
            (p.barcode && p.barcode.includes(query))
        );
        renderProducts(filtered);
    }

    function addToCart(productId) {
        const product = products.find(p => p.id === productId);
        const existing = cart.find(c => c.product_id === productId);

        if(existing) {
            if(existing.quantity < product.stock) {
                existing.quantity++;
            }
        } else {
            cart.push({ ...product, product_id: product.id, quantity: 1 });
        }
        renderCart();
    }

    function updateQty(productId, delta) {
        const idx = cart.findIndex(c => c.product_id === productId);
        if(idx === -1) return;
        
        const item = cart[idx];
        const product = products.find(p => p.id === productId);

        item.quantity += delta;
        if(item.quantity <= 0) {
            cart.splice(idx, 1);
        } else if(item.quantity > product.stock) {
            item.quantity = product.stock;
        }
        renderCart();
    }

    function clearCart() {
        cart = [];
        renderCart();
    }

    function renderCart() {
        const container = document.getElementById('cartItems');
        if(cart.length === 0) {
            container.innerHTML = '<div style="text-align: center; color: var(--text-secondary); margin-top: 2rem;">Cart is empty</div>';
            document.getElementById('cartTotal').innerText = '$0.00';
            document.getElementById('btnPay').innerText = 'Charge $0.00';
            document.getElementById('btnPay').disabled = true;
            return;
        }

        container.innerHTML = '';
        let total = 0;
        
        cart.forEach(item => {
            const rowTotal = item.price * item.quantity;
            total += rowTotal;
            container.innerHTML += `
                <div class="cart-item">
                    <div class="cart-item-info">
                        <span class="cart-item-title">${item.name}</span>
                        <span class="cart-item-price">$${parseFloat(item.price).toFixed(2)} x ${item.quantity}</span>
                    </div>
                    <div class="cart-item-actions">
                        <button class="qty-btn" onclick="updateQty(${item.product_id}, -1)">-</button>
                        <span>${item.quantity}</span>
                        <button class="qty-btn" onclick="updateQty(${item.product_id}, 1)">+</button>
                    </div>
                </div>
            `;
        });

        document.getElementById('cartTotal').innerText = '$' + total.toFixed(2);
        document.getElementById('btnPay').innerText = 'Charge $' + total.toFixed(2);
        document.getElementById('btnPay').disabled = false;
    }

    // Payment Logic
    function openPaymentModal() {
        document.getElementById('paymentModal').classList.add('active');
    }
    function closePaymentModal() {
        document.getElementById('paymentModal').classList.remove('active');
    }

    function selectPayment(method, el) {
        selectedPayment = method;
        document.querySelectorAll('.pay-option').forEach(e => e.classList.remove('selected'));
        el.classList.add('selected');
    }

    async function processPayment() {
        const customerName = document.getElementById('customerName').value || 'Walk-in Customer';
        
        const payload = {
            items: cart.map(c => ({ product_id: c.product_id, quantity: c.quantity })),
            customer_name: customerName,
            payment_method: selectedPayment
        };

        try {
            const res = await fetch('orders.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify(payload)
            });
            
            const text = await res.text();
            let data;
            try {
                data = JSON.parse(text);
            } catch (e) {
                console.error("Non-JSON response:", text);
                throw new Error(`Server Error (${res.status}): ` + text.substring(0, 200) + "...");
            }

            if(!res.ok || data.error) throw new Error(data.error || 'Order failed');

            // Opens receipt in new window
            window.open(`receipt_view.php?id=${data.id}`, '_blank', 'width=400,height=600');
            
            cart = [];
            renderCart();
            closePaymentModal();
            loadProducts(); // Refresh stock
            alert('Order completed!');

        } catch (err) {
            alert('Error: ' + err.message);
        }
    }

    // Theme Toggle
    function toggleTheme() {
        document.body.classList.toggle('light-mode');
        const isLight = document.body.classList.contains('light-mode');
        localStorage.setItem('theme', isLight ? 'light' : 'dark');
        updateThemeIcon();
    }
    
    function updateThemeIcon() {
        const btn = document.getElementById('themeBtn');
        const isLight = document.body.classList.contains('light-mode');
        btn.innerHTML = isLight ? '<i class="fas fa-sun"></i>' : '<i class="fas fa-moon"></i>';
    }

    // Load Theme
    if(localStorage.getItem('theme') === 'light') {
        document.body.classList.add('light-mode');
        updateThemeIcon();
    }

    // Init
    loadProducts();
    initLanguage();
</script>
<script src="translations.js"></script>
<script src="animation.js"></script>
</body>
</html>
