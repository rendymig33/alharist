<?php
declare(strict_types=1);

class Keuangan_controller extends Controller
{
    public function brankas(): void
    {
        $model = $this->model('Keuangan_model');

        if (isset($_GET['ajax_balance']) && ($_GET['vault_id'] ?? 0) > 0) {
            $ajaxVaultId = (int) $_GET['vault_id'];
            $ajaxVault = $model->findVault($ajaxVaultId);
            header('Content-Type: application/json');
            echo json_encode([
                'balance' => $ajaxVault ? (float) $ajaxVault['balance'] : 0,
            ], JSON_UNESCAPED_UNICODE);
            exit;
        }

        $editVault = null;
        $keyword = trim((string) ($_GET['q'] ?? ''));
        $currentPage = (int) ($_GET['p'] ?? 1);
        $activeTransactionVaultId = isset($_GET['transaksi']) ? (int) $_GET['transaksi'] : 0;

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $isAjax = (string) ($_SERVER['HTTP_X_REQUESTED_WITH'] ?? '') === 'XMLHttpRequest';
            $action = post('action');

            if ($action === 'save_transaction') {
                $type = (string) post('transaction_type');
                $sourceVaultId = (int) post('source_vault_id', 0);
                $targetVaultId = (int) post('target_vault_id', 0);
                $activeVaultId = (int) post('active_vault_id', 0);
                
                // Jika active_vault_id kosong (misal belum refresh), gunakan salah satu ID yang ada
                if ($activeVaultId <= 0) $activeVaultId = $sourceVaultId ?: $targetVaultId;

                // Paksa pemetaan sesuai logika "Brankas Aktif"
                if ($type === 'dana_masuk') {
                    $targetVaultId = $activeVaultId;
                    $sourceVaultId = 0;
                } elseif ($type === 'pembelian') {
                    $sourceVaultId = $activeVaultId;
                    $targetVaultId = 0;
                } elseif ($type === 'switching_dana') {
                    $sourceVaultId = $activeVaultId;
                    // targetVaultId tetap dari input select
                }

                $redirectVaultId = $activeVaultId;
                
                // Robust unformatting untuk nominal bertitik
                $amountRaw = (string) post('amount');
                $amount = (float) str_replace(['.', ','], ['', '.'], $amountRaw);

                $saved = $model->recordVaultTransaction([
                    'transaction_type' => $type,
                    'source_vault_id' => $sourceVaultId,
                    'target_vault_id' => $targetVaultId,
                    'amount' => $amount,
                    'notes' => post('notes'),
                    'transaction_date' => post('transaction_date'),
                ]);

                if ($isAjax) {
                    // Bersihkan semua output sebelumnya agar JSON tidak korup
                    while (ob_get_level() > 0) ob_end_clean();
                    ob_start();
                    
                    $vault = $model->findVault($redirectVaultId);
                    $history = $model->transactionsByVault($redirectVaultId);
                    
                    // Render HTML baris riwayat
                    $vaultId = $redirectVaultId;
                    $rowHtml = '';
                    foreach ($history as $transaction) {
                        ob_start();
                        include 'application/views/keuangan/brankas_history_row.php';
                        $rowHtml .= ob_get_clean();
                    }
                    
                    // Buang semua output yang tidak sengaja tercetak
                    ob_end_clean();
                    
                    header('Content-Type: application/json');
                    echo json_encode([
                        'success' => $saved,
                        'message' => $saved ? 'Transaksi berhasil disimpan.' : 'Transaksi tidak valid (nominal 0 atau jenis transaksi salah).',
                        'new_balance' => $vault ? (float) $vault['balance'] : 0,
                        'history_html' => $rowHtml,
                    ], JSON_UNESCAPED_UNICODE);
                    exit;
                }

                flash($saved ? 'Transaksi brankas berhasil disimpan.' : 'Transaksi brankas tidak valid.', $saved ? 'success' : 'warning');
                $query = ['route' => 'keuangan/brankas', 'p' => $currentPage];
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
                    $vault = $model->findVault($redirectVaultId);
                    header('Content-Type: application/json');
                    echo json_encode([
                        'success' => $deleted,
                        'message' => $deleted ? 'Transaksi brankas berhasil dihapus.' : 'Transaksi brankas tidak ditemukan.',
                        'new_balance' => $vault ? $vault['balance'] : 0,
                    ]);
                    exit;
                }

                flash($deleted ? 'Transaksi brankas berhasil dihapus.' : 'Transaksi brankas tidak ditemukan.', $deleted ? 'success' : 'warning');
                $query = ['route' => 'keuangan/brankas', 'p' => $currentPage];
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
