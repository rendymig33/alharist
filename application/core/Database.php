<?php
declare(strict_types=1);

class Database
{
    public static function createPdo(string $basePath, array $config): PDO
    {
        $database = $config['database'] ?? [];
        $driver = strtolower((string) ($database['driver'] ?? 'sqlite'));

        if ($driver === 'mysql') {
            $host = (string) ($database['host'] ?? '127.0.0.1');
            $port = (int) ($database['port'] ?? 3306);
            $name = (string) ($database['name'] ?? 'alharist');
            $charset = (string) ($database['charset'] ?? 'utf8mb4');
            $username = (string) ($database['username'] ?? 'root');
            $password = (string) ($database['password'] ?? '');
            $dsn = "mysql:host={$host};port={$port};dbname={$name};charset={$charset}";
            $pdo = new PDO($dsn, $username, $password, [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            ]);
            $pdo->exec("SET NAMES {$charset}");
            return $pdo;
        }

        $sqlitePath = (string) ($database['sqlite_path'] ?? ($basePath . '/storage/kasir.sqlite'));
        $pdo = new PDO('sqlite:' . $sqlitePath);
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        $pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
        return $pdo;
    }

    public static function initialize(string $basePath, array $config): void
    {
        $pdo = self::createPdo($basePath, $config);
        $driver = strtolower((string) (($config['database']['driver'] ?? 'sqlite')));
        $schemaFile = $driver === 'mysql'
            ? $basePath . '/database/schema_mysql.sql'
            : $basePath . '/database/schema.sql';

        $schema = file_get_contents($schemaFile);
        if ($schema !== false) {
            $pdo->exec($schema);
        }

        if ($driver === 'mysql') {
            self::migrateMysql($pdo);
            return;
        }

        self::migrateSqlite($pdo);
    }

    private static function migrateSqlite(PDO $pdo): void
    {
        self::dropSqliteVaultAccountName($pdo);

        $columns = $pdo->query("PRAGMA table_info(items)")->fetchAll(PDO::FETCH_ASSOC);
        $columnNames = array_column($columns, 'name');

        if (!in_array('small_unit_qty', $columnNames, true)) {
            $pdo->exec("ALTER TABLE items ADD COLUMN small_unit_qty INTEGER NOT NULL DEFAULT 1");
        }
        if (!in_array('description', $columnNames, true)) {
            $pdo->exec("ALTER TABLE items ADD COLUMN description TEXT");
        }
        if (!in_array('barcode', $columnNames, true)) {
            $pdo->exec("ALTER TABLE items ADD COLUMN barcode TEXT");
        }
        if (!in_array('purchase_total', $columnNames, true)) {
            $pdo->exec("ALTER TABLE items ADD COLUMN purchase_total REAL NOT NULL DEFAULT 0");
        }
        if (!in_array('purchase_basis_qty', $columnNames, true)) {
            $pdo->exec("ALTER TABLE items ADD COLUMN purchase_basis_qty INTEGER NOT NULL DEFAULT 0");
        }
        if (!in_array('half_price', $columnNames, true)) {
            $pdo->exec("ALTER TABLE items ADD COLUMN half_price REAL NOT NULL DEFAULT 0");
        }
        if (!in_array('allow_small_sale', $columnNames, true)) {
            $pdo->exec("ALTER TABLE items ADD COLUMN allow_small_sale INTEGER NOT NULL DEFAULT 1");
        }
        if (!in_array('allow_half_sale', $columnNames, true)) {
            $pdo->exec("ALTER TABLE items ADD COLUMN allow_half_sale INTEGER NOT NULL DEFAULT 1");
        }
        if (!in_array('promo_qty_1', $columnNames, true)) {
            $pdo->exec("ALTER TABLE items ADD COLUMN promo_qty_1 INTEGER NOT NULL DEFAULT 0");
        }
        if (!in_array('promo_price_1', $columnNames, true)) {
            $pdo->exec("ALTER TABLE items ADD COLUMN promo_price_1 REAL NOT NULL DEFAULT 0");
        }
        if (!in_array('promo_qty_2', $columnNames, true)) {
            $pdo->exec("ALTER TABLE items ADD COLUMN promo_qty_2 INTEGER NOT NULL DEFAULT 0");
        }
        if (!in_array('promo_price_2', $columnNames, true)) {
            $pdo->exec("ALTER TABLE items ADD COLUMN promo_price_2 REAL NOT NULL DEFAULT 0");
        }
        if (!in_array('promo_qty_3', $columnNames, true)) {
            $pdo->exec("ALTER TABLE items ADD COLUMN promo_qty_3 INTEGER NOT NULL DEFAULT 0");
        }
        if (!in_array('promo_price_3', $columnNames, true)) {
            $pdo->exec("ALTER TABLE items ADD COLUMN promo_price_3 REAL NOT NULL DEFAULT 0");
        }
        if (!in_array('promo_qty_4', $columnNames, true)) {
            $pdo->exec("ALTER TABLE items ADD COLUMN promo_qty_4 INTEGER NOT NULL DEFAULT 0");
        }
        if (!in_array('promo_price_4', $columnNames, true)) {
            $pdo->exec("ALTER TABLE items ADD COLUMN promo_price_4 REAL NOT NULL DEFAULT 0");
        }
        if (!in_array('promo_qty_5', $columnNames, true)) {
            $pdo->exec("ALTER TABLE items ADD COLUMN promo_qty_5 INTEGER NOT NULL DEFAULT 0");
        }
        if (!in_array('promo_price_5', $columnNames, true)) {
            $pdo->exec("ALTER TABLE items ADD COLUMN promo_price_5 REAL NOT NULL DEFAULT 0");
        }
        if (!in_array('promo_qty_6', $columnNames, true)) {
            $pdo->exec("ALTER TABLE items ADD COLUMN promo_qty_6 INTEGER NOT NULL DEFAULT 0");
        }
        if (!in_array('promo_price_6', $columnNames, true)) {
            $pdo->exec("ALTER TABLE items ADD COLUMN promo_price_6 REAL NOT NULL DEFAULT 0");
        }

        $saleItemColumns = $pdo->query("PRAGMA table_info(sale_items)")->fetchAll(PDO::FETCH_ASSOC);
        $saleItemColumnNames = array_column($saleItemColumns, 'name');
        if (!empty($saleItemColumnNames) && !in_array('vault_id', $saleItemColumnNames, true)) {
            $pdo->exec("ALTER TABLE sale_items ADD COLUMN vault_id INTEGER");
        }

        $debtPaymentColumns = $pdo->query("PRAGMA table_info(debt_payments)")->fetchAll(PDO::FETCH_ASSOC);
        $debtPaymentColumnNames = array_column($debtPaymentColumns, 'name');
        if (!empty($debtPaymentColumnNames) && !in_array('vault_id', $debtPaymentColumnNames, true)) {
            $pdo->exec("ALTER TABLE debt_payments ADD COLUMN vault_id INTEGER");
        }

        $serviceColumns = $pdo->query("PRAGMA table_info(service_transactions)")->fetchAll(PDO::FETCH_ASSOC);
        $serviceColumnNames = array_column($serviceColumns, 'name');
        if (!empty($serviceColumnNames)) {
            if (!in_array('customer_id', $serviceColumnNames, true)) {
                $pdo->exec("ALTER TABLE service_transactions ADD COLUMN customer_id INTEGER");
            }
            if (!in_array('token_number', $serviceColumnNames, true)) {
                $pdo->exec("ALTER TABLE service_transactions ADD COLUMN token_number TEXT");
            }
        }

        $salesColumns = $pdo->query("PRAGMA table_info(sales)")->fetchAll(PDO::FETCH_ASSOC);
        $salesColumnNames = array_column($salesColumns, 'name');
        if (!empty($salesColumnNames) && !in_array('shift', $salesColumnNames, true)) {
            $pdo->exec("ALTER TABLE sales ADD COLUMN shift INTEGER DEFAULT 1");
        }

        $pdo->exec("
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
            )
        ");

        $pdo->exec("
            CREATE TABLE IF NOT EXISTS stock_opnames (
                id INTEGER PRIMARY KEY AUTOINCREMENT,
                item_id INTEGER NOT NULL,
                before_stock INTEGER NOT NULL DEFAULT 0,
                actual_stock INTEGER NOT NULL DEFAULT 0,
                adjustment INTEGER NOT NULL DEFAULT 0,
                notes TEXT,
                transaction_date TEXT NOT NULL,
                created_at TEXT NOT NULL
            )
        ");

        $pdo->exec("
            CREATE TABLE IF NOT EXISTS vault_pecahan (
                vault_id INTEGER NOT NULL PRIMARY KEY,
                pecahan_json TEXT NOT NULL,
                updated_at TEXT NOT NULL
            )
        ");
    }

    private static function migrateMysql(PDO $pdo): void
    {
        self::dropMysqlVaultAccountName($pdo);

        self::ensureMysqlColumn($pdo, 'items', 'description', "ALTER TABLE items ADD COLUMN description TEXT NULL");
        self::ensureMysqlColumn($pdo, 'items', 'barcode', "ALTER TABLE items ADD COLUMN barcode VARCHAR(100) NULL");
        self::ensureMysqlColumn($pdo, 'items', 'small_unit_qty', "ALTER TABLE items ADD COLUMN small_unit_qty INT NOT NULL DEFAULT 1");
        self::ensureMysqlColumn($pdo, 'items', 'purchase_total', "ALTER TABLE items ADD COLUMN purchase_total DECIMAL(15,2) NOT NULL DEFAULT 0");
        self::ensureMysqlColumn($pdo, 'items', 'purchase_basis_qty', "ALTER TABLE items ADD COLUMN purchase_basis_qty INT NOT NULL DEFAULT 0");
        self::ensureMysqlColumn($pdo, 'items', 'half_price', "ALTER TABLE items ADD COLUMN half_price DECIMAL(15,2) NOT NULL DEFAULT 0");
        self::ensureMysqlColumn($pdo, 'items', 'allow_small_sale', "ALTER TABLE items ADD COLUMN allow_small_sale TINYINT(1) NOT NULL DEFAULT 1");
        self::ensureMysqlColumn($pdo, 'items', 'allow_half_sale', "ALTER TABLE items ADD COLUMN allow_half_sale TINYINT(1) NOT NULL DEFAULT 1");
        self::ensureMysqlColumn($pdo, 'items', 'promo_qty_1', "ALTER TABLE items ADD COLUMN promo_qty_1 INT NOT NULL DEFAULT 0");
        self::ensureMysqlColumn($pdo, 'items', 'promo_price_1', "ALTER TABLE items ADD COLUMN promo_price_1 DECIMAL(15,2) NOT NULL DEFAULT 0");
        self::ensureMysqlColumn($pdo, 'items', 'promo_qty_2', "ALTER TABLE items ADD COLUMN promo_qty_2 INT NOT NULL DEFAULT 0");
        self::ensureMysqlColumn($pdo, 'items', 'promo_price_2', "ALTER TABLE items ADD COLUMN promo_price_2 DECIMAL(15,2) NOT NULL DEFAULT 0");
        self::ensureMysqlColumn($pdo, 'items', 'promo_qty_3', "ALTER TABLE items ADD COLUMN promo_qty_3 INT NOT NULL DEFAULT 0");
        self::ensureMysqlColumn($pdo, 'items', 'promo_price_3', "ALTER TABLE items ADD COLUMN promo_price_3 DECIMAL(15,2) NOT NULL DEFAULT 0");
        self::ensureMysqlColumn($pdo, 'items', 'promo_qty_4', "ALTER TABLE items ADD COLUMN promo_qty_4 INT NOT NULL DEFAULT 0");
        self::ensureMysqlColumn($pdo, 'items', 'promo_price_4', "ALTER TABLE items ADD COLUMN promo_price_4 DECIMAL(15,2) NOT NULL DEFAULT 0");
        self::ensureMysqlColumn($pdo, 'items', 'promo_qty_5', "ALTER TABLE items ADD COLUMN promo_qty_5 INT NOT NULL DEFAULT 0");
        self::ensureMysqlColumn($pdo, 'items', 'promo_price_5', "ALTER TABLE items ADD COLUMN promo_price_5 DECIMAL(15,2) NOT NULL DEFAULT 0");
        self::ensureMysqlColumn($pdo, 'items', 'promo_qty_6', "ALTER TABLE items ADD COLUMN promo_qty_6 INT NOT NULL DEFAULT 0");
        self::ensureMysqlColumn($pdo, 'items', 'promo_price_6', "ALTER TABLE items ADD COLUMN promo_price_6 DECIMAL(15,2) NOT NULL DEFAULT 0");
        self::ensureMysqlColumn($pdo, 'sale_items', 'vault_id', "ALTER TABLE sale_items ADD COLUMN vault_id BIGINT UNSIGNED NULL");
        self::ensureMysqlColumn($pdo, 'debt_payments', 'vault_id', "ALTER TABLE debt_payments ADD COLUMN vault_id BIGINT UNSIGNED NULL");
        self::ensureMysqlColumn($pdo, 'service_transactions', 'customer_id', "ALTER TABLE service_transactions ADD COLUMN customer_id BIGINT UNSIGNED NULL");
        self::ensureMysqlColumn($pdo, 'service_transactions', 'token_number', "ALTER TABLE service_transactions ADD COLUMN token_number VARCHAR(100) NULL");
        self::ensureMysqlColumn($pdo, 'sales', 'shift', "ALTER TABLE sales ADD COLUMN shift INT DEFAULT 1");
        $pdo->exec("
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
            )
        ");
        $pdo->exec("
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
            )
        ");

        $pdo->exec("
            CREATE TABLE IF NOT EXISTS vault_pecahan (
                vault_id BIGINT UNSIGNED NOT NULL PRIMARY KEY,
                pecahan_json TEXT NOT NULL,
                updated_at DATETIME NOT NULL
            )
        ");
    }

    private static function ensureMysqlColumn(PDO $pdo, string $table, string $column, string $sql): void
    {
        $statement = $pdo->prepare("
            SELECT COUNT(*) 
            FROM INFORMATION_SCHEMA.COLUMNS 
            WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = :table_name AND COLUMN_NAME = :column_name
        ");
        $statement->execute([
            'table_name' => $table,
            'column_name' => $column,
        ]);

        if ((int) $statement->fetchColumn() === 0) {
            $pdo->exec($sql);
        }
    }

    private static function dropSqliteVaultAccountName(PDO $pdo): void
    {
        $columns = $pdo->query("PRAGMA table_info(vaults)")->fetchAll(PDO::FETCH_ASSOC);
        $columnNames = array_column($columns, 'name');
        if (!in_array('account_name', $columnNames, true)) {
            return;
        }

        $pdo->beginTransaction();
        $pdo->exec("
            CREATE TABLE IF NOT EXISTS vaults_tmp (
                id INTEGER PRIMARY KEY AUTOINCREMENT,
                bank_name TEXT NOT NULL,
                balance REAL NOT NULL DEFAULT 0,
                created_at TEXT NOT NULL
            )
        ");
        $pdo->exec("
            INSERT INTO vaults_tmp (id, bank_name, balance, created_at)
            SELECT id, bank_name, balance, created_at
            FROM vaults
        ");
        $pdo->exec("DROP TABLE vaults");
        $pdo->exec("ALTER TABLE vaults_tmp RENAME TO vaults");
        $pdo->commit();
    }

    private static function dropMysqlVaultAccountName(PDO $pdo): void
    {
        $statement = $pdo->prepare("
            SELECT COUNT(*)
            FROM INFORMATION_SCHEMA.COLUMNS
            WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'vaults' AND COLUMN_NAME = 'account_name'
        ");
        $statement->execute();
        if ((int) $statement->fetchColumn() === 0) {
            return;
        }

        $pdo->exec("ALTER TABLE vaults DROP COLUMN account_name");
    }
}
