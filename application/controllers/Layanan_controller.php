<?php
declare(strict_types=1);

class Layanan_controller extends Controller
{
    public function index(): void
    {
        $model = $this->model('Layanan_model');
        $keuanganModel = $this->model('Keuangan_model');
        $pelangganModel = $this->model('Pelanggan_model');
        $keyword = trim((string) ($_GET['q'] ?? ''));

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            if (post('action') === 'delete_service') {
                $deleted = $model->delete((int) post('service_id'));
                if ($deleted && !empty($deleted['vault_id'])) {
                    $keuanganModel->updateVaultBalance((int) $deleted['vault_id'], (float) $deleted['buy_price']);
                }
                if ($deleted && $deleted['payment_type'] === 'QRIS') {
                    $qrisVault = $keuanganModel->findVaultByKeyword('QRIS');
                    if ($qrisVault) {
                        $keuanganModel->updateVaultBalance((int) $qrisVault['id'], -1 * (float) $deleted['sell_price']);
                    }
                }
                flash($deleted ? 'Transaksi layanan berhasil dihapus.' : 'Transaksi layanan tidak ditemukan.', $deleted ? 'success' : 'warning');
                $this->redirect('layanan');
            }

            $sellPrice = unformat_number((string) post('sell_price'));
            $buyPrice = unformat_number((string) post('buy_price'));
            $paymentType = post('payment_type');
            $vaultId = (int) post('vault_id');
            $customerId = (int) post('customer_id');
            $selectedCustomer = null;

            foreach ($pelangganModel->all() as $customer) {
                if ((int) $customer['id'] === $customerId) {
                    $selectedCustomer = $customer;
                    break;
                }
            }

            $receipt = [
                'code' => $model->nextCode(),
                'service_type' => post('service_type'),
                'customer_id' => $customerId,
                'customer_name' => $selectedCustomer['name'] ?? '',
                'customer_phone' => $selectedCustomer['phone'] ?? '',
                'target_number' => post('target_number'),
                'nominal' => unformat_number((string) post('nominal')),
                'buy_price' => $buyPrice,
                'sell_price' => $sellPrice,
                'profit' => $sellPrice - $buyPrice,
                'payment_type' => $paymentType,
                'vault_id' => $vaultId,
                'token_number' => post('token_number'),
            ];

            $model->save($receipt);

            if ($vaultId > 0) {
                $keuanganModel->updateVaultBalance($vaultId, -1 * $buyPrice);
            }
            if ($paymentType === 'QRIS') {
                $qrisVault = $keuanganModel->findVaultByKeyword('QRIS');
                if ($qrisVault) {
                    $keuanganModel->updateVaultBalance((int) $qrisVault['id'], $sellPrice);
                }
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
            'customers' => $pelangganModel->all(),
            'serviceReceipt' => $_SESSION['service_receipt'] ?? null,
            'keyword' => $keyword,
            'flash' => flash(),
        ]);
        unset($_SESSION['service_receipt']);
    }
}
