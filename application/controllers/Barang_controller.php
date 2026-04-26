<?php
declare(strict_types=1);

class Barang_controller extends Controller
{
    public function index(): void
    {
        $model = $this->model('Barang_model');
        $editItem = null;
        $viewItem = null;
        $keyword = trim((string) ($_GET['q'] ?? ''));
        $recommendationInput = [
            'purchase_receipt_total' => (float) post('purchase_receipt_total', 0),
            'selling_price' => (float) post('selling_price', 0),
            'small_unit_qty' => (int) post('small_unit_qty', 1),
        ];

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            if (post('action') === 'delete') {
                $deleted = $model->delete((int) post('id'));
                flash($deleted ? 'Data barang berhasil dihapus.' : 'Data barang tidak ditemukan.', $deleted ? 'success' : 'warning');
                $this->redirect('barang');
            }

            $smallUnitQty = max(1, (int) post('small_unit_qty', 1));
            $stockLarge = max(0, (int) post('stock_large', 0));
            $stockSmall = max(0, (int) post('stock_small', 0));
            $stock = stock_to_smallest_units($stockLarge, $stockSmall, $smallUnitQty);
            $stockMode = post('stock_mode', 'add');
            $purchaseReceiptTotal = unformat_number((string) post('purchase_receipt_total'));
            $purchaseLargeQty = max(0, (int) post('purchase_large_qty', 0));
            $existingRaw = post('id') !== '' ? $model->findRaw((int) post('id')) : false;
            $purchaseInput = $purchaseLargeQty > 0 ? ($purchaseReceiptTotal / $purchaseLargeQty) : 0;
            $purchaseTotal = $purchaseReceiptTotal;
            $purchaseBasisQty = $purchaseLargeQty;
            $unitLarge = trim((string) post('unit_large'));
            $unitSmall = trim((string) post('unit_small'));

            if ($unitLarge !== '' && $unitSmall !== '' && strcasecmp($unitLarge, $unitSmall) === 0) {
                flash('Satuan Besar dan Satuan Kecil tidak boleh sama.', 'warning');
                header('Location: index.php?route=barang' . (!empty(post('id')) ? '&edit=' . (int) post('id') : ''));
                exit;
            }

            if (post('action', 'save') === 'save') {
                $model->save([
                    'id' => post('id'),
                    'code' => post('code'),
                    'barcode' => trim((string) post('barcode')),
                    'name' => post('name'),
                    'category' => post('category'),
                    'description' => post('description'),
                    'unit_large' => $unitLarge,
                    'unit_small' => $unitSmall,
                    'small_unit_qty' => $smallUnitQty,
                    'purchase_price' => $purchaseInput,
                    'purchase_total' => $purchaseTotal,
                    'purchase_basis_qty' => $purchaseBasisQty,
                    'selling_price' => unformat_number((string) post('selling_price')),
                    'profit_percent' => 0,
                    'unit_price' => unformat_number((string) post('unit_price')),
                    'half_price' => unformat_number((string) post('half_price')),
                    'allow_small_sale' => post('allow_small_sale', 0),
                    'allow_half_sale' => post('allow_half_sale', 0),
                    'promo_qty_1' => (int) post('promo_qty_1', 0),
                    'promo_price_1' => unformat_number((string) post('promo_price_1')),
                    'promo_qty_2' => (int) post('promo_qty_2', 0),
                    'promo_price_2' => unformat_number((string) post('promo_price_2')),
                    'promo_qty_3' => (int) post('promo_qty_3', 0),
                    'promo_price_3' => unformat_number((string) post('promo_price_3')),
                    'stock' => $stock,
                    'stock_mode' => $stockMode,
                    'exp_date' => null,
                ]);

                flash('Data barang berhasil disimpan.');
                $this->redirect('barang');
            }
        }

        if (!empty($_GET['edit'])) {
            $editItem = $model->find((int) $_GET['edit']);
            $recommendationInput = [
                'purchase_receipt_total' => (float) ($editItem['purchase_total'] ?? 0),
                'selling_price' => (float) ($editItem['selling_price'] ?? 0),
                'small_unit_qty' => (int) ($editItem['small_unit_qty'] ?? 1),
            ];
        }

        if (!empty($_GET['view'])) {
            $viewItem = $model->find((int) $_GET['view']);
        }

        $nextCode = !empty($editItem['code']) ? (string) $editItem['code'] : $model->nextCode();
        $items = $model->search($keyword);

        $this->view('barang/index', [
            'title' => 'Master Data Barang',
            'items' => $items,
            'editItem' => $editItem,
            'viewItem' => $viewItem,
            'nextCode' => $nextCode,
            'recommendation' => $model->recommendation($recommendationInput),
            'keyword' => $keyword,
            'flash' => flash(),
        ]);
    }
}
