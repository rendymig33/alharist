<?php

declare(strict_types=1);

class Pelanggan_controller extends Controller
{
    public function index(): void
    {
        $model = $this->model('Pelanggan_model');
        $editCustomer = null;
        $keyword = trim((string) ($_GET['q'] ?? ''));

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            if (post('action') === 'delete') {
                $deleted = $model->delete((int) post('id'));
                flash($deleted ? 'Pelanggan berhasil dihapus.' : 'Pelanggan gagal dihapus.', $deleted ? 'success' : 'warning');
                $this->redirect('pelanggan');
            }

            $model->save([
                'id' => post('id'),
                'code' => post('code'),
                'name' => post('name'),
            ]);

            flash('Data pelanggan berhasil disimpan.');
            $this->redirect('pelanggan');
        }

        if (!empty($_GET['edit'])) {
            $customers = $model->all();
            foreach ($customers as $customer) {
                if ((int) $customer['id'] === (int) $_GET['edit']) {
                    $editCustomer = $customer;
                    break;
                }
            }
        }

        $this->view('pelanggan/index', [
            'title' => 'Master Data Pelanggan',
            'customers' => $model->search($keyword),
            'editCustomer' => $editCustomer,
            'nextCode' => !empty($editCustomer['code']) ? $editCustomer['code'] : $model->nextCode(),
            'keyword' => $keyword,
            'flash' => flash(),
        ]);
    }
}
