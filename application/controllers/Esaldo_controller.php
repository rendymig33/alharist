<?php

declare(strict_types=1);

class Esaldo_controller extends Controller
{
    public function index(): void
    {
        $model = $this->model('Esaldo_model');
        $editEsaldo = null;
        $isAjax = (string) ($_SERVER['HTTP_X_REQUESTED_WITH'] ?? '') === 'XMLHttpRequest';

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            if (post('action') === 'delete') {
                $deleted = $model->delete((int) post('id'));

                if ($isAjax) {
                    header('Content-Type: application/json');
                    echo json_encode([
                        'success' => $deleted,
                        'message' => $deleted ? 'Saldo berhasil dihapus.' : 'Saldo gagal dihapus.',
                    ]);
                    exit;
                }

                flash($deleted ? 'Saldo berhasil dihapus.' : 'Saldo gagal dihapus.', $deleted ? 'success' : 'warning');
                $this->redirect('esaldo');
            }

            $balance = unformat_number((string) post('balance'));
            $name = trim((string) post('name'));

            $saved = $model->save([
                'id' => post('id'),
                'name' => $name !== '' ? $name : 'E-Saldo',
                'balance' => $balance,
            ]);

            if ($isAjax) {
                header('Content-Type: application/json');
                echo json_encode([
                    'success' => true,
                    'message' => 'Saldo berhasil disimpan.',
                ]);
                exit;
            }

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
