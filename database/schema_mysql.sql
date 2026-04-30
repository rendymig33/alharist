CREATE TABLE IF NOT EXISTS items (
    id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
    code VARCHAR(50) NOT NULL,
    barcode VARCHAR(100) NULL,
    name VARCHAR(255) NOT NULL,
    category VARCHAR(150) NULL,
    description TEXT NULL,
    unit_large VARCHAR(100) NOT NULL,
    unit_small VARCHAR(100) NOT NULL,
    small_unit_qty INT NOT NULL DEFAULT 1,
    purchase_price DECIMAL(15,2) NOT NULL DEFAULT 0,
    purchase_total DECIMAL(15,2) NOT NULL DEFAULT 0,
    purchase_basis_qty INT NOT NULL DEFAULT 0,
    selling_price DECIMAL(15,2) NOT NULL DEFAULT 0,
    profit_percent DECIMAL(8,2) NOT NULL DEFAULT 0,
    unit_price DECIMAL(15,2) NOT NULL DEFAULT 0,
    half_price DECIMAL(15,2) NOT NULL DEFAULT 0,
    allow_small_sale TINYINT(1) NOT NULL DEFAULT 1,
    allow_half_sale TINYINT(1) NOT NULL DEFAULT 1,
    promo_qty_1 INT NOT NULL DEFAULT 0,
    promo_price_1 DECIMAL(15,2) NOT NULL DEFAULT 0,
    promo_qty_2 INT NOT NULL DEFAULT 0,
    promo_price_2 DECIMAL(15,2) NOT NULL DEFAULT 0,
    promo_qty_3 INT NOT NULL DEFAULT 0,
    promo_price_3 DECIMAL(15,2) NOT NULL DEFAULT 0,
    promo_qty_4 INT NOT NULL DEFAULT 0,
    promo_price_4 DECIMAL(15,2) NOT NULL DEFAULT 0,
    promo_qty_5 INT NOT NULL DEFAULT 0,
    promo_price_5 DECIMAL(15,2) NOT NULL DEFAULT 0,
    promo_qty_6 INT NOT NULL DEFAULT 0,
    promo_price_6 DECIMAL(15,2) NOT NULL DEFAULT 0,
    stock INT NOT NULL DEFAULT 0,
    exp_date DATE NULL,
    created_at DATETIME NOT NULL,
    PRIMARY KEY (id),
    UNIQUE KEY uq_items_code (code),
    KEY idx_items_barcode (barcode),
    KEY idx_items_name (name)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS customers (
    id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
    code VARCHAR(50) NOT NULL,
    name VARCHAR(255) NOT NULL,
    phone VARCHAR(100) NULL,
    address TEXT NULL,
    created_at DATETIME NOT NULL,
    PRIMARY KEY (id),
    UNIQUE KEY uq_customers_code (code)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS vaults (
    id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
    bank_name VARCHAR(150) NOT NULL,
    account_name VARCHAR(150) NULL,
    balance DECIMAL(15,2) NOT NULL DEFAULT 0,
    created_at DATETIME NOT NULL,
    PRIMARY KEY (id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS vault_transactions (
    id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
    transaction_type VARCHAR(100) NOT NULL,
    source_vault_id BIGINT UNSIGNED NULL,
    target_vault_id BIGINT UNSIGNED NULL,
    amount DECIMAL(15,2) NOT NULL DEFAULT 0,
    notes TEXT NULL,
    transaction_date DATE NOT NULL,
    created_at DATETIME NOT NULL,
    PRIMARY KEY (id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS sales (
    id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
    invoice_no VARCHAR(100) NOT NULL,
    customer_id BIGINT UNSIGNED NULL,
    payment_type VARCHAR(50) NOT NULL,
    vault_id BIGINT UNSIGNED NULL,
    subtotal DECIMAL(15,2) NOT NULL DEFAULT 0,
    total_profit DECIMAL(15,2) NOT NULL DEFAULT 0,
    total_paid DECIMAL(15,2) NOT NULL DEFAULT 0,
    notes TEXT NULL,
    transaction_date DATE NOT NULL,
    created_at DATETIME NOT NULL,
    PRIMARY KEY (id),
    UNIQUE KEY uq_sales_invoice_no (invoice_no),
    KEY idx_sales_transaction_date (transaction_date)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS sale_items (
    id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
    sale_id BIGINT UNSIGNED NOT NULL,
    item_id BIGINT UNSIGNED NOT NULL,
    vault_id BIGINT UNSIGNED NULL,
    qty INT NOT NULL,
    purchase_price DECIMAL(15,2) NOT NULL DEFAULT 0,
    selling_price DECIMAL(15,2) NOT NULL DEFAULT 0,
    line_total DECIMAL(15,2) NOT NULL DEFAULT 0,
    line_profit DECIMAL(15,2) NOT NULL DEFAULT 0,
    PRIMARY KEY (id),
    KEY idx_sale_items_sale_id (sale_id),
    KEY idx_sale_items_item_id (item_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS debts (
    id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
    sale_id BIGINT UNSIGNED NOT NULL,
    customer_id BIGINT UNSIGNED NULL,
    total_debt DECIMAL(15,2) NOT NULL DEFAULT 0,
    paid_amount DECIMAL(15,2) NOT NULL DEFAULT 0,
    due_date DATE NULL,
    status VARCHAR(50) NOT NULL DEFAULT 'Belum Lunas',
    created_at DATETIME NOT NULL,
    PRIMARY KEY (id),
    KEY idx_debts_sale_id (sale_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS debt_payments (
    id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
    debt_id BIGINT UNSIGNED NOT NULL,
    vault_id BIGINT UNSIGNED NULL,
    amount DECIMAL(15,2) NOT NULL,
    payment_date DATE NOT NULL,
    notes TEXT NULL,
    PRIMARY KEY (id),
    KEY idx_debt_payments_debt_id (debt_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS service_transactions (
    id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
    code VARCHAR(100) NOT NULL,
    service_type VARCHAR(100) NOT NULL,
    customer_id BIGINT UNSIGNED NULL,
    customer_name VARCHAR(255) NULL,
    customer_phone VARCHAR(100) NULL,
    target_number VARCHAR(100) NOT NULL,
    nominal DECIMAL(15,2) NOT NULL DEFAULT 0,
    buy_price DECIMAL(15,2) NOT NULL DEFAULT 0,
    sell_price DECIMAL(15,2) NOT NULL DEFAULT 0,
    profit DECIMAL(15,2) NOT NULL DEFAULT 0,
    payment_type VARCHAR(50) NOT NULL,
    vault_id BIGINT UNSIGNED NULL,
    token_number VARCHAR(100) NULL,
    transaction_date DATE NOT NULL,
    created_at DATETIME NOT NULL,
    PRIMARY KEY (id),
    UNIQUE KEY uq_service_transactions_code (code)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS item_receives (
    id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
    item_id BIGINT UNSIGNED NOT NULL,
    qty_large INT NOT NULL DEFAULT 0,
    qty_small INT NOT NULL DEFAULT 0,
    qty_total INT NOT NULL DEFAULT 0,
    purchase_price DECIMAL(15,2) NOT NULL DEFAULT 0,
    purchase_total DECIMAL(15,2) NOT NULL DEFAULT 0,
    notes TEXT NULL,
    transaction_date DATE NOT NULL,
    created_at DATETIME NOT NULL,
    PRIMARY KEY (id),
    KEY idx_item_receives_item_id (item_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS stock_opnames (
    id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
    item_id BIGINT UNSIGNED NOT NULL,
    before_stock INT NOT NULL DEFAULT 0,
    actual_stock INT NOT NULL DEFAULT 0,
    adjustment INT NOT NULL DEFAULT 0,
    notes TEXT NULL,
    transaction_date DATE NOT NULL,
    created_at DATETIME NOT NULL,
    PRIMARY KEY (id),
    KEY idx_stock_opnames_item_id (item_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
