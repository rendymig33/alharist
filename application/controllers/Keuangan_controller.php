<?php
declare(strict_types=1);

class Keuangan_controller extends Controller
{
    public function brankas(): void
    {
        $model = $this->model('Keuangan_model');
        $editVault = null;
        $keyword = trim((string) ($_GET['q'] ?? ''));
        $activeTransactionVaultId = isset($_GET['transaksi']) ? (int) $_GET['transaksi'] : 0;

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            if (post('action') === 'save_transaction') {
                $targetVaultId = (int) post('target_vault_id', 0);
                $sourceVaultId = (int) post('source_vault_id', 0);
                $redirectVaultId = $targetVaultId > 0 ? $targetVaultId : $sourceVaultId;
                $saved = $model->recordVaultTransaction([
                    'transaction_type' => post('transaction_type'),
                    'source_vault_id' => $sourceVaultId,
                    'target_vault_id' => $targetVaultId,
                    'amount' => unformat_number((string) post('amount')),
                    'notes' => post('notes'),
                ]);

                flash($saved ? 'Transaksi brankas berhasil disimpan.' : 'Transaksi brankas tidak valid.', $saved ? 'success' : 'warning');
                $query = ['route' => 'keuangan/brankas'];
                if ($redirectVaultId > 0) {
                    $query['transaksi'] = $redirectVaultId;
                }
                if ($keyword !== '') {
                    $query['q'] = $keyword;
                }
                header('Location: index.php?' . http_build_query($query));
                exit;
            }

            if (post('action') === 'delete_transaction') {
                $redirectVaultId = (int) post('vault_id', 0);
                $deleted = $model->deleteVaultTransaction((int) post('transaction_id'));
                flash($deleted ? 'Transaksi brankas berhasil dihapus.' : 'Transaksi brankas tidak ditemukan.', $deleted ? 'success' : 'warning');
                $query = ['route' => 'keuangan/brankas'];
                if ($redirectVaultId > 0) {
                    $query['transaksi'] = $redirectVaultId;
                }
                if ($keyword !== '') {
                    $query['q'] = $keyword;
                }
                header('Location: index.php?' . http_build_query($query));
                exit;
            }

            $model->saveVault([
                'id' => post('id'),
                'bank_name' => post('bank_name'),
                'balance' => unformat_number((string) post('balance')),
            ]);

            flash('Data brankas berhasil disimpan.');
            $this->redirect('keuangan/brankas');
        }

        $vaults = $model->allVaults($keyword);
        $totalBalance = array_sum(array_map(fn(array $vault): float => (float) ($vault['balance'] ?? 0), $model->allVaults()));
        $filteredBalance = array_sum(array_map(fn(array $vault): float => (float) ($vault['balance'] ?? 0), $vaults));

        if (!empty($_GET['edit'])) {
            foreach ($model->allVaults() as $vault) {
                if ((int) $vault['id'] === (int) $_GET['edit']) {
                    $editVault = $vault;
                    break;
                }
            }
        }

        $this->view('keuangan/brankas', [
            'title' => 'Brankas',
            'vaults' => $vaults,
            'editVault' => $editVault,
            'keyword' => $keyword,
            'totalBalance' => $totalBalance,
            'filteredBalance' => $filteredBalance,
            'activeTransactionVaultId' => $activeTransactionVaultId,
            'flash' => flash(),
            'transactionsByVault' => array_reduce($vaults, function (array $carry, array $vault) use ($model): array {
                $carry[(int) $vault['id']] = $model->transactionsByVault((int) $vault['id']);
                return $carry;
            }, []),
        ]);
    }

    public function hutang(): void
    {
        $model = $this->model('Keuangan_model');
        $keyword = trim((string) ($_GET['q'] ?? ''));

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            if (post('action') === 'create_manual_debt') {
                $saved = $model->createManualDebt([
                    'customer_id' => (int) post('customer_id', 0),
                    'total_debt' => unformat_number((string) post('total_debt')),
                    'due_date' => post('due_date'),
                    'notes' => post('notes'),
                ]);
                flash($saved ? 'Hutang manual berhasil ditambahkan.' : 'Hutang manual tidak valid.', $saved ? 'success' : 'warning');
                $this->redirect('keuangan/hutang');
            }

            $debtId = (int) post('debt_id');
            $paymentMode = post('payment_mode', 'partial');
            $vaultId = (int) post('vault_id', 0);
            $amount = unformat_number((string) post('amount'));

            if ($paymentMode === 'full') {
                foreach ($model->debts() as $debt) {
                    if ((int) ($debt['id'] ?? 0) === $debtId) {
                        $amount = max(0, (float) ($debt['remaining_debt'] ?? 0));
                        break;
                    }
                }
            }

            $saved = $model->recordDebtPayment($debtId, $amount, $vaultId, post('notes'));
            flash($saved ? 'Pembayaran hutang berhasil dicatat.' : 'Pembayaran hutang tidak valid.', $saved ? 'success' : 'warning');
            $this->redirect('keuangan/hutang');
        }

        $this->view('keuangan/hutang', [
            'title' => 'Pencatatan Utang',
            'debts' => $model->debts($keyword),
            'vaults' => $model->allVaults(),
            'customers' => $this->model('Pelanggan_model')->all(),
            'keyword' => $keyword,
            'flash' => flash(),
        ]);
    }
}
