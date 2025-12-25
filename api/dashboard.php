<?php
ini_set('display_errors', 1);
require 'session.php';
$username = $_SESSION['username'] ?? 'Guest';
$is_logged_in = isset($_SESSION['user_id']);
if (!$is_logged_in) {
    header('Location: index.php');
    exit;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sales Dashboard - YourPurpose</title>
    <link rel="stylesheet" href="../style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        /* Dashboard Specific Styles */
        .dashboard-layout {
            display: flex;
            min-height: 100vh;
            width: 100%;
        }
        
        .sidebar {
            width: 250px;
            background: var(--card-bg);
            padding: 2rem 1rem;
            border-right: 1px solid rgba(255, 255, 255, 0.05);
            display: flex;
            flex-direction: column;
            flex-shrink: 0;
            transition: transform 0.3s;
        }

        .sidebar .logo {
            margin-bottom: 2rem;
            padding-left: 1rem;
        }

        .nav-item {
            padding: 1rem;
            color: var(--text-secondary);
            text-decoration: none;
            border-radius: 0.5rem;
            margin-bottom: 0.5rem;
            transition: all 0.3s;
            display: flex;
            align-items: center;
            gap: 0.75rem;
        }

        .nav-item:hover, .nav-item.active {
            background: rgba(255, 255, 255, 0.05);
            color: var(--text-primary);
        }

        .main-content {
            flex: 1;
            padding: 2rem;
            background: var(--bg-color);
            overflow-y: auto;
            position: relative;
        }
        
        /* Mobile Toggle */
        .mobile-menu-btn {
            display: none;
            background: none;
            border: none;
            color: white;
            font-size: 1.5rem;
            cursor: pointer;
            padding: 0.5rem;
        }

        @media (max-width: 900px) {
            .dashboard-layout {
                flex-direction: column;
            }
            .sidebar {
                width: 100%;
                border-right: none;
                border-bottom: 1px solid rgba(255, 255, 255, 0.05);
                padding: 1rem;
                flex-direction: row;
                overflow-x: auto;
                align-items: center;
                gap: 1rem;
            }
            .sidebar .logo {
                margin-bottom: 0;
                margin-right: 1rem;
            }
            .nav-item {
                margin-bottom: 0;
                padding: 0.75rem 1rem;
                white-space: nowrap;
            }
            .nav-item[href="logout.php"] {
                margin-top: 0;
                margin-left: auto;
            }
            .main-content {
                padding: 1rem;
            }
        }

        /* Further small screens: stack nav items if needed, or keep scroll */
        @media (max-width: 600px) {
            .sidebar {
                flex-wrap: wrap;
                justify-content: center;
            }
            .sidebar .logo {
                width: 100%;
                text-align: center;
                margin-bottom: 1rem;
            }
            .nav-item {
                font-size: 0.9rem;
            }
        }

        .header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 2rem;
            flex-wrap: wrap;
            gap: 1rem;
        }

        .section-title {
            font-size: 1.8rem;
            font-weight: 600;
        }

        .card {
            background: var(--card-bg);
            border-radius: 1rem;
            padding: 1.5rem;
            border: 1px solid rgba(255,255,255,0.05);
            margin-bottom: 2rem;
            overflow-x: auto; /* Handle tables on small screens */
        }
        
        .data-table {
            min-width: 600px; /* Force table width to trigger scroll if needed */
        }

        .data-table th, .data-table td {
            text-align: left;
            padding: 1rem;
            border-bottom: 1px solid rgba(255,255,255,0.05);
        }

        .data-table th {
            color: var(--text-secondary);
            font-weight: 500;
        }

        .btn-sm {
            padding: 0.5rem 1rem;
            font-size: 0.85rem;
            border-radius: 0.375rem;
        }

        /* Modal - Refined Symmetrical Design */
        .modal {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0,0,0,0.8);
            z-index: 1000;
            overflow-y: auto; /* Allow scrolling on small screens */
            padding: 1rem;
        }

        .modal.active {
            display: flex;
        }

        .modal-content {
            margin: auto; /* Robust centering */
            background: var(--card-bg);
            padding: 2.5rem;
            border-radius: 1rem;
            width: 400px;
            max-width: 100%;
            flex-shrink: 0;
            border: 1px solid rgba(255,255,255,0.1);
            box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.5);
            position: relative;
        }
        
        .modal-content h2 {
            text-align: center;
            margin-bottom: 2rem;
            font-size: 1.5rem;
            color: var(--text-primary);
        }

        .modal-content .form-group {
            margin-bottom: 1.25rem;
        }
        
        .modal-content label {
            display: block;
            margin-bottom: 0.5rem;
            font-size: 0.9rem;
            color: var(--text-secondary);
            font-weight: 500;
        }
        
        .modal-content .form-input {
            width: 100%;
            padding: 0.75rem 1rem;
            background: var(--input-bg);
            border: 1px solid var(--input-border);
            border-radius: 0.5rem;
            color: white;
            font-size: 1rem;
            transition: all 0.3s;
        }
        
        .modal-content .form-input:focus {
            border-color: var(--accent);
            box-shadow: 0 0 0 2px rgba(99, 102, 241, 0.2);
        }

        .modal-content textarea.form-input {
            resize: vertical;
            min-height: 80px;
        }

        .modal-content .form-footer {
            margin-top: 2rem;
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 1rem;
        }
        
        .modal-content .btn-cancel {
            background: transparent;
            border: 1px solid rgba(255,255,255,0.2);
            color: var(--text-secondary);
            padding: 0.75rem;
            border-radius: 0.5rem;
            cursor: pointer;
            font-weight: 600;
            transition: all 0.3s;
        }
        .modal-content .btn-cancel:hover {
            border-color: var(--text-primary);
            color: var(--text-primary);
        }
        
        .modal-content .btn-save {
            background: var(--accent);
            border: none;
            color: white;
            padding: 0.75rem;
            border-radius: 0.5rem;
            cursor: pointer;
            font-weight: 600;
            transition: background 0.3s;
        }
        .modal-content .btn-save:hover {
            background: var(--accent-hover);
        }

        .status-badge {
            padding: 0.25rem 0.75rem;
            border-radius: 999px;
            font-size: 0.75rem;
            font-weight: 600;
            text-transform: uppercase;
        }
        .status-pending { background: rgba(251, 191, 36, 0.2); color: #fbbf24; }
        .status-completed { background: rgba(16, 185, 129, 0.2); color: #10b981; }
        .status-cancelled { background: rgba(239, 68, 68, 0.2); color: #ef4444; }

    </style>
</head>
<body>

<div class="dashboard-layout">
    <!-- Sidebar -->
    <aside class="sidebar">
        <div class="logo">
            <i class="fas fa-cube"></i> <span>Dashboard</span>
        </div>
        <div style="padding: 0 1rem; margin-bottom: 2rem;">
            <a href="pos.php" class="btn-submit" style="display: block; text-align: center; text-decoration: none; background: #10b981;">
                <i class="fas fa-cash-register"></i> Open POS
            </a>
        </div>
        <a href="#" class="nav-item active" onclick="showSection('products')"><i class="fas fa-box"></i> Products</a>
        <a href="#" class="nav-item" onclick="showSection('orders')"><i class="fas fa-shopping-cart"></i> Orders</a>
        <a href="chatbot.php" class="nav-item"><i class="fas fa-robot"></i> AI Chat</a>
        <a href="logout.php" class="nav-item" style="margin-top: auto;"><i class="fas fa-sign-out-alt"></i> Logout</a>
    </aside>

    <!-- Main Content -->
    <div class="main-content">
        <header class="header">
            <div>
                <h1 id="page-title" class="section-title">Products</h1>
                <div id="daily-sales-widget" style="font-size: 0.9rem; color: #10b981; margin-top: 0.5rem; display: none;">
                    Today's Sales: <span style="font-weight: bold;" id="daily-total">$0.00</span> (<span id="daily-count">0</span> orders)
                </div>
            </div>
            <div>
                <span class="text-secondary" style="margin-right: 1rem;">Hello, <?php echo htmlspecialchars($username); ?></span>
            </div>
        </header>

<script>
    // ... existing navigation code ... 

    async function loadDailySales() {
        const res = await fetch('reports.php?type=daily');
        const data = await res.json();
        if(data) {
             document.getElementById('daily-sales-widget').style.display = 'block';
             document.getElementById('daily-total').innerText = '$' + (parseFloat(data.total) || 0).toFixed(2);
             document.getElementById('daily-count').innerText = data.count || 0;
        }
    }

    // Call this on load
    loadDailySales();
</script>

        <!-- Products Section -->
        <div id="section-products">
            <div style="display: flex; justify-content: flex-end; margin-bottom: 1rem;">
                <button class="btn-submit btn-sm" style="width: auto;" onclick="openProductModal()"><i class="fas fa-plus"></i> Add Product</button>
            </div>
            <div class="card">
                <table class="data-table">
                    <thead>
                        <tr>
                            <th>Name</th>
                            <th>Price</th>
                            <th>Stock</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody id="products-list">
                        <!-- Populated by JS -->
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Orders Section -->
        <div id="section-orders" style="display: none;">
            <div class="card">
                <table class="data-table">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Customer</th>
                            <th>Total</th>
                            <th>Status</th>
                            <th>Date</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody id="orders-list">
                        <!-- Populated by JS -->
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<!-- Add Product Modal -->
<div class="modal" id="productModal">
    <div class="modal-content">
        <h2 style="margin-bottom: 1.5rem;">Add New Product</h2>
        <form id="addProductForm" onsubmit="handleProductSubmit(event)">
            <div class="form-group">
                <label>Name</label>
                <input type="text" name="name" class="form-input" required>
            </div>
            <div class="form-group">
                <label>Price</label>
                <input type="number" name="price" step="0.01" class="form-input" required>
            </div>
            <div class="form-group">
                <label>Stock</label>
                <input type="number" name="stock" class="form-input" value="0">
            </div>
            <div class="form-group">
                <label>Description</label>
                <textarea name="description" class="form-input" rows="3"></textarea>
            </div>
        <div class="form-footer">
                <button type="button" class="btn-cancel" onclick="closeProductModal()">Cancel</button>
                <button type="submit" class="btn-save">Save Product</button>
            </div>
        </form>
    </div>
</div>

<script>
    // Navigation
    function showSection(sectionId) {
        document.getElementById('section-products').style.display = 'none';
        document.getElementById('section-orders').style.display = 'none';
        
        document.querySelectorAll('.nav-item').forEach(el => el.classList.remove('active'));
        
        document.getElementById('section-' + sectionId).style.display = 'block';
        document.getElementById('page-title').innerText = sectionId.charAt(0).toUpperCase() + sectionId.slice(1);
        
        // Update active nav
        // Simple logic based on onclick text matching
        const navs = document.querySelectorAll('.nav-item');
        if(sectionId === 'products') navs[0].classList.add('active');
        if(sectionId === 'orders') navs[1].classList.add('active');

        if(sectionId === 'products') loadProducts();
        if(sectionId === 'orders') loadOrders();
    }

    // Modal
    function openProductModal() {
        document.getElementById('productModal').classList.add('active');
    }
    function closeProductModal() {
        document.getElementById('productModal').classList.remove('active');
    }

    // API Calls
    async function loadProducts() {
        const res = await fetch('products.php');
        const products = await res.json();
        const tbody = document.getElementById('products-list');
        tbody.innerHTML = '';
        products.forEach(p => {
            tbody.innerHTML += `
                <tr>
                    <td>${p.name}</td>
                    <td>$${parseFloat(p.price).toFixed(2)}</td>
                    <td>${p.stock}</td>
                    <td>
                        <button class="btn-sm" style="background-color: #ef4444; color: white; border: none; cursor: pointer;" onclick="deleteProduct(${p.id})">Delete</button>
                    </td>
                </tr>
            `;
        });
    }

    async function handleProductSubmit(e) {
        e.preventDefault();
        const formData = new FormData(e.target);
        const data = Object.fromEntries(formData.entries());
        
        await fetch('products.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify(data)
        });
        
        closeProductModal();
        e.target.reset();
        loadProducts();
    }

    async function deleteProduct(id) {
        if(!confirm('Are you sure?')) return;
        await fetch(`products.php?id=${id}`, { method: 'DELETE' });
        loadProducts();
    }

    async function loadOrders() {
        const res = await fetch('orders.php');
        const orders = await res.json();
        const tbody = document.getElementById('orders-list');
        tbody.innerHTML = '';
        orders.forEach(o => {
            tbody.innerHTML += `
                <tr>
                    <td>#${o.id}</td>
                    <td>${o.customer_name}</td>
                    <td>$${parseFloat(o.total_amount).toFixed(2)}</td>
                    <td><span class="status-badge status-${o.status}">${o.status}</span></td>
                    <td>${new Date(o.created_at).toLocaleDateString()}</td>
                    <td>
                        
                        <select onchange="updateStatus(${o.id}, this.value)" style="background: #374151; color: white; border: 1px solid #4b5563; padding: 0.25rem; border-radius: 0.25rem;">
                            <option value="pending" ${o.status === 'pending' ? 'selected' : ''}>Pending</option>
                            <option value="completed" ${o.status === 'completed' ? 'selected' : ''}>Completed</option>
                            <option value="cancelled" ${o.status === 'cancelled' ? 'selected' : ''}>Cancelled</option>
                        </select>
                    </td>
                </tr>
            `;
        });
    }

    async function updateStatus(id, status) {
        await fetch('orders.php', {
            method: 'PUT',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({ id, status })
        });
        loadOrders();
    }

    // Initial Load
    showSection('products');
</script>

</body>
</html>
