<?php
declare(strict_types=1);

class Pelanggan_controller extends Controller
{
    public function index(): void
    {
        $model = $this->model('Pelanggan_model');
        $editCustomer = null;

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $model->save([
                'id' => post('id'),
                'code' => post('code'),
                'name' => post('name'),
                'phone' => post('phone'),
                'address' => post('address'),
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
            'customers' => $model->all(),
            'editCustomer' => $editCustomer,
            'nextCode' => !empty($editCustomer['code']) ? $editCustomer['code'] : $model->nextCode(),
            'flash' => flash(),
        ]);
    }
}
