<?php

declare(strict_types=1);

class Esaldo_controller extends Controller
{
    public function index(): void
    {
        $model = $this->model('Esaldo_model');
        $editEsaldo = null;

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            if (post('action') === 'delete') {
                $deleted = $model->delete((int) post('id'));
                flash($deleted ? 'Saldo berhasil dihapus.' : 'Saldo gagal dihapus.', $deleted ? 'success' : 'warning');
                $this->redirect('esaldo');
            }

            $balance = unformat_number((string) post('balance'));
            $name = trim((string) post('name'));

            $model->save([
                'id' => post('id'),
                'name' => $name !== '' ? $name : 'E-Saldo',
                'balance' => $balance,
            ]);

            flash('Saldo berhasil disimpan.');
            $this->redirect('esaldo');
        }

        if (!empty($_GET['edit'])) {
            $editEsaldo = $model->findBalance((int) $_GET['edit']);
        }

        $this->view('esaldo/index', [
            'title' => 'E-Saldo',
            'esaldos' => $model->allBalances(),
            'editEsaldo' => $editEsaldo,
            'flash' => flash(),
        ]);
    }
}
