<?php
declare(strict_types=1);

class Importexport_controller extends Controller
{
    public function index(): void
    {
        $barangModel = $this->model('Barang_model');
        $pelangganModel = $this->model('Pelanggan_model');

        if ($_SERVER['REQUEST_METHOD'] === 'POST' && !empty($_FILES['file']['tmp_name'])) {
            $target = $this->basePath . '/storage/uploads/' . basename((string) $_FILES['file']['name']);
            move_uploaded_file($_FILES['file']['tmp_name'], $target);

            $type = post('type');
            $count = $type === 'barang' ? $barangModel->importCsv($target) : $pelangganModel->importCsv($target);
            flash($count . ' data berhasil diimport dari Excel/CSV.');
            $this->redirect('importexport');
        }

        if (!empty($_GET['download'])) {
            $this->download((string) $_GET['download']);
            return;
        }

        $this->view('importexport/index', [
            'title' => 'Import Export Excel',
            'items' => $barangModel->all(),
            'customers' => $pelangganModel->all(),
            'flash' => flash(),
        ]);
    }

    private function download(string $type): void
    {
        if ($type === 'barang') {
            $rows = $this->model('Barang_model')->all();
            $filename = 'master_barang.xls';
            $headers = ['Kode', 'Nama', 'Kategori', 'Satuan Besar', 'Satuan Kecil', 'Isi Satuan Besar', 'Harga Beli', 'Harga Jual', 'Presentase Untung', 'Harga Satuan', 'Harga Beli >3', 'Stok', 'EXP Date'];
        } else {
            $rows = $this->model('Pelanggan_model')->all();
            $filename = 'master_pelanggan.xls';
            $headers = ['Kode', 'Nama', 'Telepon', 'Alamat'];
        }

        header('Content-Type: application/vnd.ms-excel');
        header('Content-Disposition: attachment; filename=' . $filename);

        echo '<table border="1"><tr>';
        foreach ($headers as $header) {
            echo '<th>' . htmlspecialchars($header) . '</th>';
        }
        echo '</tr>';

        foreach ($rows as $row) {
            echo '<tr>';
            if ($type === 'barang') {
                echo '<td>' . htmlspecialchars((string) $row['code']) . '</td>';
                echo '<td>' . htmlspecialchars((string) $row['name']) . '</td>';
                echo '<td>' . htmlspecialchars((string) $row['category']) . '</td>';
                echo '<td>' . htmlspecialchars((string) $row['unit_large']) . '</td>';
                echo '<td>' . htmlspecialchars((string) $row['unit_small']) . '</td>';
                echo '<td>' . htmlspecialchars((string) ($row['small_unit_qty'] ?? 1)) . '</td>';
                echo '<td>' . htmlspecialchars((string) $row['purchase_price']) . '</td>';
                echo '<td>' . htmlspecialchars((string) $row['selling_price']) . '</td>';
                echo '<td>' . htmlspecialchars((string) $row['profit_percent']) . '</td>';
                echo '<td>' . htmlspecialchars((string) $row['unit_price']) . '</td>';
                echo '<td>' . htmlspecialchars((string) $row['bulk_purchase_price']) . '</td>';
                echo '<td>' . htmlspecialchars((string) $row['stock']) . '</td>';
                echo '<td>' . htmlspecialchars((string) $row['exp_date']) . '</td>';
            } else {
                echo '<td>' . htmlspecialchars((string) $row['code']) . '</td>';
                echo '<td>' . htmlspecialchars((string) $row['name']) . '</td>';
                echo '<td>' . htmlspecialchars((string) $row['phone']) . '</td>';
                echo '<td>' . htmlspecialchars((string) $row['address']) . '</td>';
            }
            echo '</tr>';
        }
        echo '</table>';
    }
}
