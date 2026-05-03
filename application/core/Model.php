<?php
declare(strict_types=1);

require_once __DIR__ . '/Database.php';

class Model
{
    protected PDO $db;
    protected string $basePath;
    protected array $config;

    public function __construct(string $basePath, array $config)
    {
        $this->basePath = $basePath;
        $this->config = $config;
        $this->db = Database::createPdo($this->basePath, $this->config);
    }

    public function getLastClosingAt(): string
    {
        $file = $this->basePath . '/storage/closing.txt';
        if (file_exists($file)) {
            return trim(file_get_contents($file));
        }
        return '2000-01-01 00:00:00';
    }

    public function closeTransaction(): void
    {
        $file = $this->basePath . '/storage/closing.txt';
        file_put_contents($file, date('Y-m-d H:i:s'));
    }
}
