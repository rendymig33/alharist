<?php
declare(strict_types=1);

class Stok_model extends Model
{
    public function itemOptions(string $keyword = ''): array
    {
        $keyword = trim($keyword);
        $sql = "SELECT * FROM items";
        $params = [];

        if ($keyword !== '') {
            $sql .= " WHERE code LIKE :keyword OR barcode LIKE :keyword OR name LIKE :keyword OR category LIKE :keyword";
            $params['keyword'] = '%' . $keyword . '%';
        }

        $sql .= " ORDER BY name ASC";
        $statement = $this->db->prepare($sql);
        $statement->execute($params);
        $items = $statement->fetchAll(PDO::FETCH_ASSOC);

        return array_map(function (array $item): array {
            $smallUnitQty = max(1, (int) ($item['small_unit_qty'] ?? 1));
            $stock = max(0, (int) ($item['stock'] ?? 0));
            $stockParts = split_stock_units($stock, $smallUnitQty);
            $allowSmallSale = !empty($item['allow_small_sale']);
            $item['stock_display'] = format_stock_breakdown(
                $stock,
                (string) ($item['unit_large'] ?? 'Bungkus'),
                (string) ($item['unit_small'] ?? 'Pcs'),
                $smallUnitQty
            );
            $item['stock_large_units'] = (int) $stockParts['large'];
            $item['stock_small_units'] = (int) $stockParts['small'];
            $item['low_stock'] = $allowSmallSale ? ($stock < 3) : ((int) $stockParts['large'] < 3);
            $item['low_stock_note'] = $allowSmallSale
                ? 'Stok kecil kurang dari 3'
                : 'Stok besar kurang dari 3';
            return $item;
        }, $items);
    }

    public function receiveHistory(string $keyword = ''): array
    {
        $keyword = trim($keyword);
        $sql = "
            SELECT ir.*, items.code AS item_code, items.name AS item_name, items.unit_large, items.unit_small
            FROM item_receives ir
            INNER JOIN items ON items.id = ir.item_id
        ";
        $params = [];

        if ($keyword !== '') {
            $sql .= " WHERE items.code LIKE :keyword OR items.barcode LIKE :keyword OR items.name LIKE :keyword";
            $params['keyword'] = '%' . $keyword . '%';
        }

        $sql .= " ORDER BY ir.id DESC";
        $statement = $this->db->prepare($sql);
        $statement->execute($params);
        return $statement->fetchAll(PDO::FETCH_ASSOC);
    }

    public function receiveHistoryByItem(int $itemId): array
    {
        $statement = $this->db->prepare("
            SELECT ir.*, items.code AS item_code, items.name AS item_name, items.unit_large, items.unit_small, items.small_unit_qty
            FROM item_receives ir
            INNER JOIN items ON items.id = ir.item_id
            WHERE ir.item_id = :item_id
            ORDER BY ir.id DESC
            LIMIT 12
        ");
        $statement->execute(['item_id' => $itemId]);
        return $statement->fetchAll(PDO::FETCH_ASSOC);
    }

    public function opnameHistory(string $keyword = ''): array
    {
        $keyword = trim($keyword);
        $sql = "
            SELECT so.*, items.code AS item_code, items.name AS item_name, items.unit_large, items.unit_small, items.small_unit_qty
            FROM stock_opnames so
            INNER JOIN items ON items.id = so.item_id
        ";
        $params = [];

        if ($keyword !== '') {
            $sql .= " WHERE items.code LIKE :keyword OR items.barcode LIKE :keyword OR items.name LIKE :keyword";
            $params['keyword'] = '%' . $keyword . '%';
        }

        $sql .= " ORDER BY so.id DESC";
        $statement = $this->db->prepare($sql);
        $statement->execute($params);
        return $statement->fetchAll(PDO::FETCH_ASSOC);
    }

    public function opnameHistoryByItem(int $itemId): array
    {
        $statement = $this->db->prepare("
            SELECT so.*, items.unit_large, items.unit_small, items.small_unit_qty
            FROM stock_opnames so
            INNER JOIN items ON items.id = so.item_id
            WHERE so.item_id = :item_id
            ORDER BY so.id DESC
            LIMIT 12
        ");
        $statement->execute(['item_id' => $itemId]);
        return $statement->fetchAll(PDO::FETCH_ASSOC);
    }

    public function receiveItem(array $data): bool
    {
        $itemId = (int) ($data['item_id'] ?? 0);
        $qtyLarge = max(0, (int) ($data['qty_large'] ?? 0));
        $qtySmall = max(0, (int) ($data['qty_small'] ?? 0));
        $purchasePrice = max(0, (float) ($data['purchase_price'] ?? 0));
        $notes = trim((string) ($data['notes'] ?? ''));

        $itemStatement = $this->db->prepare("SELECT * FROM items WHERE id = :id");
        $itemStatement->execute(['id' => $itemId]);
        $item = $itemStatement->fetch(PDO::FETCH_ASSOC);

        if (!$item) {
            return false;
        }

        $smallUnitQty = max(1, (int) ($item['small_unit_qty'] ?? 1));
        $qtyTotal = stock_to_smallest_units($qtyLarge, $qtySmall, $smallUnitQty);
        if ($qtyTotal <= 0) {
            return false;
        }

        $effectiveLargeQty = $qtyTotal / $smallUnitQty;
        $purchaseTotal = $purchasePrice * $effectiveLargeQty;

        $this->db->beginTransaction();

        $updateItem = $this->db->prepare("
            UPDATE items
            SET stock = stock + :qty_total,
                purchase_price = :purchase_price,
                purchase_total = :purchase_total,
                purchase_basis_qty = :purchase_basis_qty
            WHERE id = :id
        ");
        $updateItem->execute([
            'qty_total' => $qtyTotal,
            'purchase_price' => $purchasePrice,
            'purchase_total' => $purchaseTotal,
            'purchase_basis_qty' => $qtyLarge,
            'id' => $itemId,
        ]);

        $insert = $this->db->prepare("
            INSERT INTO item_receives (
                item_id, qty_large, qty_small, qty_total, purchase_price, purchase_total, notes, transaction_date, created_at
            ) VALUES (
                :item_id, :qty_large, :qty_small, :qty_total, :purchase_price, :purchase_total, :notes, :transaction_date, :created_at
            )
        ");
        $insert->execute([
            'item_id' => $itemId,
            'qty_large' => $qtyLarge,
            'qty_small' => $qtySmall,
            'qty_total' => $qtyTotal,
            'purchase_price' => $purchasePrice,
            'purchase_total' => $purchaseTotal,
            'notes' => $notes,
            'transaction_date' => date('Y-m-d'),
            'created_at' => date('Y-m-d H:i:s'),
        ]);

        $this->db->commit();
        return true;
    }

    public function deleteReceive(int $receiveId): bool
    {
        $statement = $this->db->prepare("
            SELECT ir.*, items.id AS current_item_id, items.stock AS current_stock
            FROM item_receives ir
            INNER JOIN items ON items.id = ir.item_id
            WHERE ir.id = :id
        ");
        $statement->execute(['id' => $receiveId]);
        $receive = $statement->fetch(PDO::FETCH_ASSOC);

        if (!$receive) {
            return false;
        }

        $itemId = (int) ($receive['item_id'] ?? 0);
        $qtyTotal = max(0, (int) ($receive['qty_total'] ?? 0));
        $currentStock = max(0, (int) ($receive['current_stock'] ?? 0));
        $newStock = max(0, $currentStock - $qtyTotal);

        $this->db->beginTransaction();

        $delete = $this->db->prepare("DELETE FROM item_receives WHERE id = :id");
        $delete->execute(['id' => $receiveId]);

        $latestStatement = $this->db->prepare("
            SELECT *
            FROM item_receives
            WHERE item_id = :item_id
            ORDER BY id DESC
            LIMIT 1
        ");
        $latestStatement->execute(['item_id' => $itemId]);
        $latestReceive = $latestStatement->fetch(PDO::FETCH_ASSOC);

        $update = $this->db->prepare("
            UPDATE items
            SET stock = :stock,
                purchase_price = :purchase_price,
                purchase_total = :purchase_total,
                purchase_basis_qty = :purchase_basis_qty
            WHERE id = :id
        ");
        $update->execute([
            'stock' => $newStock,
            'purchase_price' => $latestReceive ? (float) ($latestReceive['purchase_price'] ?? 0) : 0,
            'purchase_total' => $latestReceive ? (float) ($latestReceive['purchase_total'] ?? 0) : 0,
            'purchase_basis_qty' => $latestReceive ? (int) ($latestReceive['qty_large'] ?? 0) : 0,
            'id' => $itemId,
        ]);

        $this->db->commit();
        return true;
    }

    public function stockOpname(array $data): bool
    {
        $itemId = (int) ($data['item_id'] ?? 0);
        $qtyLarge = max(0, (int) ($data['qty_large'] ?? 0));
        $qtySmall = max(0, (int) ($data['qty_small'] ?? 0));
        $notes = trim((string) ($data['notes'] ?? ''));

        $itemStatement = $this->db->prepare("SELECT * FROM items WHERE id = :id");
        $itemStatement->execute(['id' => $itemId]);
        $item = $itemStatement->fetch(PDO::FETCH_ASSOC);

        if (!$item) {
            return false;
        }

        $smallUnitQty = max(1, (int) ($item['small_unit_qty'] ?? 1));
        $beforeStock = max(0, (int) ($item['stock'] ?? 0));
        $actualStock = stock_to_smallest_units($qtyLarge, $qtySmall, $smallUnitQty);
        $adjustment = $actualStock - $beforeStock;

        $this->db->beginTransaction();

        $updateItem = $this->db->prepare("UPDATE items SET stock = :actual_stock WHERE id = :id");
        $updateItem->execute([
            'actual_stock' => $actualStock,
            'id' => $itemId,
        ]);

        $insert = $this->db->prepare("
            INSERT INTO stock_opnames (
                item_id, before_stock, actual_stock, adjustment, notes, transaction_date, created_at
            ) VALUES (
                :item_id, :before_stock, :actual_stock, :adjustment, :notes, :transaction_date, :created_at
            )
        ");
        $insert->execute([
            'item_id' => $itemId,
            'before_stock' => $beforeStock,
            'actual_stock' => $actualStock,
            'adjustment' => $adjustment,
            'notes' => $notes,
            'transaction_date' => date('Y-m-d'),
            'created_at' => date('Y-m-d H:i:s'),
        ]);

        $this->db->commit();
        return true;
    }

    public function deleteStockOpname(int $opnameId): bool
    {
        $statement = $this->db->prepare("
            SELECT so.*, items.id AS current_item_id
            FROM stock_opnames so
            INNER JOIN items ON items.id = so.item_id
            WHERE so.id = :id
        ");
        $statement->execute(['id' => $opnameId]);
        $opname = $statement->fetch(PDO::FETCH_ASSOC);

        if (!$opname) {
            return false;
        }

        $itemId = (int) ($opname['item_id'] ?? 0);
        $latestStatement = $this->db->prepare("
            SELECT id
            FROM stock_opnames
            WHERE item_id = :item_id
            ORDER BY id DESC
            LIMIT 1
        ");
        $latestStatement->execute(['item_id' => $itemId]);
        $latestId = (int) ($latestStatement->fetchColumn() ?: 0);

        if ($latestId !== $opnameId) {
            return false;
        }

        $this->db->beginTransaction();

        $update = $this->db->prepare("UPDATE items SET stock = :before_stock WHERE id = :item_id");
        $update->execute([
            'before_stock' => max(0, (int) ($opname['before_stock'] ?? 0)),
            'item_id' => $itemId,
        ]);

        $delete = $this->db->prepare("DELETE FROM stock_opnames WHERE id = :id");
        $delete->execute(['id' => $opnameId]);

        $this->db->commit();
        return true;
    }
}
