<?php
declare(strict_types=1);

class Layanan_controller extends Controller
{
    public function index(): void
    {
        $model = $this->model('Layanan_model');
        $keuanganModel = $this->model('Keuangan_model');
        $keyword = trim((string) ($_GET['q'] ?? ''));
        $modalVault = $keuanganModel->findPrimaryModalVault();

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            if (post('action') === 'delete_service') {
                $deleted = $model->delete((int) post('service_id'));
                if ($deleted && $modalVault) {
                    $keuanganModel->updateVaultBalance((int) $modalVault['id'], (float) $deleted['buy_price']);
                }
                if ($deleted && !empty($deleted['vault_id'])) {
                    $keuanganModel->updateVaultBalance((int) $deleted['vault_id'], -1 * (float) $deleted['profit']);
                }
                flash($deleted ? 'Transaksi layanan berhasil dihapus.' : 'Transaksi layanan tidak ditemukan.', $deleted ? 'success' : 'warning');
                $this->redirect('layanan');
            }

            $sellPrice = unformat_number((string) post('sell_price'));
            $buyPrice = unformat_number((string) post('buy_price'));
            $paymentType = post('payment_type');
            $vaultId = (int) post('vault_id');
            $profit = max(0, $sellPrice - $buyPrice);

            $receipt = [
                'code' => $model->nextCode(),
                'service_type' => post('service_type'),
                'customer_id' => 0,
                'customer_name' => '',
                'customer_phone' => '',
                'target_number' => '-',
                'nominal' => $sellPrice,
                'buy_price' => $buyPrice,
                'sell_price' => $sellPrice,
                'profit' => $profit,
                'payment_type' => $paymentType,
                'vault_id' => $vaultId,
                'token_number' => post('token_number'),
            ];

            $model->save($receipt);

            if ($modalVault && $buyPrice > 0) {
                $keuanganModel->updateVaultBalance((int) $modalVault['id'], -1 * $buyPrice);
            }
            if ($vaultId > 0 && $profit > 0) {
                $keuanganModel->updateVaultBalance($vaultId, $profit);
            }

            $_SESSION['service_receipt'] = $receipt;
            flash('Transaksi layanan berhasil disimpan.');
            $this->redirect('layanan');
        }

        $this->view('layanan/index', [
            'title' => 'Top Up & PPOB',
            'nextCode' => $model->nextCode(),
            'services' => $model->all($keyword),
            'vaults' => $keuanganModel->allVaults(),
            'modalVault' => $modalVault,
            'serviceReceipt' => $_SESSION['service_receipt'] ?? null,
            'keyword' => $keyword,
            'flash' => flash(),
        ]);
        unset($_SESSION['service_receipt']);
    }
}
