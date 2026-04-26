<?php
declare(strict_types=1);

class App
{
    private string $basePath;
    private array $config;

    public function __construct(string $basePath)
    {
        $this->basePath = $basePath;
        $this->config = require $this->basePath . '/application/config/config.php';
        date_default_timezone_set($this->config['default_timezone']);
        $this->ensureStorage();
        $this->initializeDatabase();
    }

    public function run(): void
    {
        $route = $_GET['route'] ?? 'dashboard';
        $segments = array_values(array_filter(explode('/', trim((string) $route, '/'))));

        $controllerName = ucfirst($segments[0] ?? 'dashboard') . '_controller';
        $method = $segments[1] ?? 'index';

        $controllerFile = $this->basePath . '/application/controllers/' . $controllerName . '.php';
        if (!file_exists($controllerFile)) {
            http_response_code(404);
            echo 'Halaman tidak ditemukan.';
            return;
        }

        require_once $controllerFile;

        if (!class_exists($controllerName)) {
            http_response_code(500);
            echo 'Controller tidak tersedia.';
            return;
        }

        $controller = new $controllerName($this->basePath, $this->config);
        if (!method_exists($controller, $method)) {
            http_response_code(404);
            echo 'Method tidak ditemukan.';
            return;
        }

        $controller->{$method}();
    }

    private function ensureStorage(): void
    {
        $directories = [
            $this->basePath . '/storage',
            $this->basePath . '/storage/exports',
            $this->basePath . '/storage/uploads',
        ];

        foreach ($directories as $directory) {
            if (!is_dir($directory)) {
                mkdir($directory, 0777, true);
            }
        }
    }

    private function initializeDatabase(): void
    {
        $dbPath = $this->basePath . '/storage/kasir.sqlite';
        $pdo = new PDO('sqlite:' . $dbPath);
        $schema = file_get_contents($this->basePath . '/database/schema.sql');
        $pdo->exec((string) $schema);
        $this->migrateDatabase($pdo);
    }

    private function migrateDatabase(PDO $pdo): void
    {
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

        $saleItemColumns = $pdo->query("PRAGMA table_info(sale_items)")->fetchAll(PDO::FETCH_ASSOC);
        $saleItemColumnNames = array_column($saleItemColumns, 'name');
        if (!empty($saleItemColumnNames) && !in_array('vault_id', $saleItemColumnNames, true)) {
            $pdo->exec("ALTER TABLE sale_items ADD COLUMN vault_id INTEGER");
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
    }
}
