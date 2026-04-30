<?php
declare(strict_types=1);

class Keuangan_model extends Model
{
    public function allVaults(string $keyword = ''): array
    {
        $keyword = trim($keyword);
        if ($keyword === '') {
            return $this->db->query("SELECT * FROM vaults ORDER BY id DESC")->fetchAll(PDO::FETCH_ASSOC);
        }

        $statement = $this->db->prepare("
            SELECT *
            FROM vaults
            WHERE bank_name LIKE :keyword OR account_name LIKE :keyword
            ORDER BY id DESC
        ");
        $statement->execute(['keyword' => '%' . $keyword . '%']);
        return $statement->fetchAll(PDO::FETCH_ASSOC);
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

    public function deleteVaultTransaction(int $transactionId): bool
    {
        $statement = $this->db->prepare("SELECT * FROM vault_transactions WHERE id = :id");
        $statement->execute(['id' => $transactionId]);
        $transaction = $statement->fetch(PDO::FETCH_ASSOC);

        if (!$transaction) {
            return false;
        }

        $type = (string) ($transaction['transaction_type'] ?? '');
        $amount = (float) ($transaction['amount'] ?? 0);
        $sourceVaultId = (int) ($transaction['source_vault_id'] ?? 0);
        $targetVaultId = (int) ($transaction['target_vault_id'] ?? 0);

        $this->db->beginTransaction();

        if ($type === 'switching_dana') {
            if ($sourceVaultId > 0) {
                $this->updateVaultBalance($sourceVaultId, $amount);
            }
            if ($targetVaultId > 0) {
                $this->updateVaultBalance($targetVaultId, -1 * $amount);
            }
        } elseif ($type === 'pembelian') {
            if ($sourceVaultId > 0) {
                $this->updateVaultBalance($sourceVaultId, $amount);
            }
        } elseif ($type === 'dana_masuk') {
            if ($targetVaultId > 0) {
                $this->updateVaultBalance($targetVaultId, -1 * $amount);
            }
        }

        $delete = $this->db->prepare("DELETE FROM vault_transactions WHERE id = :id");
        $delete->execute(['id' => $transactionId]);

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
        $vault = $this->findVault($vaultId);
        $currentBalance = (float) ($vault['balance'] ?? 0);

        $manualStatement = $this->db->prepare("
            SELECT
                vt.id,
                vt.transaction_date,
                vt.created_at,
                vt.amount,
                vt.notes,
                vt.transaction_type,
                vt.source_vault_id,
                vt.target_vault_id,
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
                NULL AS source_vault_id,
                sale_items.vault_id AS target_vault_id,
                CASE
                    WHEN sales.payment_type = 'Tunai' THEN 'CASH'
                    WHEN sales.payment_type = 'QRIS' THEN 'QRIS'
                    ELSE COALESCE(sales.payment_type, '')
                END AS source_bank_name,
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

        $debtPaymentStatement = $this->db->prepare("
            SELECT
                debt_payments.id,
                debt_payments.payment_date AS transaction_date,
                debt_payments.payment_date AS created_at,
                debt_payments.amount,
                CASE
                    WHEN COALESCE(sales.invoice_no, '') <> '' AND COALESCE(customers.name, '') <> '' THEN sales.invoice_no
                    WHEN COALESCE(sales.invoice_no, '') <> '' THEN sales.invoice_no
                    WHEN COALESCE(customers.name, '') <> '' THEN customers.name
                    ELSE 'Pelunasan Hutang'
                END AS notes,
                'pelunasan_hutang' AS transaction_type,
                'PELANGGAN HUTANG' AS source_bank_name,
                '' AS source_account_name,
                vaults.bank_name AS target_bank_name,
                vaults.account_name AS target_account_name,
                debt_payments.vault_id AS target_vault_id,
                'hutang' AS source_module
            FROM debt_payments
            INNER JOIN debts ON debts.id = debt_payments.debt_id
            LEFT JOIN sales ON sales.id = debts.sale_id
            LEFT JOIN customers ON customers.id = debts.customer_id
            LEFT JOIN vaults ON vaults.id = debt_payments.vault_id
            WHERE debt_payments.vault_id = :vault_id
        ");
        $debtPaymentStatement->execute(['vault_id' => $vaultId]);
        $debtPaymentRows = $debtPaymentStatement->fetchAll(PDO::FETCH_ASSOC);

        $rows = array_merge($manualRows, $salesRows, $debtPaymentRows);
                usort($rows, function (array $a, array $b): int {
            return strcmp(($b['created_at'] ?? ''), ($a['created_at'] ?? ''));
        });

        $runningBalance = $currentBalance;
        foreach ($rows as &$row) {
            $isDebit = (int) ($row['target_vault_id'] ?? 0) === $vaultId
                || in_array((string) ($row['transaction_type'] ?? ''), ['penjualan', 'pelunasan_hutang'], true);
            $debet = $isDebit ? (float) ($row['amount'] ?? 0) : 0;
            $kredit = $isDebit ? 0 : (float) ($row['amount'] ?? 0);

            $row['debet'] = $debet;
            $row['kredit'] = $kredit;
            $row['ending_balance'] = $runningBalance;
            $runningBalance = $runningBalance - $debet + $kredit;
        }
        unset($row);

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

    public function debts(string $keyword = ''): array
    {
        $sql = "SELECT debts.*, customers.name AS customer_name, sales.invoice_no,
                       (debts.total_debt - debts.paid_amount) AS remaining_debt
                FROM debts
                LEFT JOIN customers ON customers.id = debts.customer_id
                LEFT JOIN sales ON sales.id = debts.sale_id";
        $params = [];
        if (trim($keyword) !== '') {
            $sql .= " WHERE customers.name LIKE :keyword OR sales.invoice_no LIKE :keyword OR debts.status LIKE :keyword";
            $params['keyword'] = '%' . trim($keyword) . '%';
        }

        $sql .= " ORDER BY debts.id DESC";
        $statement = $this->db->prepare($sql);
        $statement->execute($params);
        return $statement->fetchAll(PDO::FETCH_ASSOC);
    }

    public function recordDebtPayment(int $debtId, float $amount, int $vaultId, string $notes = ''): bool
    {
        $debtStatement = $this->db->prepare("SELECT * FROM debts WHERE id = :id");
        $debtStatement->execute(['id' => $debtId]);
        $debt = $debtStatement->fetch(PDO::FETCH_ASSOC);

        if (!$debt || $vaultId <= 0) {
            return false;
        }

        $remainingDebt = max(0, (float) ($debt['total_debt'] ?? 0) - (float) ($debt['paid_amount'] ?? 0));
        $paymentAmount = max(0, min($amount, $remainingDebt));
        if ($paymentAmount <= 0) {
            return false;
        }

        $this->db->beginTransaction();

        $statement = $this->db->prepare("INSERT INTO debt_payments (debt_id, vault_id, amount, payment_date, notes) VALUES (:debt_id, :vault_id, :amount, :payment_date, :notes)");
        $statement->execute([
            'debt_id' => $debtId,
            'vault_id' => $vaultId,
            'amount' => $paymentAmount,
            'payment_date' => date('Y-m-d'),
            'notes' => $notes,
        ]);

        $update = $this->db->prepare("UPDATE debts SET paid_amount = paid_amount + :amount, status = CASE WHEN paid_amount + :amount >= total_debt THEN 'Lunas' ELSE 'Belum Lunas' END WHERE id = :id");
        $update->execute([
            'amount' => $paymentAmount,
            'id' => $debtId,
        ]);

        $this->updateVaultBalance($vaultId, $paymentAmount);
        $this->db->commit();
        return true;
    }
}
