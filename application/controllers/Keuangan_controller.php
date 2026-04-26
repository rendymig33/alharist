<?php
declare(strict_types=1);

class Keuangan_controller extends Controller
{
    public function brankas(): void
    {
        $model = $this->model('Keuangan_model');
        $editVault = null;

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            if (post('action') === 'save_transaction') {
                $saved = $model->recordVaultTransaction([
                    'transaction_type' => post('transaction_type'),
                    'source_vault_id' => (int) post('source_vault_id', 0),
                    'target_vault_id' => (int) post('target_vault_id', 0),
                    'amount' => unformat_number((string) post('amount')),
                    'notes' => post('notes'),
                ]);

                flash($saved ? 'Transaksi brankas berhasil disimpan.' : 'Transaksi brankas tidak valid.', $saved ? 'success' : 'warning');
                $this->redirect('keuangan/brankas');
            }

            $model->saveVault([
                'id' => post('id'),
                'bank_name' => post('bank_name'),
                'account_name' => post('account_name'),
                'balance' => unformat_number((string) post('balance')),
            ]);

            flash('Data brankas berhasil disimpan.');
            $this->redirect('keuangan/brankas');
        }

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
            'vaults' => $model->allVaults(),
            'editVault' => $editVault,
            'flash' => flash(),
            'transactionsByVault' => array_reduce($model->allVaults(), function (array $carry, array $vault) use ($model): array {
                $carry[(int) $vault['id']] = $model->transactionsByVault((int) $vault['id']);
                return $carry;
            }, []),
        ]);
    }

    public function hutang(): void
    {
        $model = $this->model('Keuangan_model');

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $amount = (float) post('amount');
            $model->recordDebtPayment((int) post('debt_id'), $amount, post('notes'));
            flash('Pembayaran hutang berhasil dicatat.');
            $this->redirect('keuangan/hutang');
        }

        $this->view('keuangan/hutang', [
            'title' => 'Pencatatan Utang',
            'debts' => $model->debts(),
            'flash' => flash(),
        ]);
    }
}
