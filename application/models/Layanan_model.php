<?php
declare(strict_types=1);

class Layanan_model extends Model
{
    public function nextCode(): string
    {
        $lastCode = (string) $this->db->query("SELECT code FROM service_transactions WHERE code LIKE 'LYN%' ORDER BY id DESC LIMIT 1")->fetchColumn();
        $lastNumber = 0;

        if ($lastCode !== '' && preg_match('/LYN(\d+)/', $lastCode, $matches)) {
            $lastNumber = (int) $matches[1];
        }

        return 'LYN' . str_pad((string) ($lastNumber + 1), 5, '0', STR_PAD_LEFT);
    }

    public function all(): array
    {
        return $this->db->query("SELECT * FROM service_transactions ORDER BY id DESC LIMIT 50")->fetchAll(PDO::FETCH_ASSOC);
    }

    public function save(array $data): void
    {
        $statement = $this->db->prepare("INSERT INTO service_transactions (code, service_type, customer_id, customer_name, customer_phone, target_number, nominal, buy_price, sell_price, profit, payment_type, vault_id, token_number, transaction_date, created_at) VALUES (:code, :service_type, :customer_id, :customer_name, :customer_phone, :target_number, :nominal, :buy_price, :sell_price, :profit, :payment_type, :vault_id, :token_number, :transaction_date, :created_at)");
        $statement->execute([
            'code' => $data['code'],
            'service_type' => $data['service_type'],
            'customer_id' => $data['customer_id'] ?: null,
            'customer_name' => $data['customer_name'],
            'customer_phone' => $data['customer_phone'],
            'target_number' => $data['target_number'],
            'nominal' => $data['nominal'],
            'buy_price' => $data['buy_price'],
            'sell_price' => $data['sell_price'],
            'profit' => $data['profit'],
            'payment_type' => $data['payment_type'],
            'vault_id' => $data['vault_id'] ?: null,
            'token_number' => $data['token_number'],
            'transaction_date' => date('Y-m-d'),
            'created_at' => date('Y-m-d H:i:s'),
        ]);
    }

    public function delete(int $id): ?array
    {
        $statement = $this->db->prepare("SELECT * FROM service_transactions WHERE id = :id");
        $statement->execute(['id' => $id]);
        $service = $statement->fetch(PDO::FETCH_ASSOC);

        if (!$service) {
            return null;
        }

        $delete = $this->db->prepare("DELETE FROM service_transactions WHERE id = :id");
        $delete->execute(['id' => $id]);

        return $service;
    }
}
