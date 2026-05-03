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
            $isAjax = (string) ($_SERVER['HTTP_X_REQUESTED_WITH'] ?? '') === 'XMLHttpRequest';

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

                if ($isAjax) {
                    header('Content-Type: application/json');
                    echo json_encode([
                        'success' => $saved,
                        'message' => $saved ? 'Transaksi brankas berhasil disimpan.' : 'Transaksi brankas tidak valid.',
                    ]);
                    exit;
                }

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

                if ($isAjax) {
                    header('Content-Type: application/json');
                    echo json_encode([
                        'success' => $deleted,
                        'message' => $deleted ? 'Transaksi brankas berhasil dihapus.' : 'Transaksi brankas tidak ditemukan.',
                    ]);
                    exit;
                }

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

        $all_vaults = $model->allVaults($keyword);
        $limit = 6;
        $totalItems = count($all_vaults);
        $totalPages = (int) ceil($totalItems / $limit);
        $currentPage = max(1, min((int) ($_GET['p'] ?? 1), max(1, $totalPages)));
        $offset = ($currentPage - 1) * $limit;
        $vaults = array_slice($all_vaults, $offset, $limit);

        $totalBalance = array_sum(array_map(fn(array $v): float => (float) ($v['balance'] ?? 0), $model->allVaults()));
        $filteredBalance = array_sum(array_map(fn(array $v): float => (float) ($v['balance'] ?? 0), $all_vaults));

        if (!empty($_GET['edit'])) {
            foreach ($all_vaults as $v) {
                if ((int) $v['id'] === (int) $_GET['edit']) {
                    $editVault = $v;
                    break;
                }
            }
        }

        $this->view('keuangan/brankas', [
            'title' => 'Brankas',
            'vaults' => $vaults,
            'totalItems' => $totalItems,
            'totalPages' => $totalPages,
            'currentPage' => $currentPage,
            'editVault' => $editVault,
            'keyword' => $keyword,
            'totalBalance' => $totalBalance,
            'filteredBalance' => $filteredBalance,
            'activeTransactionVaultId' => $activeTransactionVaultId,
            'flash' => flash(),
            'transactionsByVault' => array_reduce($vaults, function (array $carry, array $v) use ($model): array {
                $carry[(int) $v['id']] = $model->transactionsByVault((int) $v['id']);
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

            if (post('action') === 'delete_debt') {
                $deleted = $model->deleteDebt((int) post('debt_id'));
                flash($deleted ? 'Transaksi hutang berhasil dihapus.' : 'Transaksi hutang tidak ditemukan.', $deleted ? 'success' : 'warning');
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

        $all_debts = $model->debts($keyword);
        $limit = 5;
        $totalItems = count($all_debts);
        $totalPages = (int) ceil($totalItems / $limit);
        $currentPage = max(1, min((int) ($_GET['p'] ?? 1), max(1, $totalPages)));
        $offset = ($currentPage - 1) * $limit;
        $debts = array_slice($all_debts, $offset, $limit);

        $this->view('keuangan/hutang', [
            'title' => 'Pencatatan Utang',
            'debts' => $debts,
            'totalItems' => $totalItems,
            'totalPages' => $totalPages,
            'currentPage' => $currentPage,
            'vaults' => $model->allVaults(),
            'customers' => $this->model('Pelanggan_model')->all(),
            'keyword' => $keyword,
            'flash' => flash(),
        ]);
    }
}
