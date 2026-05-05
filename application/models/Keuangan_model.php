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
            WHERE bank_name LIKE :keyword
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

    public function getVaultPecahan(int $vaultId): array
    {
        if ($vaultId <= 0) {
            return [];
        }

        $statement = $this->db->prepare('SELECT pecahan_json FROM vault_pecahan WHERE vault_id = :id');
        $statement->execute(['id' => $vaultId]);
        $row = $statement->fetch(PDO::FETCH_ASSOC);
        if (!$row || (string) ($row['pecahan_json'] ?? '') === '') {
            return [];
        }

        $decoded = json_decode((string) $row['pecahan_json'], true);
        if (!is_array($decoded)) {
            return [];
        }

        $out = [];
        foreach ($decoded as $k => $v) {
            $denom = (int) $k;
            if ($denom > 0) {
                $out[(string) $denom] = max(0, (int) $v);
            }
        }

        return $out;
    }

    public function saveVaultPecahan(int $vaultId, array $denomMap): bool
    {
        if ($vaultId <= 0 || !$this->findVault($vaultId)) {
            return false;
        }

        $clean = [];
        foreach ($denomMap as $k => $v) {
            $denom = (int) $k;
            if ($denom > 0) {
                $clean[$denom] = max(0, (int) $v);
            }
        }

        $json = json_encode($clean, JSON_UNESCAPED_UNICODE);
        if ($json === false || strlen($json) > 65535) {
            return false;
        }

        $now = date('Y-m-d H:i:s');
        $driver = $this->db->getAttribute(PDO::ATTR_DRIVER_NAME);

        if ($driver === 'mysql') {
            $statement = $this->db->prepare('
                INSERT INTO vault_pecahan (vault_id, pecahan_json, updated_at)
                VALUES (:vault_id, :pecahan_json, :updated_at)
                ON DUPLICATE KEY UPDATE pecahan_json = VALUES(pecahan_json), updated_at = VALUES(updated_at)
            ');
            $statement->execute([
                'vault_id' => $vaultId,
                'pecahan_json' => $json,
                'updated_at' => $now,
            ]);
        } else {
            $statement = $this->db->prepare('
                INSERT OR REPLACE INTO vault_pecahan (vault_id, pecahan_json, updated_at)
                VALUES (:vault_id, :pecahan_json, :updated_at)
            ');
            $statement->execute([
                'vault_id' => $vaultId,
                'pecahan_json' => $json,
                'updated_at' => $now,
            ]);
        }

        return true;
    }

    public function saveVault(array $data): void
    {
        if (!empty($data['id'])) {
            $sql = "UPDATE vaults SET bank_name=:bank_name, balance=:balance WHERE id=:id";
            $params = [
                'id' => $data['id'],
                'bank_name' => $data['bank_name'],
                'balance' => $data['balance'],
            ];
        } else {
            $sql = "INSERT INTO vaults (bank_name, balance, created_at) VALUES (:bank_name, :balance, :created_at)";
            $params = [
                'bank_name' => $data['bank_name'],
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

    public function findVaultTransaction(int $transactionId): array|false
    {
        $statement = $this->db->prepare("SELECT * FROM vault_transactions WHERE id = :id");
        $statement->execute(['id' => $transactionId]);
        return $statement->fetch(PDO::FETCH_ASSOC);
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
            $targetVaultId = 0; // Pastikan target kosong untuk pembelian
        } elseif ($type === 'dana_masuk') {
            if ($targetVaultId <= 0) {
                $this->db->rollBack();
                return false;
            }

            $this->updateVaultBalance($targetVaultId, $amount);
            $sourceVaultId = 0; // Pastikan source kosong untuk dana masuk
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
            'transaction_date' => !empty($data['transaction_date']) ? $data['transaction_date'] : date('Y-m-d'),
            'created_at' => date('Y-m-d H:i:s'),
        ]);

        $this->db->commit();
        return true;
    }

    public function deleteVaultTransaction(int $transactionId): bool
    {
        $transaction = $this->findVaultTransaction($transactionId);

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
                target.bank_name AS target_bank_name
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
        $finalBalance = (float) ($vault['balance'] ?? 0);

        $manualStatement = $this->db->prepare("
            SELECT
                vt.id, vt.transaction_date, vt.created_at, vt.amount, vt.notes,
                vt.transaction_type, vt.source_vault_id, vt.target_vault_id,
                source.bank_name AS source_bank_name,
                target.bank_name AS target_bank_name,
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
                sale_items.id, sales.transaction_date, sales.created_at,
                sale_items.line_profit AS amount, sales.invoice_no AS notes,
                'penjualan' AS transaction_type, NULL AS source_vault_id,
                COALESCE(sale_items.vault_id, sales.vault_id) AS target_vault_id,
                CASE WHEN sales.payment_type = 'Tunai' THEN 'CASH'
                     WHEN sales.payment_type = 'QRIS' THEN 'QRIS'
                     ELSE COALESCE(sales.payment_type, '') END AS source_bank_name,
                vaults.bank_name AS target_bank_name,
                'transaksi' AS source_module
            FROM sale_items
            INNER JOIN sales ON sales.id = sale_items.sale_id
            LEFT JOIN vaults ON vaults.id = COALESCE(sale_items.vault_id, sales.vault_id)
            WHERE (sale_items.vault_id = :vault_id OR sales.vault_id = :vault_id)
            AND sales.payment_type != 'Hutang'
        ");
        $salesStatement->execute(['vault_id' => $vaultId]);
        $salesRows = $salesStatement->fetchAll(PDO::FETCH_ASSOC);

        $debtPaymentStatement = $this->db->prepare("
            SELECT
                debt_payments.id, debt_payments.payment_date AS transaction_date,
                debt_payments.payment_date AS created_at, debt_payments.amount,
                CASE WHEN COALESCE(sales.invoice_no, '') <> '' THEN sales.invoice_no
                     ELSE 'Pelunasan Hutang' END AS notes,
                'pelunasan_hutang' AS transaction_type, 'PELANGGAN HUTANG' AS source_bank_name,
                vaults.bank_name AS target_bank_name, debt_payments.vault_id AS target_vault_id,
                'hutang' AS source_module
            FROM debt_payments
            INNER JOIN debts ON debts.id = debt_payments.debt_id
            LEFT JOIN sales ON sales.id = debts.sale_id
            LEFT JOIN vaults ON vaults.id = debt_payments.vault_id
            WHERE debt_payments.vault_id = :vault_id
        ");
        $debtPaymentStatement->execute(['vault_id' => $vaultId]);
        $debtPaymentRows = $debtPaymentStatement->fetchAll(PDO::FETCH_ASSOC);

        $rows = array_merge($manualRows, $salesRows, $debtPaymentRows);
        
        // Urutkan dari TERTUA ke TERBARU
        usort($rows, function (array $a, array $b): int {
            $timeA = $a['created_at'] ?? '';
            $timeB = $b['created_at'] ?? '';
            if ($timeA === $timeB) {
                return (int)($a['id'] ?? 0) - (int)($b['id'] ?? 0);
            }
            return strcmp($timeA, $timeB);
        });

        // Hitung total mutasi untuk mencari Saldo Awal (Working backwards)
        $totalDebet = 0;
        $totalKredit = 0;
        foreach ($rows as $row) {
            $isTarget = (int) ($row['target_vault_id'] ?? 0) === $vaultId;
            $isSource = (int) ($row['source_vault_id'] ?? 0) === $vaultId;
            if (in_array((string) ($row['transaction_type'] ?? ''), ['penjualan', 'pelunasan_hutang'], true)) {
                $isTarget = true; $isSource = false;
            }
            
            if ($isTarget) $totalDebet += (float)($row['amount'] ?? 0);
            if ($isSource) $totalKredit += (float)($row['amount'] ?? 0);
        }
        
        $startingBalance = $finalBalance - $totalDebet + $totalKredit;
        
        // Hitung Saldo Berjalan (Running Balance) dari Atas ke Bawah
        $currentRunning = $startingBalance;
        foreach ($rows as &$row) {
            $type = (string) ($row['transaction_type'] ?? '');
            $isTarget = (int) ($row['target_vault_id'] ?? 0) === $vaultId;
            $isSource = (int) ($row['source_vault_id'] ?? 0) === $vaultId;

            // Prioritaskan jenis transaksi untuk menentukan arah uang
            if (in_array($type, ['penjualan', 'pelunasan_hutang', 'dana_masuk'], true)) {
                $isTarget = true; $isSource = false;
            } elseif ($type === 'pembelian') {
                $isTarget = false; $isSource = true;
            }

            // User Request: Kredit = Masuk, Debet = Keluar (Standar Perbankan/Buku Tabungan)
            $kredit = $isTarget ? (float) ($row['amount'] ?? 0) : 0;
            $debet = $isSource ? (float) ($row['amount'] ?? 0) : 0;

            $currentRunning = $currentRunning + $kredit - $debet;
            
            $row['debet'] = $debet;
            $row['kredit'] = $kredit;
            $row['ending_balance'] = $currentRunning;
        }
        unset($row);

        return $rows;
    }

    public function findVaultByKeyword(string $keyword): array|false
    {
        $statement = $this->db->prepare("SELECT * FROM vaults WHERE bank_name LIKE :keyword ORDER BY id DESC LIMIT 1");
        $statement->execute([
            'keyword' => '%' . $keyword . '%',
        ]);
        return $statement->fetch(PDO::FETCH_ASSOC);
    }

    public function findPrimaryModalVault(): array|false
    {
        $exactStatement = $this->db->prepare("
            SELECT *
            FROM vaults
            WHERE UPPER(COALESCE(bank_name, '')) = :bank_name
            ORDER BY id ASC
            LIMIT 1
        ");
        $exactStatement->execute(['bank_name' => 'SALDO MODAL #1']);
        $exact = $exactStatement->fetch(PDO::FETCH_ASSOC);
        if ($exact) {
            return $exact;
        }

        return $this->findVaultByKeyword('SALDO MODAL');
    }

    public function debts(string $keyword = ''): array
    {
        $sql = "SELECT debts.*, customers.name AS customer_name, sales.invoice_no,
                       (debts.total_debt - debts.paid_amount) AS remaining_debt
                FROM debts
                LEFT JOIN customers ON customers.id = debts.customer_id
                LEFT JOIN sales ON sales.id = debts.sale_id
                WHERE (debts.total_debt - debts.paid_amount) > 0";
        $params = [];
        if (trim($keyword) !== '') {
            $sql .= " AND (customers.name LIKE :keyword OR sales.invoice_no LIKE :keyword OR debts.status LIKE :keyword)";
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

        // Jika lunas, update tanggal transaksi di sales agar muncul di history tgl berjalan
        if ($paymentAmount + (float) $debt['paid_amount'] >= (float) $debt['total_debt']) {
            $updateSaleDate = $this->db->prepare("UPDATE sales SET transaction_date = :today WHERE id = :sale_id");
            $updateSaleDate->execute([
                'today' => date('Y-m-d'),
                'sale_id' => $debt['sale_id'],
            ]);
        }

        $this->updateVaultBalance($vaultId, $paymentAmount);
        $this->db->commit();
        return true;
    }

    public function deleteDebt(int $debtId): bool
    {
        $debtStatement = $this->db->prepare("SELECT * FROM debts WHERE id = :id");
        $debtStatement->execute(['id' => $debtId]);
        $debt = $debtStatement->fetch(PDO::FETCH_ASSOC);

        if (!$debt) {
            return false;
        }

        $paymentsStatement = $this->db->prepare("SELECT * FROM debt_payments WHERE debt_id = :debt_id");
        $paymentsStatement->execute(['debt_id' => $debtId]);
        $payments = $paymentsStatement->fetchAll(PDO::FETCH_ASSOC);

        $this->db->beginTransaction();

        foreach ($payments as $payment) {
            $vaultId = (int) ($payment['vault_id'] ?? 0);
            $amount = (float) ($payment['amount'] ?? 0);
            if ($vaultId > 0 && $amount > 0) {
                $this->updateVaultBalance($vaultId, -1 * $amount);
            }
        }

        $deletePayments = $this->db->prepare("DELETE FROM debt_payments WHERE debt_id = :debt_id");
        $deletePayments->execute(['debt_id' => $debtId]);

        $deleteDebt = $this->db->prepare("DELETE FROM debts WHERE id = :id");
        $deleteDebt->execute(['id' => $debtId]);

        $this->db->commit();
        return true;
    }

    public function createManualDebt(array $data): bool
    {
        $customerId = (int) ($data['customer_id'] ?? 0);
        $totalDebt = max(0, (float) ($data['total_debt'] ?? 0));
        $dueDate = trim((string) ($data['due_date'] ?? ''));
        $notes = trim((string) ($data['notes'] ?? ''));

        if ($customerId <= 0 || $totalDebt <= 0) {
            return false;
        }

        $statement = $this->db->prepare("
            INSERT INTO debts (sale_id, customer_id, total_debt, paid_amount, due_date, status, created_at)
            VALUES (0, :customer_id, :total_debt, 0, :due_date, 'Belum Lunas', :created_at)
        ");

        return $statement->execute([
            'customer_id' => $customerId,
            'total_debt' => $totalDebt,
            'due_date' => $dueDate !== '' ? $dueDate : null,
            'created_at' => date('Y-m-d H:i:s'),
        ]);
    }
}
