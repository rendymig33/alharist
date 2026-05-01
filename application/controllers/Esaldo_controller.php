<?php

declare(strict_types=1);

class Esaldo_controller extends Controller
{
    public function index(): void
    {
        $model = $this->model('Esaldo_model');
        $keyword = trim((string) ($_GET['q'] ?? ''));
        $editEsaldo = null;

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            if (post('action') === 'delete') {
                $deleted = $model->delete((int) post('id'));
                flash($deleted ? 'Master E-Saldo berhasil dihapus.' : 'Master E-Saldo gagal dihapus.', $deleted ? 'success' : 'warning');
                $this->redirect('esaldo');
            }

            $model->save([
                'id' => post('id'),
                'code' => post('code') ?: $model->nextCode(),
                'name' => post('name'),
                'description' => '',
                'purchase_price' => unformat_number((string) post('purchase_price')),
                'selling_price' => unformat_number((string) post('selling_price')),
            ]);

            flash('Master E-Saldo berhasil disimpan.');
            $this->redirect('esaldo');
        }

        if (!empty($_GET['edit'])) {
            $editEsaldo = $model->find((int) $_GET['edit']);
        }

        $dashboardModel = $this->model('Dashboard_model');
        $modalBalance = $dashboardModel->modalVaultBalance();

        $this->view('esaldo/index', [
            'title' => 'E-Saldo',
            'esaldos' => $model->all($keyword),
            'editEsaldo' => $editEsaldo,
            'nextCode' => !empty($editEsaldo['code']) ? $editEsaldo['code'] : $model->nextCode(),
            'keyword' => $keyword,
            'modalBalance' => $modalBalance,
            'flash' => flash(),
        ]);
    }
}
