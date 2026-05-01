CREATE TABLE IF NOT EXISTS items (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    code TEXT NOT NULL UNIQUE,
    barcode TEXT,
    name TEXT NOT NULL,
    category TEXT,
    description TEXT,
    unit_large TEXT NOT NULL,
    unit_small TEXT NOT NULL,
    small_unit_qty INTEGER NOT NULL DEFAULT 1,
    purchase_price REAL NOT NULL DEFAULT 0,
    purchase_total REAL NOT NULL DEFAULT 0,
    purchase_basis_qty INTEGER NOT NULL DEFAULT 0,
    selling_price REAL NOT NULL DEFAULT 0,
    profit_percent REAL NOT NULL DEFAULT 0,
    unit_price REAL NOT NULL DEFAULT 0,
    half_price REAL NOT NULL DEFAULT 0,
    allow_small_sale INTEGER NOT NULL DEFAULT 1,
    allow_half_sale INTEGER NOT NULL DEFAULT 1,
    promo_qty_1 INTEGER NOT NULL DEFAULT 0,
    promo_price_1 REAL NOT NULL DEFAULT 0,
    promo_qty_2 INTEGER NOT NULL DEFAULT 0,
    promo_price_2 REAL NOT NULL DEFAULT 0,
    promo_qty_3 INTEGER NOT NULL DEFAULT 0,
    promo_price_3 REAL NOT NULL DEFAULT 0,
    promo_qty_4 INTEGER NOT NULL DEFAULT 0,
    promo_price_4 REAL NOT NULL DEFAULT 0,
    promo_qty_5 INTEGER NOT NULL DEFAULT 0,
    promo_price_5 REAL NOT NULL DEFAULT 0,
    promo_qty_6 INTEGER NOT NULL DEFAULT 0,
    promo_price_6 REAL NOT NULL DEFAULT 0,
    stock INTEGER NOT NULL DEFAULT 0,
    exp_date TEXT,
    created_at TEXT NOT NULL
);

CREATE TABLE IF NOT EXISTS customers (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    code TEXT NOT NULL UNIQUE,
    name TEXT NOT NULL,
    phone TEXT,
    address TEXT,
    created_at TEXT NOT NULL
);

CREATE TABLE IF NOT EXISTS vaults (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    bank_name TEXT NOT NULL,
    balance REAL NOT NULL DEFAULT 0,
    created_at TEXT NOT NULL
);

CREATE TABLE IF NOT EXISTS vault_transactions (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    transaction_type TEXT NOT NULL,
    source_vault_id INTEGER,
    target_vault_id INTEGER,
    amount REAL NOT NULL DEFAULT 0,
    notes TEXT,
    transaction_date TEXT NOT NULL,
    created_at TEXT NOT NULL
);

CREATE TABLE IF NOT EXISTS sales (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    invoice_no TEXT NOT NULL UNIQUE,
    customer_id INTEGER,
    payment_type TEXT NOT NULL,
    vault_id INTEGER,
    subtotal REAL NOT NULL DEFAULT 0,
    total_profit REAL NOT NULL DEFAULT 0,
    total_paid REAL NOT NULL DEFAULT 0,
    notes TEXT,
    transaction_date TEXT NOT NULL,
    created_at TEXT NOT NULL
);

CREATE TABLE IF NOT EXISTS sale_items (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    sale_id INTEGER NOT NULL,
    item_id INTEGER NOT NULL,
    vault_id INTEGER,
    qty INTEGER NOT NULL,
    purchase_price REAL NOT NULL DEFAULT 0,
    selling_price REAL NOT NULL DEFAULT 0,
    line_total REAL NOT NULL DEFAULT 0,
    line_profit REAL NOT NULL DEFAULT 0
);

CREATE TABLE IF NOT EXISTS debts (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    sale_id INTEGER NOT NULL,
    customer_id INTEGER,
    total_debt REAL NOT NULL DEFAULT 0,
    paid_amount REAL NOT NULL DEFAULT 0,
    due_date TEXT,
    status TEXT NOT NULL DEFAULT 'Belum Lunas',
    created_at TEXT NOT NULL
);

CREATE TABLE IF NOT EXISTS debt_payments (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    debt_id INTEGER NOT NULL,
    vault_id INTEGER,
    amount REAL NOT NULL,
    payment_date TEXT NOT NULL,
    notes TEXT
);

CREATE TABLE IF NOT EXISTS service_transactions (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    code TEXT NOT NULL UNIQUE,
    service_type TEXT NOT NULL,
    customer_id INTEGER,
    customer_name TEXT,
    customer_phone TEXT,
    target_number TEXT NOT NULL,
    nominal REAL NOT NULL DEFAULT 0,
    buy_price REAL NOT NULL DEFAULT 0,
    sell_price REAL NOT NULL DEFAULT 0,
    profit REAL NOT NULL DEFAULT 0,
    payment_type TEXT NOT NULL,
    vault_id INTEGER,
    token_number TEXT,
    transaction_date TEXT NOT NULL,
    created_at TEXT NOT NULL
);

CREATE TABLE IF NOT EXISTS item_receives (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    item_id INTEGER NOT NULL,
    qty_large INTEGER NOT NULL DEFAULT 0,
    qty_small INTEGER NOT NULL DEFAULT 0,
    qty_total INTEGER NOT NULL DEFAULT 0,
    purchase_price REAL NOT NULL DEFAULT 0,
    purchase_total REAL NOT NULL DEFAULT 0,
    notes TEXT,
    transaction_date TEXT NOT NULL,
    created_at TEXT NOT NULL
);

CREATE TABLE IF NOT EXISTS stock_opnames (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    item_id INTEGER NOT NULL,
    before_stock INTEGER NOT NULL DEFAULT 0,
    actual_stock INTEGER NOT NULL DEFAULT 0,
    adjustment INTEGER NOT NULL DEFAULT 0,
    notes TEXT,
    transaction_date TEXT NOT NULL,
    created_at TEXT NOT NULL
);
