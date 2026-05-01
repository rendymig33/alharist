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

    public function save(array $data): void
    {
        $payload = [
            'code' => trim((string) ($data['code'] ?? '')),
            'barcode' => '',
            'name' => trim((string) ($data['name'] ?? '')),
            'category' => 'E-SALDO',
            'description' => trim((string) ($data['description'] ?? '')),
            'unit_large' => 'Transaksi',
            'unit_small' => 'Transaksi',
            'small_unit_qty' => 1,
            'purchase_price' => (float) ($data['purchase_price'] ?? 0),
            'purchase_total' => (float) ($data['purchase_price'] ?? 0),
            'purchase_basis_qty' => 1,
            'selling_price' => (float) ($data['selling_price'] ?? 0),
            'profit_percent' => 0,
            'unit_price' => (float) ($data['selling_price'] ?? 0),
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
        ];

        if (!empty($data['id'])) {
            $sql = "UPDATE items SET
                code = :code,
                barcode = :barcode,
                name = :name,
                category = :category,
                description = :description,
                unit_large = :unit_large,
                unit_small = :unit_small,
                small_unit_qty = :small_unit_qty,
                purchase_price = :purchase_price,
                purchase_total = :purchase_total,
                purchase_basis_qty = :purchase_basis_qty,
                selling_price = :selling_price,
                profit_percent = :profit_percent,
                unit_price = :unit_price,
                half_price = :half_price,
                allow_small_sale = :allow_small_sale,
                allow_half_sale = :allow_half_sale,
                promo_qty_1 = :promo_qty_1,
                promo_price_1 = :promo_price_1,
                promo_qty_2 = :promo_qty_2,
                promo_price_2 = :promo_price_2,
                promo_qty_3 = :promo_qty_3,
                promo_price_3 = :promo_price_3,
                promo_qty_4 = :promo_qty_4,
                promo_price_4 = :promo_price_4,
                promo_qty_5 = :promo_qty_5,
                promo_price_5 = :promo_price_5,
                promo_qty_6 = :promo_qty_6,
                promo_price_6 = :promo_price_6,
                stock = :stock,
                exp_date = :exp_date
                WHERE id = :id";
            $payload['id'] = (int) $data['id'];
        } else {
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
            $payload['created_at'] = date('Y-m-d H:i:s');
        }

        $statement = $this->db->prepare($sql);
        $statement->execute($payload);
    }

    public function delete(int $id): bool
    {
        $statement = $this->db->prepare("DELETE FROM items WHERE id = :id AND category = 'E-SALDO'");
        return $statement->execute(['id' => $id]);
    }
}
