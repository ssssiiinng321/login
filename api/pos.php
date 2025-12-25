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
            background: var(--bg-color); 
        } 
        
        .pos-layout {
            display: flex;
            height: 100vh;
            width: 100vw;
            overflow: hidden;
        }
        
        .product-grid-section {
            flex: 1;
            padding: 1rem;
            display: flex;
            flex-direction: column;
            border-right: 1px solid rgba(255,255,255,0.1);
            overflow: hidden; /* Contain scroll */
        }

        .cart-section {
            width: 350px;
            min-width: 300px;
            padding: 1rem;
            display: flex;
            flex-direction: column;
            background: var(--card-bg);
            border-left: 1px solid rgba(255,255,255,0.1);
            z-index: 10;
        }

        /* Search Bar */
        .search-bar {
            margin-bottom: 1rem;
            position: relative;
            flex-shrink: 0;
        }
        .search-bar input {
            width: 100%;
            padding: 0.75rem 1rem 0.75rem 2.5rem;
            background: var(--input-bg);
            border: 1px solid var(--input-border);
            border-radius: 0.5rem;
            color: white;
            font-size: 1rem;
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
            flex: 1; /* Take remaining space */
        }
        .product-card {
            background: rgba(255,255,255,0.05);
            border: 1px solid rgba(255,255,255,0.05);
            border-radius: 0.75rem;
            padding: 0.75rem;
            cursor: pointer;
            transition: all 0.2s;
            display: flex;
            flex-direction: column;
            align-items: center;
            text-align: center;
            height: fit-content;
        }
        .product-card:hover {
            background: rgba(255,255,255,0.1);
            transform: translateY(-2px);
            border-color: var(--accent);
        }
        .product-card img {
            width: 100%;
            aspect-ratio: 1;
            object-fit: cover;
            border-radius: 0.5rem;
            margin-bottom: 0.5rem;
            background: #000;
        }
        
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
            border-radius: 0.5rem;
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
        .modal-content {
            background: var(--card-bg);
            padding: 2rem;
            border-radius: 1rem;
            width: 100%; 
            max-width: 500px;
            margin: 1rem;
        }

        .btn-pay {
            width: 100%;
            padding: 1rem;
            background: var(--shape-color-3); /* Green */
            color: white;
            border: none;
            border-radius: 0.5rem;
            font-size: 1.2rem;
            font-weight: bold;
            cursor: pointer;
            transition: all 0.2s;
            margin-top: 1rem;
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

<div class="pos-layout">
    <!-- Left: Product Grid -->
    <div class="product-grid-section">
        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom:1rem;">
            <a href="dashboard.php" style="color: var(--text-secondary); text-decoration:none;"><i class="fas fa-arrow-left"></i> Back</a>
            <h2 style="margin:0;">POS Terminal</h2>
            <div class="text-secondary"><?php echo htmlspecialchars($username); ?></div>
        </div>
        
        <div class="search-bar">
            <i class="fas fa-search"></i>
            <input type="text" id="search" placeholder="Search products by name or barcode... (Press / to focus)" onkeyup="filterProducts()">
        </div>

        <div class="product-grid" id="productGrid">
            <!-- Loaded via JS -->
        </div>
    </div>

    <!-- Right: Cart -->
    <div class="cart-section">
        <div class="cart-header">
            <h3>Current Order</h3>
            <button onclick="clearCart()" style="background:none; border:none; color: #ef4444; cursor:pointer;"><i class="fas fa-trash"></i> Clear</button>
        </div>
        <div class="cart-items" id="cartItems">
            <!-- Loaded via JS -->
            <div style="text-align: center; color: var(--text-secondary); margin-top: 2rem;">
                Cart is empty
            </div>
        </div>
        <div class="cart-footer">
            <div class="total-row">
                <span>Total</span>
                <span id="cartTotal">$0.00</span>
            </div>
            <button class="btn-pay" onclick="openPaymentModal()" id="btnPay" disabled>Charge $0.00</button>
        </div>
    </div>
</div>

<!-- Payment Modal -->
<div class="modal" id="paymentModal">
    <div class="modal-content" style="width: 500px; text-align: center;">
        <h2>Payment Method</h2>
        <div class="payment-options">
            <div class="pay-option selected" onclick="selectPayment('cash', this)">
                <i class="fas fa-money-bill-wave"></i> Cash
            </div>
            <div class="pay-option" onclick="selectPayment('card', this)">
                <i class="fas fa-credit-card"></i> Card
            </div>
        </div>
        <div style="margin-top: 2rem; text-align: left;">
            <label>Customer Name (Optional)</label>
            <input type="text" id="customerName" class="form-input" placeholder="Walk-in Customer" style="margin-top: 0.5rem;">
        </div>
        <div class="form-footer" style="margin-top: 2rem; justify-content: space-between;">
            <button class="btn-purchase" onclick="closePaymentModal()" style="border:1px solid #555;">Cancel</button>
            <button class="btn-submit" onclick="processPayment()" style="width: auto; padding: 0.75rem 3rem;">Complete Order</button>
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
        list.forEach(p => {
            if(p.stock <= 0) return; // Don't show out of stock
            grid.innerHTML += `
                <div class="product-card" onclick="addToCart(${p.id})">
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

    // Init
    loadProducts();
</script>

</body>
</html>
