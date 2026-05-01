<?php
declare(strict_types=1);

class Barang_model extends Model
{
    public function nextCode(): string
    {
        $lastCode = (string) $this->db->query("SELECT code FROM items WHERE code LIKE 'BRG%' ORDER BY id DESC LIMIT 1")->fetchColumn();
        $lastNumber = 0;

        if ($lastCode !== '' && preg_match('/BRG(\d+)/', $lastCode, $matches)) {
            $lastNumber = (int) $matches[1];
        }

        return 'BRG' . str_pad((string) ($lastNumber + 1), 4, '0', STR_PAD_LEFT);
    }

    public function all(): array
    {
        $items = $this->db->query("SELECT * FROM items WHERE category <> 'E-SALDO' ORDER BY id DESC")->fetchAll(PDO::FETCH_ASSOC);
        return array_map([$this, 'mapStockDisplay'], $items);
    }

    public function search(string $keyword = ''): array
    {
        $keyword = trim($keyword);
        if ($keyword === '') {
            return $this->all();
        }

        $statement = $this->db->prepare("
            SELECT *
            FROM items
            WHERE category <> 'E-SALDO'
              AND (
                   code LIKE :keyword
               OR barcode LIKE :keyword
               OR name LIKE :keyword
               OR category LIKE :keyword
              )
            ORDER BY id DESC
        ");
        $statement->execute([
            'keyword' => '%' . $keyword . '%',
        ]);

        return array_map([$this, 'mapStockDisplay'], $statement->fetchAll(PDO::FETCH_ASSOC));
    }

    public function find(int $id): array|false
    {
        $statement = $this->db->prepare("SELECT * FROM items WHERE id = :id");
        $statement->execute(['id' => $id]);
        $item = $statement->fetch(PDO::FETCH_ASSOC);

        if ($item === false) {
            return false;
        }

        return $this->mapStockDisplay($item);
    }

    public function findRaw(int $id): array|false
    {
        $statement = $this->db->prepare("SELECT * FROM items WHERE id = :id");
        $statement->execute(['id' => $id]);
        return $statement->fetch(PDO::FETCH_ASSOC);
    }

    public function save(array $data): void
    {
        if (empty($data['code'])) {
            $data['code'] = $this->nextCode();
        }

        if (empty($data['id'])) {
            $existing = $this->findByCode((string) $data['code']);
            if ($existing) {
                $data['id'] = $existing['id'];
            }
        }

        $existingItem = !empty($data['id']) ? $this->findRaw((int) $data['id']) : false;
        $data['small_unit_qty'] = max(1, (int) ($data['small_unit_qty'] ?? 1));
        $currentStock = max(0, (int) ($existingItem['stock'] ?? 0));
        $data['stock'] = $existingItem ? $currentStock : max(0, (int) ($data['stock'] ?? 0));
        $incomingPurchaseTotal = max(0, (float) ($data['purchase_total'] ?? 0));
        $incomingBasisQty = max(0, (int) ($data['purchase_basis_qty'] ?? 0));
        $existingPurchaseTotal = max(0, (float) ($existingItem['purchase_total'] ?? 0));
        $existingBasisQty = max(0, (int) ($existingItem['purchase_basis_qty'] ?? 0));
        $updatePurchase = (string) ($data['update_purchase'] ?? '0');
        $isNewItem = empty($existingItem);

        if ($isNewItem) {
            $data['purchase_total'] = $incomingPurchaseTotal;
            $data['purchase_basis_qty'] = $incomingBasisQty;
        } elseif ($updatePurchase === '1') {
            $data['purchase_total'] = $incomingPurchaseTotal;
            $data['purchase_basis_qty'] = $incomingBasisQty;
        } elseif ($incomingPurchaseTotal > 0 && $incomingBasisQty > 0) {
            $data['purchase_total'] = $existingPurchaseTotal;
            $data['purchase_basis_qty'] = $existingBasisQty;
        } else {
            $data['purchase_total'] = $existingPurchaseTotal;
            $data['purchase_basis_qty'] = $existingBasisQty;
        }
        $recommendedUnitPrice = $this->recommendedUnitPrice((float) $data['selling_price'], (int) $data['small_unit_qty']);
        $recommendedHalfPrice = $this->recommendedHalfPrice((float) $data['selling_price']);
        $data['unit_price'] = (float) ($data['unit_price'] ?? 0) > 0 ? (float) $data['unit_price'] : $recommendedUnitPrice;
        $data['half_price'] = (float) ($data['half_price'] ?? 0) > 0 ? (float) $data['half_price'] : $recommendedHalfPrice;
        $data['allow_small_sale'] = !empty($data['allow_small_sale']) ? 1 : 0;
        $data['allow_half_sale'] = !empty($data['allow_half_sale']) ? 1 : 0;
        $data['promo_qty_1'] = max(0, (int) ($data['promo_qty_1'] ?? 0));
        $data['promo_price_1'] = max(0, (float) ($data['promo_price_1'] ?? 0));
        $data['promo_qty_2'] = max(0, (int) ($data['promo_qty_2'] ?? 0));
        $data['promo_price_2'] = max(0, (float) ($data['promo_price_2'] ?? 0));
        $data['promo_qty_3'] = max(0, (int) ($data['promo_qty_3'] ?? 0));
        $data['promo_price_3'] = max(0, (float) ($data['promo_price_3'] ?? 0));
        $data['promo_qty_4'] = max(0, (int) ($data['promo_qty_4'] ?? 0));
        $data['promo_price_4'] = max(0, (float) ($data['promo_price_4'] ?? 0));
        $data['promo_qty_5'] = max(0, (int) ($data['promo_qty_5'] ?? 0));
        $data['promo_price_5'] = max(0, (float) ($data['promo_price_5'] ?? 0));
        $data['promo_qty_6'] = max(0, (int) ($data['promo_qty_6'] ?? 0));
        $data['promo_price_6'] = max(0, (float) ($data['promo_price_6'] ?? 0));
        $data['description'] = (string) ($data['description'] ?? '');

        if (!empty($data['id'])) {
            $sql = "UPDATE items SET code=:code, barcode=:barcode, name=:name, category=:category, description=:description, unit_large=:unit_large, unit_small=:unit_small, small_unit_qty=:small_unit_qty, purchase_price=:purchase_price, purchase_total=:purchase_total, purchase_basis_qty=:purchase_basis_qty, selling_price=:selling_price, profit_percent=:profit_percent, unit_price=:unit_price, half_price=:half_price, allow_small_sale=:allow_small_sale, allow_half_sale=:allow_half_sale, promo_qty_1=:promo_qty_1, promo_price_1=:promo_price_1, promo_qty_2=:promo_qty_2, promo_price_2=:promo_price_2, promo_qty_3=:promo_qty_3, promo_price_3=:promo_price_3, promo_qty_4=:promo_qty_4, promo_price_4=:promo_price_4, promo_qty_5=:promo_qty_5, promo_price_5=:promo_price_5, promo_qty_6=:promo_qty_6, promo_price_6=:promo_price_6, stock=:stock, exp_date=:exp_date WHERE id=:id";
            $params = [
                'id' => $data['id'],
                'code' => $data['code'],
                'barcode' => $data['barcode'],
                'name' => $data['name'],
                'category' => $data['category'],
                'description' => $data['description'],
                'unit_large' => $data['unit_large'],
                'unit_small' => $data['unit_small'],
                'small_unit_qty' => $data['small_unit_qty'],
                'purchase_price' => $data['purchase_price'],
                'purchase_total' => $data['purchase_total'],
                'purchase_basis_qty' => $data['purchase_basis_qty'],
                'selling_price' => $data['selling_price'],
                'profit_percent' => $data['profit_percent'],
                'unit_price' => $data['unit_price'],
                'half_price' => $data['half_price'],
                'allow_small_sale' => $data['allow_small_sale'],
                'allow_half_sale' => $data['allow_half_sale'],
                'promo_qty_1' => $data['promo_qty_1'],
                'promo_price_1' => $data['promo_price_1'],
                'promo_qty_2' => $data['promo_qty_2'],
                'promo_price_2' => $data['promo_price_2'],
                'promo_qty_3' => $data['promo_qty_3'],
                'promo_price_3' => $data['promo_price_3'],
                'promo_qty_4' => $data['promo_qty_4'],
                'promo_price_4' => $data['promo_price_4'],
                'promo_qty_5' => $data['promo_qty_5'],
                'promo_price_5' => $data['promo_price_5'],
                'promo_qty_6' => $data['promo_qty_6'],
                'promo_price_6' => $data['promo_price_6'],
                'stock' => $data['stock'],
                'exp_date' => $data['exp_date'],
            ];
        } else {
            $sql = "INSERT INTO items (code, barcode, name, category, description, unit_large, unit_small, small_unit_qty, purchase_price, purchase_total, purchase_basis_qty, selling_price, profit_percent, unit_price, half_price, allow_small_sale, allow_half_sale, promo_qty_1, promo_price_1, promo_qty_2, promo_price_2, promo_qty_3, promo_price_3, promo_qty_4, promo_price_4, promo_qty_5, promo_price_5, promo_qty_6, promo_price_6, stock, exp_date, created_at) VALUES (:code, :barcode, :name, :category, :description, :unit_large, :unit_small, :small_unit_qty, :purchase_price, :purchase_total, :purchase_basis_qty, :selling_price, :profit_percent, :unit_price, :half_price, :allow_small_sale, :allow_half_sale, :promo_qty_1, :promo_price_1, :promo_qty_2, :promo_price_2, :promo_qty_3, :promo_price_3, :promo_qty_4, :promo_price_4, :promo_qty_5, :promo_price_5, :promo_qty_6, :promo_price_6, :stock, :exp_date, :created_at)";
            $params = [
                'code' => $data['code'],
                'barcode' => $data['barcode'],
                'name' => $data['name'],
                'category' => $data['category'],
                'description' => $data['description'],
                'unit_large' => $data['unit_large'],
                'unit_small' => $data['unit_small'],
                'small_unit_qty' => $data['small_unit_qty'],
                'purchase_price' => $data['purchase_price'],
                'purchase_total' => $data['purchase_total'],
                'purchase_basis_qty' => $data['purchase_basis_qty'],
                'selling_price' => $data['selling_price'],
                'profit_percent' => $data['profit_percent'],
                'unit_price' => $data['unit_price'],
                'half_price' => $data['half_price'],
                'allow_small_sale' => $data['allow_small_sale'],
                'allow_half_sale' => $data['allow_half_sale'],
                'promo_qty_1' => $data['promo_qty_1'],
                'promo_price_1' => $data['promo_price_1'],
                'promo_qty_2' => $data['promo_qty_2'],
                'promo_price_2' => $data['promo_price_2'],
                'promo_qty_3' => $data['promo_qty_3'],
                'promo_price_3' => $data['promo_price_3'],
                'promo_qty_4' => $data['promo_qty_4'],
                'promo_price_4' => $data['promo_price_4'],
                'promo_qty_5' => $data['promo_qty_5'],
                'promo_price_5' => $data['promo_price_5'],
                'promo_qty_6' => $data['promo_qty_6'],
                'promo_price_6' => $data['promo_price_6'],
                'stock' => $data['stock'],
                'exp_date' => $data['exp_date'],
                'created_at' => date('Y-m-d H:i:s'),
            ];
        }

        $statement = $this->db->prepare($sql);
        $statement->execute($params);
    }

    public function recommendation(array $input): array
    {
        $purchasePrice = (float) ($input['purchase_price'] ?? 0);
        $sellingPrice = (float) ($input['selling_price'] ?? 0);
        $smallUnitQty = max(1, (int) ($input['small_unit_qty'] ?? 1));
        $basePurchase = $purchasePrice;
        $recommendedSell = $sellingPrice > 0 ? $sellingPrice : $this->roundedPrice($basePurchase);
        $recommendedUnit = $this->recommendedUnitPrice($recommendedSell, $smallUnitQty);
        $unitCost = $smallUnitQty > 0 ? ceil($basePurchase / $smallUnitQty) : 0;

        return [
            'base_purchase' => $basePurchase,
            'recommended_sell' => $recommendedSell,
            'recommended_unit' => $recommendedUnit,
            'recommended_half' => $this->recommendedHalfPrice($recommendedSell),
            'unit_cost' => $unitCost,
            'small_unit_qty' => $smallUnitQty,
        ];
    }

    public function findByCode(string $code): array|false
    {
        $statement = $this->db->prepare("SELECT * FROM items WHERE code = :code");
        $statement->execute(['code' => $code]);
        $item = $statement->fetch(PDO::FETCH_ASSOC);

        if ($item === false) {
            return false;
        }

        return $this->mapStockDisplay($item);
    }

    public function delete(int $id): bool
    {
        $statement = $this->db->prepare("DELETE FROM items WHERE id = :id");
        $statement->execute(['id' => $id]);

        return $statement->rowCount() > 0;
    }

    public function searchForTransaction(string $keyword = ''): array
    {
        $statement = $this->db->prepare("SELECT * FROM items WHERE category <> 'E-SALDO' AND (name LIKE :keyword OR code LIKE :keyword OR barcode LIKE :keyword) ORDER BY name ASC");
        $statement->execute(['keyword' => '%' . $keyword . '%']);
        $items = $statement->fetchAll(PDO::FETCH_ASSOC);
        return array_map([$this, 'mapStockDisplay'], $items);
    }

    public function importCsv(string $filePath): int
    {
        $handle = fopen($filePath, 'r');
        if ($handle === false) {
            return 0;
        }

        $count = 0;
        fgetcsv($handle);
        while (($row = fgetcsv($handle)) !== false) {
            if (count($row) < 19) {
                continue;
            }

            $this->save([
                'code' => trim((string) $row[0]),
                'name' => trim((string) $row[1]),
                'category' => trim((string) $row[2]),
                'description' => trim((string) ($row[3] ?? '')),
                'unit_large' => trim((string) ($row[4] ?? 'Pcs')),
                'unit_small' => trim((string) ($row[5] ?? 'Unit')),
                'small_unit_qty' => (int) ($row[6] ?? 1),
                'purchase_price' => (float) ($row[7] ?? 0),
                'purchase_total' => (float) ($row[7] ?? 0),
                'purchase_basis_qty' => (int) ($row[18] ?? 0),
                'selling_price' => (float) ($row[8] ?? 0),
                'profit_percent' => (float) ($row[9] ?? 0),
                'unit_price' => (float) ($row[10] ?? 0),
                'half_price' => (float) ($row[11] ?? 0),
                'promo_qty_1' => (int) ($row[12] ?? 0),
                'promo_price_1' => (float) ($row[13] ?? 0),
                'promo_qty_2' => (int) ($row[14] ?? 0),
                'promo_price_2' => (float) ($row[15] ?? 0),
                'promo_qty_3' => (int) ($row[16] ?? 0),
                'promo_price_3' => (float) ($row[17] ?? 0),
                'promo_qty_4' => 0,
                'promo_price_4' => 0,
                'promo_qty_5' => 0,
                'promo_price_5' => 0,
                'promo_qty_6' => 0,
                'promo_price_6' => 0,
                'stock' => (int) ($row[18] ?? 0),
                'exp_date' => $row[19] ?? null,
            ]);
            $count++;
        }

        fclose($handle);
        return $count;
    }

    private function recommendedUnitPrice(float $sellingPrice, int $smallUnitQty): float
    {
        if ($sellingPrice <= 0 || $smallUnitQty <= 0) {
            return 0;
        }

        return (float) $this->roundedUnitPrice($sellingPrice / $smallUnitQty);
    }

    private function roundedPrice(float $value): int
    {
        if ($value <= 0) {
            return 0;
        }

        return (int) (ceil($value / 100) * 100);
    }

    private function roundedUnitPrice(float $value): int
    {
        if ($value <= 0) {
            return 0;
        }

        return (int) (ceil($value / 500) * 500);
    }

    private function recommendedHalfPrice(float $sellingPrice): float
    {
        if ($sellingPrice <= 0) {
            return 0;
        }

        return (float) $this->roundedUnitPrice($sellingPrice / 2);
    }

    private function mapStockDisplay(array $item): array
    {
        $smallUnitQty = max(1, (int) ($item['small_unit_qty'] ?? 1));
        $stock = (int) ($item['stock'] ?? 0);
        $stockParts = split_stock_units($stock, $smallUnitQty);
        $purchaseTotal = (float) ($item['purchase_total'] ?? 0);
        $purchaseBasisQty = max(0, (int) ($item['purchase_basis_qty'] ?? 0));

        if ($purchaseTotal <= 0) {
            $purchaseTotal = (float) ($item['purchase_price'] ?? 0);
        }
        if ($purchaseBasisQty <= 0) {
            $purchaseBasisQty = $stock > 0 ? $stock : $smallUnitQty;
        }

        $purchasePrice = (float) ($item['purchase_price'] ?? 0);

        $item['purchase_total'] = $purchaseTotal;
        $item['purchase_basis_qty'] = $purchaseBasisQty;
        $purchaseParts = split_stock_units($purchaseBasisQty, $smallUnitQty);
        $item['purchase_basis_large'] = $purchaseParts['large'];
        $item['purchase_basis_small'] = $purchaseParts['small'];
        $purchaseBasisLarge = max(1, (int) ($item['purchase_basis_large'] ?? 0));
        $allowSmallSale = !empty($item['allow_small_sale']);

        $item['cost_per_small'] = $smallUnitQty > 0 ? ($purchasePrice / $smallUnitQty) : 0.0;
        $item['cost_per_large'] = $purchasePrice;

        $item['profit_per_small'] = (float) ($item['unit_price'] ?? 0) - $item['cost_per_small'];
        $item['profit_per_large'] = (float) ($item['selling_price'] ?? 0) - $item['cost_per_large'];
        $item['profit_per_small_rounded'] = (int) round($item['profit_per_small']);
        $item['profit_per_large_rounded'] = (int) round($item['profit_per_large']);
        $useSmallProfit = $allowSmallSale;
        $item['active_profit_value'] = $useSmallProfit
            ? ($item['profit_per_small_rounded'] ?? $item['profit_per_small'])
            : ($item['profit_per_large_rounded'] ?? $item['profit_per_large']);
        $item['active_profit_unit'] = $useSmallProfit
            ? (string) ($item['unit_small'] ?? 'Batang')
            : (string) ($item['unit_large'] ?? 'Bungkus');

        $item['stock_large'] = $stockParts['large'];
        $item['stock_small'] = $stockParts['small'];
        $item['stock_display'] = format_stock_breakdown(
            $stock,
            (string) ($item['unit_large'] ?? 'Bungkus'),
            (string) ($item['unit_small'] ?? 'Pcs'),
            $smallUnitQty
        );

        return $item;
    }
}
