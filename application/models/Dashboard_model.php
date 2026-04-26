<?php
declare(strict_types=1);

class Dashboard_model extends Model
{
    private function currentProfitSql(): string
    {
        return "
            SELECT COALESCE(SUM(
                sale_items.line_total - (
                    sale_items.qty * (
                        CASE
                            WHEN COALESCE(items.purchase_price, 0) > 0 THEN COALESCE(items.purchase_price, 0)
                            WHEN COALESCE(items.purchase_basis_qty, 0) > 0 THEN COALESCE(items.purchase_total, 0) / items.purchase_basis_qty
                            WHEN COALESCE(items.stock, 0) > 0 THEN COALESCE(items.purchase_total, 0) / items.stock
                            ELSE COALESCE(sale_items.purchase_price, 0)
                        END
                    )
                )
            ), 0)
            FROM sale_items
            INNER JOIN sales ON sales.id = sale_items.sale_id
            LEFT JOIN items ON items.id = sale_items.item_id
            WHERE sales.transaction_date = :transaction_date
        ";
    }

    public function summaryToday(): array
    {
        $today = date('Y-m-d');

        $sales = (float) $this->db->query("SELECT COALESCE(SUM(subtotal),0) FROM sales WHERE transaction_date = '{$today}'")->fetchColumn();
        $profitStatement = $this->db->prepare($this->currentProfitSql());
        $profitStatement->execute(['transaction_date' => $today]);
        $profit = (float) $profitStatement->fetchColumn();
        $debts = (float) $this->db->query("SELECT COALESCE(SUM(total_debt - paid_amount),0) FROM debts WHERE status = 'Belum Lunas'")->fetchColumn();
        $stock = (int) $this->db->query("SELECT COALESCE(SUM(stock),0) FROM items")->fetchColumn();
        $vaultStatement = $this->db->prepare("
            SELECT COALESCE(SUM(balance),0)
            FROM vaults
            WHERE UPPER(TRIM(COALESCE(bank_name, ''))) = 'MODAL'
        ");
        $vaultStatement->execute();
        $vault = (float) $vaultStatement->fetchColumn();

        return [
            'sales' => $sales,
            'profit' => $profit,
            'net_profit' => $profit - (float) $this->config['daily_capital'],
            'debts' => $debts,
            'stock' => $stock,
            'vault' => $vault,
        ];
    }

    public function lowStockItems(): array
    {
        $statement = $this->db->query("SELECT * FROM items WHERE stock <= 5 ORDER BY stock ASC, name ASC LIMIT 8");
        return $statement->fetchAll(PDO::FETCH_ASSOC);
    }

    public function expiringItems(): array
    {
        $date = date('Y-m-d', strtotime('+30 days'));
        $statement = $this->db->prepare("SELECT * FROM items WHERE exp_date IS NOT NULL AND exp_date <= :date ORDER BY exp_date ASC LIMIT 8");
        $statement->execute(['date' => $date]);
        return $statement->fetchAll(PDO::FETCH_ASSOC);
    }
}
