const translations = {
    en: {
        "dashboard": "Dashboard",
        "open_pos": "Open POS",
        "overview": "Overview",
        "products": "Products",
        "orders": "Orders",
        "ai_chat": "AI Chat",
        "toggle_theme": "Toggle Theme",
        "logout": "Logout",
        "hello": "Hello",
        "todays_sales": "Today's Sales",
        "items_sold": "We have sold <span id='overview-items-sold'>0</span> items",
        "products_sold_title": "Products Sold",
        "in_stock": "In stock inventory",
        "refund_cancelled": "Refund / Cancelled",
        "online_visits": "Online Store Visits",
        "target_reached": "Target Reached",
        "customer_satisfaction": "Customer Satisfaction",
        "sales_statistics": "Sales Statistics (Last 7 Days)",
        "last_week_summary": "Last Week Summary",
        "sales": "Sales",
        "tax": "Tax",
        "sales_total": "Sales total",
        "returns": "Returns",
        "returns_total": "Returns total",
        "add_product": "Add Product",
        "name": "Name",
        "price": "Price",
        "stock": "Stock",
        "actions": "Actions",
        "search": "Search",
        "clear": "Clear",
        "search_products": "Search products or scan barcode (/)",
        "pos_terminal": "POS Terminal",
        "current_order": "Current Order",
        "total": "Total",
        "charge": "Charge",
        "cart_empty": "Cart is empty",
        "payment_method": "Payment Method",
        "cash": "Cash",
        "card": "Card",
        "customer_name": "Customer Name (Optional)",
        "cancel": "Cancel",
        "complete_order": "Complete Order",
        "back": "Back"
    },
    km: {
        "dashboard": "ផ្ទាំងគ្រប់គ្រង",
        "open_pos": "បើក POS",
        "overview": "ទិដ្ឋភាពទូទៅ",
        "products": "ផលិតផល",
        "orders": "ការបញ្ជាទិញ",
        "ai_chat": "AI សន្ទនា",
        "toggle_theme": "ប្ដូររចនាប័ទ្ម",
        "logout": "ចាកចេញ",
        "hello": "សួស្តី",
        "todays_sales": "ការលក់ថ្ងៃនេះ",
        "items_sold": "យើងបានលក់ <span id='overview-items-sold'>0</span> មុខ",
        "products_sold_title": "ផលិតផលដែលបានលក់",
        "in_stock": "ស្តុកក្នុងឃ្លាំង",
        "refund_cancelled": "បង្វិលសង / បោះបង់",
        "online_visits": "អ្នកចូលមើលហាងអនឡាញ",
        "target_reached": "គោលដៅសម្រេចបាន",
        "customer_satisfaction": "ការពេញចិត្តរបស់អតិថិជន",
        "sales_statistics": "ស្ថិតិលក់ (៧ ថ្ងៃចុងក្រោយ)",
        "last_week_summary": "សង្ខេបសប្តាហ៍មុន",
        "sales": "ការលក់",
        "tax": "ពន្ធ",
        "sales_total": "សរុបការលក់",
        "returns": "ការបង្វិលសង",
        "returns_total": "សរុបការបង្វិលសង",
        "add_product": "បន្ថែមផលិតផល",
        "name": "ឈ្មោះ",
        "price": "តម្លៃ",
        "stock": "ស្តុក",
        "actions": "សកម្មភាព",
        "search": "ស្វែងរក",
        "clear": "សម្អាត",
        "search_products": "ស្វែងរកផលិតផល ឬ ស្កេនបាកូដ (/)",
        "pos_terminal": "ស្ថានីយ POS",
        "current_order": "ការបញ្ជាទិញបច្ចុប្បន្ន",
        "total": "សរុប",
        "charge": "គិតប្រាក់",
        "cart_empty": "កន្ត្រកទំនិញទទេ",
        "payment_method": "វិធីសាស្រ្តទូទាត់",
        "cash": "សាច់ប្រាក់",
        "card": "កាត",
        "customer_name": "ឈ្មោះអតិថិជន (មិនចាំបាច់)",
        "cancel": "បោះបង់",
        "complete_order": "បញ្ចប់ការបញ្ជាទិញ",
        "back": "ត្រឡប់ក្រោយ"
    },
    cn: {
        "dashboard": "仪表板",
        "open_pos": "打开 POS",
        "overview": "概览",
        "products": "产品",
        "orders": "订单",
        "ai_chat": "AI 聊天",
        "toggle_theme": "切换主题",
        "logout": "登出",
        "hello": "你好",
        "todays_sales": "今日销售额",
        "items_sold": "我们已售出 <span id='overview-items-sold'>0</span> 件商品",
        "products_sold_title": "售出产品",
        "in_stock": "库存总量",
        "refund_cancelled": "退款 / 取消",
        "online_visits": "网店访问量",
        "target_reached": "目标达成",
        "customer_satisfaction": "客户满意度",
        "sales_statistics": "销售统计 (过去7天)",
        "last_week_summary": "上周小结",
        "sales": "销售额",
        "tax": "税",
        "sales_total": "销售总额",
        "returns": "退货",
        "returns_total": "退货总额",
        "add_product": "添加产品",
        "name": "名称",
        "price": "价格",
        "stock": "库存",
        "actions": "操作",
        "search": "搜索",
        "clear": "清除",
        "search_products": "搜索产品或扫描条形码 (/)",
        "pos_terminal": "POS 终端",
        "current_order": "当前订单",
        "total": "总计",
        "charge": "收款",
        "cart_empty": "购物车为空",
        "payment_method": "支付方式",
        "cash": "现金",
        "card": "刷卡",
        "customer_name": "客户名称 (可选)",
        "cancel": "取消",
        "complete_order": "完成订单",
        "back": "返回"
    }
};

function updateLanguage(lang) {
    if (!translations[lang]) return;
    localStorage.setItem('lang', lang);

    document.querySelectorAll('[data-i18n]').forEach(el => {
        const key = el.getAttribute('data-i18n');
        if (translations[lang][key]) {
            // Check if element has children that need preserving?
            // For simple text, innerHTML allows spans (like items sold count)
            if (el.querySelectorAll('span[id]').length > 0 && key === 'items_sold') {
                // Special handling for dynamic content if needed, but innerHTML replace works if placeholder matches
                // Actually for 'items_sold', it has a span inside.
                // Let's just swap innerHTML. Note: this might kill event listeners if they were inline on children, but usually ok.
                // Better approach for dynamic values: keep specific spans separate.
                // For this helper, let's keep it simple.
                const oldVal = el.querySelector('span') ? el.querySelector('span').innerText : '0';
                el.innerHTML = translations[lang][key];
                if (el.querySelector('span')) el.querySelector('span').innerText = oldVal;
            } else {
                if (el.tagName === 'INPUT') {
                    el.placeholder = translations[lang][key];
                } else {
                    el.innerHTML = translations[lang][key];
                }
            }
        }
    });

    // Update Selectors
    document.querySelectorAll('.lang-select').forEach(sel => {
        sel.value = lang;
    });
}

function initLanguage() {
    const lang = localStorage.getItem('lang') || 'en';
    updateLanguage(lang);
}
