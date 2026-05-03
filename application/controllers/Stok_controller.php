<?php
declare(strict_types=1);

class Stok_controller extends Controller
{
    public function receive(): void
    {
        $stokModel = $this->model('Stok_model');
        $keyword = trim((string) ($_GET['q'] ?? ''));
        $selectedItemId = isset($_GET['item']) ? (int) $_GET['item'] : 0;

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $selectedItemId = (int) post('item_id');
            $action = (string) post('action', 'save');

            if ($action === 'delete_receive') {
                $saved = $stokModel->deleteReceive((int) post('receive_id'));
                flash($saved ? 'History receive berhasil dihapus.' : 'History receive tidak bisa dihapus.', $saved ? 'success' : 'warning');
            } else {
                $saved = $stokModel->receiveItem([
                    'item_id' => $selectedItemId,
                    'qty_large' => (int) post('qty_large', 0),
                    'qty_small' => (int) post('qty_small', 0),
                    'purchase_price' => unformat_number((string) post('purchase_price')),
                    'notes' => post('notes'),
                ]);

                flash($saved ? 'Barang masuk berhasil disimpan.' : 'Data receive item tidak valid.', $saved ? 'success' : 'warning');
            }

            $query = ['route' => 'stok/receive'];
            if ($keyword !== '') {
                $query['q'] = $keyword;
            }
            if ($selectedItemId > 0) {
                $query['item'] = $selectedItemId;
            }
            header('Location: index.php?' . http_build_query($query));
            exit;
        }

        $all_items = $stokModel->itemOptions($keyword);
        $limit = 8;
        $totalItems = count($all_items);
        $totalPages = (int) ceil($totalItems / $limit);
        $currentPage = max(1, min((int) ($_GET['p'] ?? 1), max(1, $totalPages)));
        $offset = ($currentPage - 1) * $limit;
        $items = array_slice($all_items, $offset, $limit);

        $selectedItem = null;
        if ($selectedItemId > 0) {
            foreach ($all_items as $item) {
                if ((int) ($item['id'] ?? 0) === $selectedItemId) {
                    $selectedItem = $item;
                    break;
                }
            }
        }
        
        if ($selectedItem === null && !empty($items)) {
            $selectedItem = $items[0];
        }

        $selectedHistory = !empty($selectedItem['id'])
            ? $stokModel->receiveHistoryByItem((int) $selectedItem['id'])
            : [];

        $this->view('stok/receive', [
            'title' => 'Receive Item',
            'items' => $items,
            'totalItems' => $totalItems,
            'totalPages' => $totalPages,
            'currentPage' => $currentPage,
            'selectedItem' => $selectedItem,
            'selectedHistory' => $selectedHistory,
            'keyword' => $keyword,
            'flash' => flash(),
        ]);
    }

    public function opname(): void
    {
        $stokModel = $this->model('Stok_model');
        $keyword = trim((string) ($_GET['q'] ?? ''));
        $selectedItemId = isset($_GET['item']) ? (int) $_GET['item'] : 0;

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $selectedItemId = (int) post('item_id');
            $action = (string) post('action', 'save');

            if ($action === 'delete_opname') {
                $saved = $stokModel->deleteStockOpname((int) post('opname_id'));
                flash($saved ? 'History stok opname berhasil dihapus.' : 'Hanya history koreksi terbaru yang bisa dihapus.', $saved ? 'success' : 'warning');
            } else {
                $saved = $stokModel->stockOpname([
                    'item_id' => $selectedItemId,
                    'qty_large' => (int) post('qty_large', 0),
                    'qty_small' => (int) post('qty_small', 0),
                    'notes' => post('notes'),
                ]);

                flash($saved ? 'Stok opname berhasil disimpan.' : 'Data stok opname tidak valid.', $saved ? 'success' : 'warning');
            }

            $query = ['route' => 'stok/opname'];
            if ($keyword !== '') {
                $query['q'] = $keyword;
            }
            if ($selectedItemId > 0) {
                $query['item'] = $selectedItemId;
            }
            header('Location: index.php?' . http_build_query($query));
            exit;
        }

        $all_items = $stokModel->itemOptions($keyword);
        $limit = 8;
        $totalItems = count($all_items);
        $totalPages = (int) ceil($totalItems / $limit);
        $currentPage = max(1, min((int) ($_GET['p'] ?? 1), max(1, $totalPages)));
        $offset = ($currentPage - 1) * $limit;
        $items = array_slice($all_items, $offset, $limit);

        $selectedItem = null;
        if ($selectedItemId > 0) {
            foreach ($all_items as $item) {
                if ((int) ($item['id'] ?? 0) === $selectedItemId) {
                    $selectedItem = $item;
                    break;
                }
            }
        }

        if ($selectedItem === null && !empty($items)) {
            $selectedItem = $items[0];
        }

        $selectedHistory = !empty($selectedItem['id'])
            ? $stokModel->opnameHistoryByItem((int) $selectedItem['id'])
            : [];

        $this->view('stok/opname', [
            'title' => 'Stok Opname',
            'items' => $items,
            'totalItems' => $totalItems,
            'totalPages' => $totalPages,
            'currentPage' => $currentPage,
            'selectedItem' => $selectedItem,
            'selectedHistory' => $selectedHistory,
            'keyword' => $keyword,
            'flash' => flash(),
        ]);
    }
}
