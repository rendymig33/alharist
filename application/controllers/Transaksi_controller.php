<?php
declare(strict_types=1);

class Transaksi_controller extends Controller
{
    public function index(): void
    {
        $barangModel = $this->model('Barang_model');
        $esaldoModel = $this->model('Esaldo_model');
        $pelangganModel = $this->model('Pelanggan_model');
        $keuanganModel = $this->model('Keuangan_model');
        $transaksiModel = $this->model('Transaksi_model');

        $_SESSION['cart'] ??= [];
        $_SESSION['transaction_mode'] ??= 'biasa';

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $action = post('action');
            $postedMode = trim((string) post('transaction_mode', $_SESSION['transaction_mode']));
            if (in_array($postedMode, ['biasa', 'esaldo'], true)) {
                $_SESSION['transaction_mode'] = $postedMode;
            }

            if ($action === 'delete_sale') {
                $deleted = $transaksiModel->deleteSale((int) post('sale_id'));
                flash($deleted ? 'Transaksi berhasil dihapus.' : 'Transaksi tidak ditemukan.', $deleted ? 'success' : 'warning');
                $this->redirect('transaksi');
            }

            if ($action === 'add_item') {
                $item = $barangModel->find((int) post('item_id'));
                if ($item) {
                    $qty = max(1, (int) post('qty'));
                    $purchaseMode = post('purchase_mode', 'besar');
                    $smallUnitQty = max(1, (int) ($item['small_unit_qty'] ?? 1));
                    $costPerSmall = (float) ($item['cost_per_small'] ?? 0);
                    $stockUsed = $qty * $smallUnitQty;
                    $sellingPrice = (float) $item['selling_price'];
                    $purchaseCost = $costPerSmall;
                    $purchaseLabel = (string) $item['unit_large'];

                    if ($purchaseMode === 'eceran' && !empty($item['allow_small_sale'])) {
                        $stockUsed = $qty;
                        $sellingPrice = (float) $item['unit_price'];
                        $purchaseLabel = (string) $item['unit_small'];
                    } elseif ($purchaseMode === 'setengah' && !empty($item['allow_half_sale'])) {
                        $stockUsed = (int) ceil($smallUnitQty / 2) * $qty;
                        $sellingPrice = (float) ($item['half_price'] ?? 0);
                        $purchaseLabel = 'Setengah ' . $item['unit_large'];
                    }

                    if ($stockUsed > (float) $item['stock']) {
                        flash('Qty melebihi stok yang tersedia.', 'warning');
                    } else {
                        $lineTotal = $qty * $sellingPrice;
                        $lineCost = $stockUsed * $purchaseCost;
                        $promoLabel = '';

                        if ($purchaseMode === 'eceran') {
                            $promoRules = [
                                ['qty' => (int) ($item['promo_qty_3'] ?? 0), 'price' => (float) ($item['promo_price_3'] ?? 0)],
                                ['qty' => (int) ($item['promo_qty_2'] ?? 0), 'price' => (float) ($item['promo_price_2'] ?? 0)],
                                ['qty' => (int) ($item['promo_qty_1'] ?? 0), 'price' => (float) ($item['promo_price_1'] ?? 0)],
                            ];

                            $remainingQty = $qty;
                            $lineTotal = 0;
                            $promoParts = [];

                            foreach ($promoRules as $rule) {
                                if ($rule['qty'] <= 0 || $rule['price'] <= 0) {
                                    continue;
                                }

                                $bundleCount = intdiv($remainingQty, $rule['qty']);
                                if ($bundleCount <= 0) {
                                    continue;
                                }

                                $lineTotal += $bundleCount * $rule['price'];
                                $remainingQty = $remainingQty % $rule['qty'];
                                $promoParts[] = $bundleCount . 'x promo ' . $rule['qty'] . ' ' . $item['unit_small'] . ' = ' . rupiah($rule['price']);
                            }

                            $lineTotal += $remainingQty * (float) $item['unit_price'];
                            $sellingPrice = $qty > 0 ? $lineTotal / $qty : 0;
                            $promoLabel = !empty($promoParts) ? implode(' | ', $promoParts) : '';
                        }

                        $_SESSION['cart'][] = [
                            'item_id' => (int) $item['id'],
                            'vault_id' => 0,
                            'name' => $item['name'],
                            'qty' => $stockUsed,
                            'display_qty' => $qty,
                            'stock_display' => format_stock_breakdown($stockUsed, (string) $item['unit_large'], (string) $item['unit_small'], $smallUnitQty),
                            'purchase_label' => $purchaseLabel,
                            'promo_label' => $promoLabel,
                            'purchase_price' => $purchaseCost,
                            'selling_price' => $sellingPrice,
                            'line_total' => $lineTotal,
                            'line_profit' => $lineTotal - $lineCost,
                        ];
                        flash('Barang ditambahkan ke transaksi.');
                    }
                }
                $this->redirect('transaksi');
            }

            if ($action === 'add_esaldo') {
                $item = $esaldoModel->find((int) post('item_id'));
                if ($item) {
                    $buyPrice = unformat_number((string) post('manual_buy_price'));
                    $sellPrice = unformat_number((string) post('manual_sell_price'));
                    $targetNumber = trim((string) post('target_number'));

                    if ($buyPrice <= 0 || $sellPrice <= 0) {
                        flash('Modal dan harga jual E-Transaction wajib diisi.', 'warning');
                    } else {
                        $_SESSION['cart'][] = [
                            'item_id' => (int) $item['id'],
                            'vault_id' => 0,
                            'name' => $item['name'],
                            'qty' => 1,
                            'display_qty' => 1,
                            'stock_display' => $targetNumber,
                            'purchase_label' => 'E-Transaction',
                            'promo_label' => $targetNumber !== '' ? 'Tujuan: ' . $targetNumber : '',
                            'purchase_price' => $buyPrice,
                            'selling_price' => $sellPrice,
                            'line_total' => $sellPrice,
                            'line_profit' => $sellPrice - $buyPrice,
                            'is_esaldo' => 1,
                        ];
                        flash('E-Saldo ditambahkan ke transaksi.');
                    }
                }
                $this->redirect('transaksi');
            }

            if ($action === 'update_item_vault') {
                $index = (int) post('index');
                $vaultId = (int) post('vault_id', 0);

                if (isset($_SESSION['cart'][$index])) {
                    $_SESSION['cart'][$index]['vault_id'] = $vaultId;
                    flash('Tujuan dana item berhasil diperbarui.');
                }

                $this->redirect('transaksi');
            }

            if ($action === 'remove_item') {
                unset($_SESSION['cart'][(int) post('index')]);
                $_SESSION['cart'] = array_values($_SESSION['cart']);
                flash('Item transaksi dihapus.', 'warning');
                $this->redirect('transaksi');
            }

            if ($action === 'checkout' && !empty($_SESSION['cart'])) {
                $subtotal = array_sum(array_column($_SESSION['cart'], 'line_total'));
                $totalProfit = array_sum(array_column($_SESSION['cart'], 'line_profit'));
                $paymentType = post('payment_type');
                $cashPaid = unformat_number((string) post('cash_paid'));
                $vaultId = 0;
                $customerId = (int) post('customer_id', 0);
                $dueDate = trim((string) post('due_date', ''));

                if ($paymentType === 'Tunai' && $cashPaid < $subtotal) {
                    flash('Uang bayar kurang dari total belanja.', 'warning');
                    $this->redirect('transaksi');
                }

                if ($paymentType === 'Hutang' && $customerId <= 0) {
                    flash('Pilih pelanggan untuk transaksi hutang.', 'warning');
                    $this->redirect('transaksi');
                }

                if ($paymentType === 'QRIS') {
                    $qrisVault = $keuanganModel->findVaultByKeyword('QRIS');
                    $vaultId = (int) ($qrisVault['id'] ?? 0);
                    foreach ($_SESSION['cart'] as $index => $row) {
                        $_SESSION['cart'][$index]['vault_id'] = $vaultId;
                    }
                }

                if ($paymentType === 'Tunai') {
                    foreach ($_SESSION['cart'] as $row) {
                        if ((int) ($row['vault_id'] ?? 0) <= 0) {
                            flash('Pilih tujuan dana pada setiap item transaksi terlebih dahulu.', 'warning');
                            $this->redirect('transaksi');
                        }
                    }
                }

                $transaksiModel->createSale([
                    'invoice_no' => $transaksiModel->nextInvoiceNo(),
                    'customer_id' => $paymentType === 'Hutang' ? $customerId : 0,
                    'payment_type' => $paymentType,
                    'vault_id' => $vaultId,
                    'subtotal' => $subtotal,
                    'total_profit' => $totalProfit,
                    'total_paid' => match ($paymentType) {
                        'Hutang', 'Prive' => 0,
                        'Tunai' => $cashPaid,
                        default => $subtotal,
                    },
                    'notes' => '',
                    'due_date' => $paymentType === 'Hutang' && $dueDate !== '' ? $dueDate : null,
                    'items' => $_SESSION['cart'],
                ]);

                if (in_array($paymentType, ['Tunai', 'QRIS'], true)) {
                    $vaultTotals = [];
                    foreach ($_SESSION['cart'] as $row) {
                        $rowVaultId = (int) ($row['vault_id'] ?? 0);
                        if ($rowVaultId <= 0) {
                            continue;
                        }
                        $vaultTotals[$rowVaultId] = ($vaultTotals[$rowVaultId] ?? 0) + (float) ($row['line_total'] ?? 0);
                    }

                    foreach ($vaultTotals as $rowVaultId => $amount) {
                        $keuanganModel->updateVaultBalance($rowVaultId, $amount);
                    }
                }

                $_SESSION['cart'] = [];
                flash('Transaksi berhasil disimpan.');
                $this->redirect('transaksi');
            }
        }

        $this->view('transaksi/index', [
            'title' => 'Pencatatan Transaksi',
            'items' => $barangModel->searchForTransaction($_GET['q'] ?? ''),
            'esaldoItems' => $esaldoModel->searchForTransaction($_GET['q'] ?? ''),
            'customers' => $pelangganModel->all(),
            'vaults' => $keuanganModel->allVaults(),
            'cart' => $_SESSION['cart'],
            'transactionMode' => $_SESSION['transaction_mode'],
            'nextInvoiceNo' => $transaksiModel->nextInvoiceNo(),
            'latestSales' => $transaksiModel->latestSales(),
            'flash' => flash(),
        ]);
    }

    public function list(): void
    {
        $transaksiModel = $this->model('Transaksi_model');
        $keyword = trim((string) ($_GET['q'] ?? ''));

        if ($_SERVER['REQUEST_METHOD'] === 'POST' && post('action') === 'delete_sale') {
            $deleted = $transaksiModel->deleteSale((int) post('sale_id'));
            flash($deleted ? 'Transaksi berhasil dihapus.' : 'Transaksi tidak ditemukan.', $deleted ? 'success' : 'warning');
            $this->redirect('transaksi/list');
        }

        $this->view('transaksi/list', [
            'title' => 'List Transaksi',
            'sales' => $transaksiModel->salesList($keyword),
            'keyword' => $keyword,
            'flash' => flash(),
        ]);
    }
}
