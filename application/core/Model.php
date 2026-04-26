<?php
declare(strict_types=1);

class Model
{
    protected PDO $db;
    protected string $basePath;
    protected array $config;

    public function __construct(string $basePath, array $config)
    {
        $this->basePath = $basePath;
        $this->config = $config;
        $this->db = new PDO('sqlite:' . $this->basePath . '/storage/kasir.sqlite');
        $this->db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    }
}
