<?php
declare(strict_types=1);

class Keuangan_model extends Model
{
    public function allVaults(): array
    {
        return $this->db->query("SELECT * FROM vaults ORDER BY id DESC")->fetchAll(PDO::FETCH_ASSOC);
    }

    public function findVault(int $id): array|false
    {
        $statement = $this->db->prepare("SELECT * FROM vaults WHERE id = :id");
        $statement->execute(['id' => $id]);
        return $statement->fetch(PDO::FETCH_ASSOC);
    }

    public function saveVault(array $data): void
    {
        if (!empty($data['id'])) {
            $sql = "UPDATE vaults SET bank_name=:bank_name, account_name=:account_name, balance=:balance WHERE id=:id";
            $params = [
                'id' => $data['id'],
                'bank_name' => $data['bank_name'],
                'account_name' => $data['account_name'],
                'balance' => $data['balance'],
            ];
        } else {
            $sql = "INSERT INTO vaults (bank_name, account_name, balance, created_at) VALUES (:bank_name, :account_name, :balance, :created_at)";
            $params = [
                'bank_name' => $data['bank_name'],
                'account_name' => $data['account_name'],
                'balance' => $data['balance'],
                'created_at' => date('Y-m-d H:i:s'),
            ];
        }

        $statement = $this->db->prepare($sql);
        $statement->execute($params);
    }

    public function updateVaultBalance(int $vaultId, float $amount): void
    {
        $statement = $this->db->prepare("UPDATE vaults SET balance = balance + :amount WHERE id = :id");
        $statement->execute([
            'amount' => $amount,
            'id' => $vaultId,
        ]);
    }

    public function recordVaultTransaction(array $data): bool
    {
        $type = (string) ($data['transaction_type'] ?? '');
        $amount = max(0, (float) ($data['amount'] ?? 0));
        $sourceVaultId = (int) ($data['source_vault_id'] ?? 0);
        $targetVaultId = (int) ($data['target_vault_id'] ?? 0);
        $notes = trim((string) ($data['notes'] ?? ''));

        if ($amount <= 0 || $type === '') {
            return false;
        }

        $this->db->beginTransaction();

        if ($type === 'switching_dana') {
            if ($sourceVaultId <= 0 || $targetVaultId <= 0 || $sourceVaultId === $targetVaultId) {
                $this->db->rollBack();
                return false;
            }

            $this->updateVaultBalance($sourceVaultId, -1 * $amount);
            $this->updateVaultBalance($targetVaultId, $amount);
        } elseif ($type === 'pembelian') {
            if ($sourceVaultId <= 0) {
                $this->db->rollBack();
                return false;
            }

            $this->updateVaultBalance($sourceVaultId, -1 * $amount);
        } elseif ($type === 'dana_masuk') {
            if ($targetVaultId <= 0) {
                $this->db->rollBack();
                return false;
            }

            $this->updateVaultBalance($targetVaultId, $amount);
        } else {
            $this->db->rollBack();
            return false;
        }

        $statement = $this->db->prepare("
            INSERT INTO vault_transactions (
                transaction_type,
                source_vault_id,
                target_vault_id,
                amount,
                notes,
                transaction_date,
                created_at
            ) VALUES (
                :transaction_type,
                :source_vault_id,
                :target_vault_id,
                :amount,
                :notes,
                :transaction_date,
                :created_at
            )
        ");
        $statement->execute([
            'transaction_type' => $type,
            'source_vault_id' => $sourceVaultId ?: null,
            'target_vault_id' => $targetVaultId ?: null,
            'amount' => $amount,
            'notes' => $notes,
            'transaction_date' => date('Y-m-d'),
            'created_at' => date('Y-m-d H:i:s'),
        ]);

        $this->db->commit();
        return true;
    }

    public function vaultTransactions(): array
    {
        $statement = $this->db->query("
            SELECT
                vt.*,
                source.bank_name AS source_bank_name,
                source.account_name AS source_account_name,
                target.bank_name AS target_bank_name,
                target.account_name AS target_account_name
            FROM vault_transactions vt
            LEFT JOIN vaults source ON source.id = vt.source_vault_id
            LEFT JOIN vaults target ON target.id = vt.target_vault_id
            ORDER BY vt.id DESC
        ");

        return $statement->fetchAll(PDO::FETCH_ASSOC);
    }

    public function transactionsByVault(int $vaultId): array
    {
        $manualStatement = $this->db->prepare("
            SELECT
                vt.id,
                vt.transaction_date,
                vt.created_at,
                vt.amount,
                vt.notes,
                vt.transaction_type,
                source.bank_name AS source_bank_name,
                source.account_name AS source_account_name,
                target.bank_name AS target_bank_name,
                target.account_name AS target_account_name,
                'manual' AS source_module
            FROM vault_transactions vt
            LEFT JOIN vaults source ON source.id = vt.source_vault_id
            LEFT JOIN vaults target ON target.id = vt.target_vault_id
            WHERE vt.source_vault_id = :vault_id OR vt.target_vault_id = :vault_id
        ");
        $manualStatement->execute(['vault_id' => $vaultId]);
        $manualRows = $manualStatement->fetchAll(PDO::FETCH_ASSOC);

        $salesStatement = $this->db->prepare("
            SELECT
                sale_items.id,
                sales.transaction_date,
                sales.created_at,
                sale_items.line_total AS amount,
                sales.invoice_no AS notes,
                'penjualan' AS transaction_type,
                '' AS source_bank_name,
                '' AS source_account_name,
                vaults.bank_name AS target_bank_name,
                vaults.account_name AS target_account_name,
                'transaksi' AS source_module
            FROM sale_items
            INNER JOIN sales ON sales.id = sale_items.sale_id
            LEFT JOIN vaults ON vaults.id = sale_items.vault_id
            WHERE sale_items.vault_id = :vault_id
        ");
        $salesStatement->execute(['vault_id' => $vaultId]);
        $salesRows = $salesStatement->fetchAll(PDO::FETCH_ASSOC);

        $rows = array_merge($manualRows, $salesRows);
        usort($rows, function (array $a, array $b): int {
            return strcmp(($b['created_at'] ?? ''), ($a['created_at'] ?? ''));
        });

        return $rows;
    }

    public function findVaultByKeyword(string $keyword): array|false
    {
        $statement = $this->db->prepare("SELECT * FROM vaults WHERE bank_name LIKE :keyword OR account_name LIKE :keyword ORDER BY id DESC LIMIT 1");
        $statement->execute([
            'keyword' => '%' . $keyword . '%',
        ]);
        return $statement->fetch(PDO::FETCH_ASSOC);
    }

    public function debts(): array
    {
        $sql = "SELECT debts.*, customers.name AS customer_name, sales.invoice_no
                FROM debts
                LEFT JOIN customers ON customers.id = debts.customer_id
                LEFT JOIN sales ON sales.id = debts.sale_id
                ORDER BY debts.id DESC";
        return $this->db->query($sql)->fetchAll(PDO::FETCH_ASSOC);
    }

    public function recordDebtPayment(int $debtId, float $amount, string $notes = ''): void
    {
        $statement = $this->db->prepare("INSERT INTO debt_payments (debt_id, amount, payment_date, notes) VALUES (:debt_id, :amount, :payment_date, :notes)");
        $statement->execute([
            'debt_id' => $debtId,
            'amount' => $amount,
            'payment_date' => date('Y-m-d'),
            'notes' => $notes,
        ]);

        $update = $this->db->prepare("UPDATE debts SET paid_amount = paid_amount + :amount, status = CASE WHEN paid_amount + :amount >= total_debt THEN 'Lunas' ELSE 'Belum Lunas' END WHERE id = :id");
        $update->execute([
            'amount' => $amount,
            'id' => $debtId,
        ]);
    }
}
