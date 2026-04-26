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
}
