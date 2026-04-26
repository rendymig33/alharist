<?php
declare(strict_types=1);

require_once __DIR__ . '/Database.php';

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
        Database::initialize($this->basePath, $this->config);
    }
}
