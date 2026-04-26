<?php
declare(strict_types=1);

class Controller
{
    protected string $basePath;
    protected array $config;

    public function __construct(string $basePath, array $config)
    {
        $this->basePath = $basePath;
        $this->config = $config;
    }

    protected function model(string $model): object
    {
        $file = $this->basePath . '/application/models/' . $model . '.php';
        require_once $file;
        return new $model($this->basePath, $this->config);
    }

    protected function view(string $view, array $data = []): void
    {
        extract($data);
        $config = $this->config;
        $currentRoute = $_GET['route'] ?? 'dashboard';
        require $this->basePath . '/application/views/layout/header.php';
        require $this->basePath . '/application/views/' . $view . '.php';
        require $this->basePath . '/application/views/layout/footer.php';
    }

    protected function redirect(string $route): void
    {
        header('Location: index.php?route=' . $route);
        exit;
    }
}
