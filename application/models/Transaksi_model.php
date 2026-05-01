<?php
declare(strict_types=1);

class Transaksi_model extends Model
{
    public function nextInvoiceNo(?string $transactionDate = null): string
    {
        $date = $transactionDate ?: date('Y-m-d');
        $dateKey = date('Ymd', strtotime($date));
        $statement = $this->db->prepare("
            SELECT invoice_no
            FROM sales
            WHERE transaction_date = :transaction_date
            ORDER BY id DESC
            LIMIT 1
        ");
        $statement->execute([
            'transaction_date' => $date,
        ]);
        $lastInvoice = (string) $statement->fetchColumn();
        $lastNumber = 0;

        if ($lastInvoice !== '' && preg_match('/(\d{4})$/', $lastInvoice, $matches)) {
            $lastNumber = (int) $matches[1];
        }

        return 'INV-' . $dateKey . '-' . str_pad((string) ($lastNumber + 1), 4, '0', STR_PAD_LEFT);
    }

    private function liveProfitExpr(string $saleAlias = 'sales', string $itemAlias = 'sale_items', string $masterAlias = 'items'): string
    {
        return "
            (
                {$itemAlias}.line_total - (
                    {$itemAlias}.qty * (
                        CASE
                            WHEN COALESCE({$masterAlias}.category, '') = 'E-SALDO' THEN COALESCE({$itemAlias}.purchase_price, 0)
                            WHEN COALESCE({$masterAlias}.small_unit_qty, 0) > 0 THEN COALESCE({$masterAlias}.purchase_price, 0) / {$masterAlias}.small_unit_qty
                            WHEN COALESCE({$masterAlias}.purchase_price, 0) > 0 THEN COALESCE({$masterAlias}.purchase_price, 0)
                            ELSE COALESCE({$itemAlias}.purchase_price, 0)
                        END
                    )
                )
            )
        ";
    }

    private function liveCostPerSmallExpr(string $itemAlias = 'sale_items', string $masterAlias = 'items'): string
    {
        return "
            (
                CASE
                    WHEN COALESCE({$masterAlias}.category, '') = 'E-SALDO' THEN COALESCE({$itemAlias}.purchase_price, 0)
                    WHEN COALESCE({$masterAlias}.small_unit_qty, 0) > 0 THEN COALESCE({$masterAlias}.purchase_price, 0) / {$masterAlias}.small_unit_qty
                    WHEN COALESCE({$masterAlias}.purchase_price, 0) > 0 THEN COALESCE({$masterAlias}.purchase_price, 0)
                    ELSE COALESCE({$itemAlias}.purchase_price, 0)
                END
            )
        ";
    }

    private function liveLineCostExpr(string $itemAlias = 'sale_items', string $masterAlias = 'items'): string
    {
        return "(" . $itemAlias . ".qty * " . $this->liveCostPerSmallExpr($itemAlias, $masterAlias) . ")";
    }

    public function createSale(array $payload): void
    {
        $this->db->beginTransaction();

        $saleStatement = $this->db->prepare("INSERT INTO sales (invoice_no, customer_id, payment_type, vault_id, subtotal, total_profit, total_paid, notes, transaction_date, created_at) VALUES (:invoice_no, :customer_id, :payment_type, :vault_id, :subtotal, :total_profit, :total_paid, :notes, :transaction_date, :created_at)");
        $saleStatement->execute([
            'invoice_no' => $payload['invoice_no'],
            'customer_id' => $payload['customer_id'] ?: null,
            'payment_type' => $payload['payment_type'],
            'vault_id' => $payload['vault_id'] ?: null,
            'subtotal' => $payload['subtotal'],
            'total_profit' => $payload['total_profit'],
            'total_paid' => $payload['total_paid'],
            'notes' => $payload['notes'],
            'transaction_date' => date('Y-m-d'),
            'created_at' => date('Y-m-d H:i:s'),
        ]);

        $saleId = (int) $this->db->lastInsertId();
        $itemStatement = $this->db->prepare("INSERT INTO sale_items (sale_id, item_id, vault_id, qty, purchase_price, selling_price, line_total, line_profit) VALUES (:sale_id, :item_id, :vault_id, :qty, :purchase_price, :selling_price, :line_total, :line_profit)");
        $stockStatement = $this->db->prepare("UPDATE items SET stock = stock - :qty WHERE id = :id");

        foreach ($payload['items'] as $item) {
            $itemStatement->execute([
                'sale_id' => $saleId,
                'item_id' => $item['item_id'],
                'vault_id' => $item['vault_id'] ?: null,
                'qty' => $item['qty'],
                'purchase_price' => $item['purchase_price'],
                'selling_price' => $item['selling_price'],
                'line_total' => $item['line_total'],
                'line_profit' => $item['line_profit'],
            ]);

            if (empty($item['is_esaldo'])) {
                $stockStatement->execute([
                    'qty' => $item['qty'],
                    'id' => $item['item_id'],
                ]);
            }
        }

        // Kurangi saldo e-saldo (dari master e-saldo, bukan vault modal)
        $esaldoDeductStmt = $this->db->prepare("UPDATE items SET selling_price = selling_price - :amount, unit_price = unit_price - :amount WHERE id = :id AND category = 'E-SALDO'");
        foreach ($payload['items'] as $item) {
            if (!empty($item['is_esaldo'])) {
                $deduction = $item['purchase_price'] * $item['qty'];
                if ($deduction > 0) {
                    $esaldoDeductStmt->execute([
                        'amount' => $deduction,
                        'id' => $item['item_id'],
                    ]);
                }
            }
        }

        if ($payload['payment_type'] === 'Hutang') {
            $debtStatement = $this->db->prepare("INSERT INTO debts (sale_id, customer_id, total_debt, paid_amount, due_date, status, created_at) VALUES (:sale_id, :customer_id, :total_debt, 0, :due_date, 'Belum Lunas', :created_at)");
            $debtStatement->execute([
                'sale_id' => $saleId,
                'customer_id' => $payload['customer_id'] ?: null,
                'total_debt' => $payload['subtotal'],
                'due_date' => $payload['due_date'],
                'created_at' => date('Y-m-d H:i:s'),
            ]);
        }

        $this->db->commit();
    }

    public function latestSales(?string $dateFrom = null, ?string $dateTo = null, int $limit = 10): array
    {
        $sql = "SELECT * FROM sales";
        $conditions = [];
        $params = [];

        if (!empty($dateFrom)) {
            $conditions[] = "transaction_date >= :date_from";
            $params['date_from'] = $dateFrom;
        }

        if (!empty($dateTo)) {
            $conditions[] = "transaction_date <= :date_to";
            $params['date_to'] = $dateTo;
        }

        if (!empty($conditions)) {
            $sql .= " WHERE " . implode(' AND ', $conditions);
        }

        $sql .= " ORDER BY transaction_date DESC, id DESC LIMIT :limit";
        $statement = $this->db->prepare($sql);

        foreach ($params as $key => $value) {
            $statement->bindValue(':' . $key, $value, PDO::PARAM_STR);
        }

        $statement->bindValue(':limit', $limit, PDO::PARAM_INT);
        $statement->execute();

        return $statement->fetchAll(PDO::FETCH_ASSOC);
    }

    public function salesList(string $keyword = ''): array
    {
        $keyword = trim($keyword);
        $sql = "
            SELECT sales.*,
                   COALESCE(SUM(" . $this->liveProfitExpr() . "), 0) AS total_profit_live
            FROM sales
            LEFT JOIN sale_items ON sale_items.sale_id = sales.id
            LEFT JOIN items ON items.id = sale_items.item_id
        ";
        $params = [];

        if ($keyword !== '') {
            $sql .= " WHERE sales.invoice_no LIKE :keyword OR sales.payment_type LIKE :keyword OR sales.transaction_date LIKE :keyword";
            $params['keyword'] = '%' . $keyword . '%';
        }

        $sql .= " GROUP BY sales.id ORDER BY sales.id DESC";
        $statement = $this->db->prepare($sql);
        $statement->execute($params);
        return $statement->fetchAll(PDO::FETCH_ASSOC);
    }

    public function salesSummaryByDate(?string $dateFrom = null, ?string $dateTo = null, int $limit = 20): array
    {
        $sql = "
            SELECT
                daily.transaction_date,
                COUNT(*) AS transaction_count,
                COALESCE(SUM(daily.subtotal), 0) AS subtotal_sum,
                COALESCE(SUM(daily.total_paid), 0) AS total_paid_sum,
                COALESCE(SUM(daily.total_profit_live), 0) AS total_profit_sum
            FROM (
                SELECT
                    sales.id,
                    sales.transaction_date,
                    sales.subtotal,
                    sales.total_paid,
                    COALESCE(SUM(" . $this->liveProfitExpr() . "), 0) AS total_profit_live
                FROM sales
                LEFT JOIN sale_items ON sale_items.sale_id = sales.id
                LEFT JOIN items ON items.id = sale_items.item_id
        ";
        $conditions = [];
        $params = [];

        if (!empty($dateFrom)) {
            $conditions[] = "sales.transaction_date >= :date_from";
            $params['date_from'] = $dateFrom;
        }

        if (!empty($dateTo)) {
            $conditions[] = "sales.transaction_date <= :date_to";
            $params['date_to'] = $dateTo;
        }

        if (!empty($conditions)) {
            $sql .= " WHERE " . implode(' AND ', $conditions);
        }

        $sql .= " GROUP BY sales.id
            ) AS daily
            GROUP BY daily.transaction_date
            ORDER BY daily.transaction_date DESC
            LIMIT :limit";
        $statement = $this->db->prepare($sql);

        foreach ($params as $key => $value) {
            $statement->bindValue(':' . $key, $value, PDO::PARAM_STR);
        }

        $statement->bindValue(':limit', $limit, PDO::PARAM_INT);
        $statement->execute();

        return $statement->fetchAll(PDO::FETCH_ASSOC);
    }

    public function findSale(int $saleId): array|false
    {
        $statement = $this->db->prepare("
            SELECT sales.*,
                   COALESCE(SUM(" . $this->liveProfitExpr() . "), 0) AS total_profit_live
            FROM sales
            LEFT JOIN sale_items ON sale_items.sale_id = sales.id
            LEFT JOIN items ON items.id = sale_items.item_id
            WHERE sales.id = :id
            GROUP BY sales.id
        ");
        $statement->execute(['id' => $saleId]);
        return $statement->fetch(PDO::FETCH_ASSOC);
    }

    public function saleItems(int $saleId): array
    {
        $statement = $this->db->prepare("
            SELECT sale_items.*, items.name AS item_name, items.code AS item_code,
                   " . $this->liveCostPerSmallExpr('sale_items', 'items') . " AS purchase_price_live,
                   " . $this->liveLineCostExpr('sale_items', 'items') . " AS line_cost_live,
                   " . $this->liveProfitExpr('sales', 'sale_items', 'items') . " AS line_profit_live
            FROM sale_items
            LEFT JOIN sales ON sales.id = sale_items.sale_id
            LEFT JOIN items ON items.id = sale_items.item_id
            WHERE sale_items.sale_id = :sale_id
            ORDER BY sale_items.id ASC
        ");
        $statement->execute(['sale_id' => $saleId]);
        return $statement->fetchAll(PDO::FETCH_ASSOC);
    }

    public function salesHistoryByDate(string $transactionDate): array
    {
        $salesStatement = $this->db->prepare("
            SELECT
                sales.*,
                COALESCE(SUM(" . $this->liveProfitExpr() . "), 0) AS total_profit_live
            FROM sales
            LEFT JOIN sale_items ON sale_items.sale_id = sales.id
            LEFT JOIN items ON items.id = sale_items.item_id
            WHERE transaction_date = :transaction_date
            GROUP BY sales.id
            ORDER BY id DESC
        ");
        $salesStatement->execute(['transaction_date' => $transactionDate]);
        $sales = $salesStatement->fetchAll(PDO::FETCH_ASSOC);

        foreach ($sales as &$sale) {
            $sale['items'] = $this->saleItems((int) $sale['id']);
        }
        unset($sale);

        return $sales;
    }

    public function deleteSale(int $saleId): bool
    {
        $saleStatement = $this->db->prepare("SELECT * FROM sales WHERE id = :id");
        $saleStatement->execute(['id' => $saleId]);
        $sale = $saleStatement->fetch(PDO::FETCH_ASSOC);

        if (!$sale) {
            return false;
        }

        $itemsStatement = $this->db->prepare("
            SELECT sale_items.*, items.category
            FROM sale_items
            LEFT JOIN items ON items.id = sale_items.item_id
            WHERE sale_id = :sale_id
        ");
        $itemsStatement->execute(['sale_id' => $saleId]);
        $saleItems = $itemsStatement->fetchAll(PDO::FETCH_ASSOC);

        $this->db->beginTransaction();

        $restoreStock = $this->db->prepare("UPDATE items SET stock = stock + :qty WHERE id = :id");
        foreach ($saleItems as $item) {
            if (($item['category'] ?? '') !== 'E-SALDO') {
                $restoreStock->execute([
                    'qty' => $item['qty'],
                    'id' => $item['item_id'],
                ]);
            }
        }

        if (in_array($sale['payment_type'], ['Tunai', 'QRIS'], true)) {
            $vaultStatement = $this->db->prepare("UPDATE vaults SET balance = balance - :amount WHERE id = :id");
            $vaultTotals = [];
            foreach ($saleItems as $item) {
                $vaultId = (int) ($item['vault_id'] ?? 0);
                if ($vaultId <= 0) {
                    continue;
                }
                $vaultTotals[$vaultId] = ($vaultTotals[$vaultId] ?? 0) + (float) ($item['line_total'] ?? 0);
            }

            if (empty($vaultTotals) && !empty($sale['vault_id'])) {
                $vaultTotals[(int) $sale['vault_id']] = (float) $sale['subtotal'];
            }

            foreach ($vaultTotals as $vaultId => $amount) {
                $vaultStatement->execute([
                    'amount' => $amount,
                    'id' => $vaultId,
                ]);
            }
        }

        // Kembalikan saldo e-saldo (ke master e-saldo, bukan vault modal)
        $esaldoRestoreStmt = $this->db->prepare("UPDATE items SET selling_price = selling_price + :amount, unit_price = unit_price + :amount WHERE id = :id AND category = 'E-SALDO'");
        foreach ($saleItems as $item) {
            if (($item['category'] ?? '') === 'E-SALDO') {
                $restoration = $item['purchase_price'] * $item['qty'];
                if ($restoration > 0) {
                    $esaldoRestoreStmt->execute([
                        'amount' => $restoration,
                        'id' => $item['item_id'],
                    ]);
                }
            }
        }

        $debtStatement = $this->db->prepare("SELECT id FROM debts WHERE sale_id = :sale_id");
        $debtStatement->execute(['sale_id' => $saleId]);
        $debtIds = $debtStatement->fetchAll(PDO::FETCH_COLUMN);

        if (!empty($debtIds)) {
            $paymentDelete = $this->db->prepare("DELETE FROM debt_payments WHERE debt_id = :debt_id");
            foreach ($debtIds as $debtId) {
                $paymentDelete->execute(['debt_id' => $debtId]);
            }

            $this->db->prepare("DELETE FROM debts WHERE sale_id = :sale_id")->execute([
                'sale_id' => $saleId,
            ]);
        }

        $this->db->prepare("DELETE FROM sale_items WHERE sale_id = :sale_id")->execute([
            'sale_id' => $saleId,
        ]);

        $this->db->prepare("DELETE FROM sales WHERE id = :id")->execute([
            'id' => $saleId,
        ]);

        $this->db->commit();
        return true;
    }
}
