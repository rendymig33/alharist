<?php

declare(strict_types=1);

class Esaldo_model extends Model
{
    public function nextCode(): string
    {
        $lastCode = (string) $this->db->query("SELECT code FROM items WHERE category = 'E-SALDO' AND code LIKE 'ESL%' ORDER BY id DESC LIMIT 1")->fetchColumn();
        $lastNumber = 0;

        if ($lastCode !== '' && preg_match('/ESL(\d+)/', $lastCode, $matches)) {
            $lastNumber = (int) $matches[1];
        }

        return 'ESL' . str_pad((string) ($lastNumber + 1), 4, '0', STR_PAD_LEFT);
    }

    public function allBalances(): array
    {
        $sql = "SELECT id, code, name, selling_price AS balance, created_at FROM items WHERE category = 'E-SALDO' ORDER BY id DESC";
        $statement = $this->db->prepare($sql);
        $statement->execute();
        return $statement->fetchAll(PDO::FETCH_ASSOC);
    }

    public function findBalance(int $id): array|false
    {
        $statement = $this->db->prepare("SELECT id, code, name, selling_price AS balance, created_at FROM items WHERE id = :id AND category = 'E-SALDO'");
        $statement->execute(['id' => $id]);
        return $statement->fetch(PDO::FETCH_ASSOC);
    }

    public function all(string $keyword = ''): array
    {
        $sql = "SELECT * FROM items WHERE category = 'E-SALDO'";
        $params = [];
        $keyword = trim($keyword);

        if ($keyword !== '') {
            $sql .= " AND (code LIKE :keyword OR name LIKE :keyword)";
            $params['keyword'] = '%' . $keyword . '%';
        }

        $sql .= " ORDER BY name ASC";
        $statement = $this->db->prepare($sql);
        $statement->execute($params);
        return $statement->fetchAll(PDO::FETCH_ASSOC);
    }

    public function searchForTransaction(string $keyword = ''): array
    {
        return $this->all($keyword);
    }

    public function find(int $id): array|false
    {
        $statement = $this->db->prepare("SELECT * FROM items WHERE id = :id AND category = 'E-SALDO'");
        $statement->execute(['id' => $id]);
        return $statement->fetch(PDO::FETCH_ASSOC);
    }

    public function save(array $data): int
    {
        $balance = (float) ($data['balance'] ?? 0);
        $name = trim((string) ($data['name'] ?? 'E-Saldo'));

        if (!empty($data['id'])) {
            $id = (int) $data['id'];
            $sql = "UPDATE items SET name = :name, selling_price = :balance, unit_price = :balance WHERE id = :id AND category = 'E-SALDO'";
            $statement = $this->db->prepare($sql);
            $statement->execute([
                'name' => $name,
                'balance' => $balance,
                'id' => $id,
            ]);
            return $id;
        } else {
            $code = $this->nextCode();
            $payload = [
                // ... same payload ...
                'code' => $code,
                'barcode' => '',
                'name' => $name,
                'category' => 'E-SALDO',
                'description' => '',
                'unit_large' => 'Transaksi',
                'unit_small' => 'Transaksi',
                'small_unit_qty' => 1,
                'purchase_price' => 0,
                'purchase_total' => 0,
                'purchase_basis_qty' => 1,
                'selling_price' => $balance,
                'profit_percent' => 0,
                'unit_price' => $balance,
                'half_price' => 0,
                'allow_small_sale' => 0,
                'allow_half_sale' => 0,
                'promo_qty_1' => 0,
                'promo_price_1' => 0,
                'promo_qty_2' => 0,
                'promo_price_2' => 0,
                'promo_qty_3' => 0,
                'promo_price_3' => 0,
                'promo_qty_4' => 0,
                'promo_price_4' => 0,
                'promo_qty_5' => 0,
                'promo_price_5' => 0,
                'promo_qty_6' => 0,
                'promo_price_6' => 0,
                'stock' => 0,
                'exp_date' => null,
                'created_at' => date('Y-m-d H:i:s'),
            ];

            $sql = "INSERT INTO items (
                code, barcode, name, category, description, unit_large, unit_small, small_unit_qty,
                purchase_price, purchase_total, purchase_basis_qty, selling_price, profit_percent, unit_price, half_price,
                allow_small_sale, allow_half_sale, promo_qty_1, promo_price_1, promo_qty_2, promo_price_2,
                promo_qty_3, promo_price_3, promo_qty_4, promo_price_4, promo_qty_5, promo_price_5,
                promo_qty_6, promo_price_6, stock, exp_date, created_at
            ) VALUES (
                :code, :barcode, :name, :category, :description, :unit_large, :unit_small, :small_unit_qty,
                :purchase_price, :purchase_total, :purchase_basis_qty, :selling_price, :profit_percent, :unit_price, :half_price,
                :allow_small_sale, :allow_half_sale, :promo_qty_1, :promo_price_1, :promo_qty_2, :promo_price_2,
                :promo_qty_3, :promo_price_3, :promo_qty_4, :promo_price_4, :promo_qty_5, :promo_price_5,
                :promo_qty_6, :promo_price_6, :stock, :exp_date, :created_at
            )";
            $statement = $this->db->prepare($sql);
            $statement->execute($payload);
            return (int) $this->db->lastInsertId();
        }
    }

    public function delete(int $id): bool
    {
        $statement = $this->db->prepare("DELETE FROM items WHERE id = :id AND category = 'E-SALDO'");
        return $statement->execute(['id' => $id]);
    }

    public function getHistory(int $esaldoId): array
    {
        $sql = "
            SELECT 
                s.invoice_no,
                s.transaction_date as date,
                si.qty,
                si.selling_price as price,
                si.line_total as total,
                COALESCE(s.notes, '') as notes
            FROM sale_items si
            JOIN sales s ON s.id = si.sale_id
            WHERE si.item_id = :id
            ORDER BY s.transaction_date DESC, s.id DESC
        ";
        $statement = $this->db->prepare($sql);
        $statement->execute(['id' => $esaldoId]);
        return $statement->fetchAll(PDO::FETCH_ASSOC);
    }
}
