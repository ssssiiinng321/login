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
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <style>
        /* Dashboard Specific Styles */
        .dashboard-layout {
            display: flex;
            min-height: 100vh;
            width: 100%;
        }
        
        .sidebar {
            width: 250px;
            background: var(--sidebar-bg); /* Glassy Blue */
            backdrop-filter: blur(10px);
            -webkit-backdrop-filter: blur(10px);
            padding: 2rem 1rem;
            border-right: 1px solid rgba(255, 255, 255, 0.1);
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
            background: rgba(255, 255, 255, 0.1);
            color: var(--text-primary);
            box-shadow: 0 4px 6px rgba(0,0,0,0.1);
        }

        .main-content {
            flex: 1;
            padding: 2rem;
            background: transparent; /* Show Body Gradient */
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
            backdrop-filter: blur(10px);
            -webkit-backdrop-filter: blur(10px);
            border-radius: var(--radius-lg);
            padding: 1.5rem;
            border: 1px solid rgba(255,255,255,0.1);
            margin-bottom: 2rem;
            overflow-x: auto; /* Handle tables on small screens */
            box-shadow: 0 4px 6px rgba(0,0,0,0.05);
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
            border-radius: var(--radius-lg);
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
            <i class="fas fa-cube"></i> <span data-i18n="dashboard">Dashboard</span>
        </div>
        
        <!-- Language Switcher -->
        <div style="padding: 0 1rem; margin-bottom: 1rem;">
             <select class="lang-select form-input" onchange="updateLanguage(this.value)" style="width: 100%; padding: 0.5rem; border-radius: 5px; background: rgba(255,255,255,0.1); color: var(--text-secondary); border: 1px solid rgba(255,255,255,0.2);">
                <option value="en">🇺🇸 English</option>
                <option value="km">🇰🇭 Khmer</option>
                <option value="cn">🇨🇳 Chinese</option>
            </select>
        </div>

        <div style="padding: 0 1rem; margin-bottom: 2rem;">
            <a href="pos.php" class="btn-pos-launch">
                <i class="fas fa-cash-register"></i> <span data-i18n="open_pos">Open POS</span>
            </a>
        </div>
        <a href="#" class="nav-item active" onclick="showSection('overview')"><i class="fas fa-th-large"></i> <span data-i18n="overview">Overview</span></a>
        <a href="#" class="nav-item" onclick="showSection('products')"><i class="fas fa-box"></i> <span data-i18n="products">Products</span></a>
        <a href="#" class="nav-item" onclick="showSection('orders')"><i class="fas fa-shopping-cart"></i> <span data-i18n="orders">Orders</span></a>
        <a href="chatbot.php" class="nav-item"><i class="fas fa-robot"></i> <span data-i18n="ai_chat">AI Chat</span></a>
        <a href="#" class="nav-item" onclick="toggleThemeDashboard()"><i class="fas fa-adjust"></i> <span data-i18n="toggle_theme">Toggle Theme</span></a>
        <a href="logout.php" class="nav-item" style="margin-top: auto;"><i class="fas fa-sign-out-alt"></i> <span data-i18n="logout">Logout</span></a>
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

<!-- Overview Section -->
        <div id="section-overview">
            <div class="overview-panel"> <!-- Wrapper for Light Theme -->
                <div class="header-actions" style="margin-bottom: 2rem; display: flex; justify-content: space-between; align-items: center;">
                    <h2 style="margin: 0;"><i class="fas fa-th-large"></i> <span data-i18n="overview">Overview</span></h2>
                    <div style="display: flex; gap: 0.5rem; align-items: center;">
                        <input type="date" id="overview-date-filter" class="form-input" style="padding: 0.5rem;" onchange="filterOverview()">
                        <button class="btn-submit" onclick="clearOverviewFilter()" style="padding: 0.5rem 1rem; background: var(--text-secondary);"><i class="fas fa-undo"></i> <span data-i18n="clear">Clear</span></button>
                    </div>
                </div>

                <!-- Stats Row -->
                <div class="stats-grid">
                    <div class="stat-card bg-green">
                        <h3 data-i18n="todays_sales">Today's Sales</h3>
                        <div class="value" id="overview-daily-sales">$0.00</div>
                        <div style="font-size: 0.8rem; opacity: 0.8;" data-i18n="items_sold">We have sold <span id="overview-items-sold">0</span> items</div>
                    </div>
                    <div class="stat-card bg-blue">
                        <h3 data-i18n="products_sold_title">Products Sold</h3>
                        <div class="value" id="overview-products-sold">0</div>
                        <div style="font-size: 0.8rem; opacity: 0.8;" data-i18n="in_stock">In stock inventory</div>
                    </div>
                    <div class="stat-card bg-red">
                        <h3 data-i18n="refund_cancelled">Refund / Cancelled</h3>
                        <div class="value" id="overview-cancelled">$0.00</div>
                        <div style="font-size: 0.8rem; opacity: 0.8;">Total value</div>
                    </div>
                    <div class="stat-card bg-purple">
                        <h3 data-i18n="online_visits">Online Store Visits</h3>
                        <div class="value">16,893</div>
                        <div style="font-size: 0.8rem; opacity: 0.8;">(Placeholder)</div>
                    </div>
                </div>

                <!-- Charts Row -->
                <div class="charts-grid">
                    <!-- Circular Charts (Left) -->
                    <div class="chart-card" style="align-items: center; justify-content: space-around;">
                    <div style="text-align: center;">
                        <div style="position: relative;">
                            <svg viewBox="0 0 36 36" class="circular-chart">
                                <path class="circle-bg" d="M18 2.0845 a 15.9155 15.9155 0 0 1 0 31.831 a 15.9155 15.9155 0 0 1 0 -31.831" />
                                <path id="circle-target" class="circle" stroke-dasharray="0, 100" d="M18 2.0845 a 15.9155 15.9155 0 0 1 0 31.831 a 15.9155 15.9155 0 0 1 0 -31.831" style="stroke: #FF6B6B;" />
                            </svg>
                            <div style="position: absolute; top: 50%; left: 50%; transform: translate(-50%, -50%); font-weight: bold; font-size: 1.2rem; color: #555;">46%</div>
                        </div>
                        <div style="font-size: 0.8rem; color: #888; margin-top: 0.5rem;">Target Reached</div>
                    </div>

                    <div style="text-align: center;">
                        <div style="position: relative;">
                            <svg viewBox="0 0 36 36" class="circular-chart">
                                <path class="circle-bg" d="M18 2.0845 a 15.9155 15.9155 0 0 1 0 31.831 a 15.9155 15.9155 0 0 1 0 -31.831" />
                                <path id="circle-satisfaction" class="circle" stroke-dasharray="0, 100" d="M18 2.0845 a 15.9155 15.9155 0 0 1 0 31.831 a 15.9155 15.9155 0 0 1 0 -31.831" style="stroke: #32A0E9;" />
                            </svg>
                            <div style="position: absolute; top: 50%; left: 50%; transform: translate(-50%, -50%); font-weight: bold; font-size: 1.2rem; color: #555;">54%</div>
                        </div>
                        <div style="font-size: 0.8rem; color: #888; margin-top: 0.5rem;">Customer Satisfaction</div>
                    </div>
                    </div>

                    <!-- Main Area Chart (Center) -->
                    <div class="chart-card">
                        <h3 style="margin-bottom: 1rem; color: #444; font-size: 1rem;">Sales Statistics (Last 7 Days)</h3>
                        <div style="flex: 1; position: relative; width: 100%;">
                            <canvas id="salesChart"></canvas>
                        </div>
                    </div>

                    <!-- Summary List (Right) -->
                    <div class="summary-card">
                        <div class="summary-header" data-i18n="last_week_summary">Last Week Summary</div>
                        <div class="summary-list">
                            <div class="summary-item"><span data-i18n="sales">Sales</span> <span>$1,234.08</span></div>
                            <div class="summary-item"><span data-i18n="tax">Tax</span> <span>$5.67</span></div>
                            <div class="summary-item total"><span data-i18n="sales_total">Sales total</span> <span>$1,239.75</span></div>
                            
                            <div style="margin-top: 1.5rem;"></div>
                            <div class="summary-item"><span data-i18n="returns">Returns</span> <span>$25.00</span></div>
                            <div class="summary-item"><span data-i18n="tax">Tax</span> <span>$1.00</span></div>
                            <div class="summary-item total"><span data-i18n="returns_total">Returns total</span> <span>$26.00</span></div>
                        </div>
                    </div>
                </div>
            </div> <!-- End Overview Panel -->
        </div>

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
            <div style="display: flex; justify-content: flex-end; margin-bottom: 1rem; gap: 0.5rem;">
                <input type="date" id="order-date-filter" class="form-input" style="width: auto; padding: 0.5rem;">
                <button class="btn-submit btn-sm" style="width: auto;" onclick="filterOrders()">Search</button>
                <button class="btn-cancel btn-sm" style="width: auto;" onclick="clearOrderFilter()">Clear</button>
            </div>
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
                <label>Product Image</label>
                <input type="file" name="image" class="form-input" accept="image/*">
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
        document.getElementById('section-overview').style.display = 'none';
        document.getElementById('section-products').style.display = 'none';
        document.getElementById('section-orders').style.display = 'none';
        
        document.querySelectorAll('.nav-item').forEach(el => el.classList.remove('active'));
        
        document.getElementById('section-' + sectionId).style.display = 'block';
        document.getElementById('page-title').innerText = sectionId.charAt(0).toUpperCase() + sectionId.slice(1);
        
        // Update active nav
        const navs = document.querySelectorAll('.nav-item');
        if(sectionId === 'overview') navs[0].classList.add('active');
        if(sectionId === 'products') navs[1].classList.add('active');
        if(sectionId === 'orders') navs[2].classList.add('active');

        if(sectionId === 'overview') loadOverviewStats();
        if(sectionId === 'products') loadProducts();
        if(sectionId === 'orders') loadOrders();
    }
    
    // Overview Logic
    let salesChartInstance = null;

    async function loadOverviewStats(date = null) {
        let url = 'reports.php?type=all_stats';
        if(date) url += `&date=${date}`;

        const res = await fetch(url);
        const data = await res.json();
        
        if(data) {
            // Label Logic
            const labelEl = document.querySelector('[data-i18n="todays_sales"]');
            if(labelEl) {
                if(date) {
                    labelEl.innerText = `Sales (${date})`;
                    labelEl.removeAttribute('data-i18n'); 
                } else {
                    // Reset to default
                     labelEl.setAttribute('data-i18n', 'todays_sales');
                     const currentLang = localStorage.getItem('lang') || 'en';
                     // Simple check if translations loaded
                     if(typeof translations !== 'undefined' && translations[currentLang]) {
                         labelEl.innerText = translations[currentLang]['todays_sales'];
                     } else {
                         labelEl.innerText = "Today's Sales"; 
                         // If cleared and logic implies "All Time", update text manually
                         if(!date) labelEl.innerText = "Total Sales (All Time)";
                     }
                }
            }

            // Animate Numbers
            animateValue('overview-daily-sales', 0, parseFloat(data.daily_sales) || 0, 1000, '$');
            animateValue('overview-products-sold', 0, parseInt(data.products_sold) || 0, 1000, '');
            animateValue('overview-cancelled', 0, parseFloat(data.cancelled_total) || 0, 1000, '$');
            
            // Render Chart
            renderChart(data.chart_data || []);
            
            // Animate Circular Charts
            setTimeout(() => {
                document.getElementById('circle-target').style.strokeDasharray = "82, 100";
                document.getElementById('circle-satisfaction').style.strokeDasharray = "54, 100";
            }, 500); 
        }
    }

    function filterOverview() {
        const date = document.getElementById('overview-date-filter').value;
        if(date) loadOverviewStats(date);
    }
    
    function clearOverviewFilter() {
        document.getElementById('overview-date-filter').value = '';
        loadOverviewStats(null);
    }

    function animateValue(id, start, end, duration, prefix = '') {
        const obj = document.getElementById(id);
        if(!obj) return;
        let startTimestamp = null;
        const step = (timestamp) => {
            if (!startTimestamp) startTimestamp = timestamp;
            const progress = Math.min((timestamp - startTimestamp) / duration, 1);
            const value = progress * (end - start) + start;
            
            if (prefix === '$') {
                obj.innerHTML = prefix + value.toFixed(2);
            } else {
                obj.innerText = Math.floor(value);
            }
            
            if (progress < 1) {
                window.requestAnimationFrame(step);
            } else {
                if (prefix === '$') obj.innerHTML = prefix + end.toFixed(2);
                else obj.innerText = end;
            }
        };
        window.requestAnimationFrame(step);
    }

    function renderChart(chartData) {
        const ctx = document.getElementById('salesChart').getContext('2d');
        const labels = chartData.map(d => new Date(d.date).toLocaleDateString(undefined, {weekday: 'short'}));
        const values = chartData.map(d => parseFloat(d.total));

        if (salesChartInstance) {
            salesChartInstance.destroy();
        }

        // Create gradient
        const gradient = ctx.createLinearGradient(0, 0, 0, 400);
        gradient.addColorStop(0, 'rgba(79, 195, 161, 0.4)'); // Teal start
        gradient.addColorStop(1, 'rgba(79, 195, 161, 0.05)'); // Teal end transparent

        salesChartInstance = new Chart(ctx, {
            type: 'line',
            data: {
                labels: labels,
                datasets: [{
                    label: 'Sales',
                    data: values,
                    backgroundColor: gradient,
                    borderColor: '#4FC3A1',
                    borderWidth: 3,
                    tension: 0.4, // Smooth curve
                    fill: true,
                    pointBackgroundColor: '#ffffff',
                    pointBorderColor: '#4FC3A1',
                    pointBorderWidth: 2,
                    pointRadius: 6,
                    pointHoverRadius: 8
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: { display: false },
                    tooltip: {
                        backgroundColor: '#fff',
                        titleColor: '#333',
                        bodyColor: '#333',
                        borderColor: '#ddd',
                        borderWidth: 1,
                        displayColors: false,
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        grid: { 
                            color: '#f0f0f0',
                            borderDash: [5, 5] 
                        },
                        ticks: { color: '#888', font: {family: 'Inter'} },
                        border: { display: false }
                    },
                    x: {
                        grid: { display: false },
                        ticks: { color: '#888', font: {family: 'Inter'} },
                        border: { display: false }
                    }
                }
            }
        });
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
        products.forEach((p, index) => {
            tbody.innerHTML += `
                <tr class="animate-fade-up" style="animation-delay: ${index * 50}ms">
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
        // Do NOT convert to JSON object, send FormData directly for file upload support
        
        try {
            const res = await fetch('products.php', {
                method: 'POST',
                // No Content-Type header needed; browser sets it with boundary for multipart/form-data
                body: formData
            });
            
            const text = await res.text();
            let result;
            try {
                result = JSON.parse(text);
            } catch (err) {
                // If it's not JSON, it's likely a PHP error page (HTML)
                console.error("Non-JSON response:", text);
                throw new Error(`Server Error (${res.status}): ` + text.substring(0, 200) + "...");
            }
            
            if (!res.ok) {
                throw new Error(result.error || `Failed to create product (${res.status})`);
            }
            
            alert('Product created successfully!');
            closeProductModal();
            e.target.reset();
            loadProducts();
        } catch (error) {
            console.error('Error:', error);
            alert('Error adding product: ' + error.message);
        }
    }

    async function deleteProduct(id) {
        if(!confirm('Are you sure you want to delete this product? This cannot be undone.')) return;
        
        try {
            const res = await fetch(`products.php?id=${id}`, { method: 'DELETE' });
            // Handle non-JSON or error responses
            const text = await res.text();
            let data;
            try {
                data = JSON.parse(text);
            } catch(e) {
                // If Vercel blocks DELETE, it might return HTML (405 or 403)
                throw new Error(`Server Error (${res.status}): ` + text.substring(0, 100));
            }

            if(data.error) throw new Error(data.error);

            alert(data.message || 'Product deleted successfully');
            loadProducts();
            loadDailySales();
        } catch (error) {
            console.error('Delete Error:', error);
            alert('Failed to delete: ' + error.message);
        }
    }

    async function loadOrders(date = null) {
        let url = 'orders.php';
        if (date) {
            url += `?date=${date}`;
        }
        
        try {
            const res = await fetch(url);
            const orders = await res.json();
            const tbody = document.getElementById('orders-list');
            tbody.innerHTML = '';
            
            if (orders.length === 0) {
                tbody.innerHTML = '<tr><td colspan="6" style="text-align: center; padding: 2rem;">No orders found for this date.</td></tr>';
                return;
            }

            orders.forEach((o, index) => {
            tbody.innerHTML += `
                <tr class="animate-fade-up" style="animation-delay: ${index * 50}ms">
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
    } catch (error) {
        console.error(error);
    }
    }

    function filterOrders() {
        const date = document.getElementById('order-date-filter').value;
        if(date) {
            loadOrders(date);
        } else {
            alert("Please select a date first.");
        }
    }

    function clearOrderFilter() {
        document.getElementById('order-date-filter').value = '';
        loadOrders();
    }

    async function updateStatus(id, status) {
        await fetch('orders.php', {
            method: 'PUT',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({ id, status })
        });
        loadOrders();
    }

    // Theme Toggle
    function toggleThemeDashboard() {
        document.body.classList.toggle('light-mode');
        const isLight = document.body.classList.contains('light-mode');
        localStorage.setItem('theme', isLight ? 'light' : 'dark');
        // Optional: update icon if we had one that changes
    }

    // Load Theme
    if(localStorage.getItem('theme') === 'light') {
        document.body.classList.add('light-mode');
    }

    // Initial Load
    showSection('overview');

    // Init Logic
    window.addEventListener('DOMContentLoaded', () => {
        initLanguage();
    });
</script>
<script src="../translations.js"></script> <!-- Load Translations -->
</body>
</html>
