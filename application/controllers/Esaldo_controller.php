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

            $savedId = $model->save([
                'id' => post('id'),
                'name' => $name !== '' ? $name : 'E-Saldo',
                'balance' => $balance,
            ]);

            if ($isAjax) {
                $isEdit = !empty(post('id'));
                $savedItem = $model->findBalance($savedId);
                
                header('Content-Type: application/json');
                echo json_encode([
                    'success' => true,
                    'message' => 'Saldo berhasil disimpan.',
                    'data' => $savedItem,
                    'is_edit' => $isEdit
                ]);
                exit;
            }

            flash('Saldo berhasil disimpan.');
            $this->redirect('esaldo');
        }

        if ($isAjax && isset($_GET['history'])) {
            $history = $model->getHistory((int) $_GET['history']);
            header('Content-Type: application/json');
            echo json_encode($history);
            exit;
        }

        if (!empty($_GET['edit'])) {
            $editEsaldo = $model->findBalance((int) $_GET['edit']);
        }

        $all_esaldos = $model->allBalances();
        $limit = 5;
        $totalItems = count($all_esaldos);
        $totalPages = (int) ceil($totalItems / $limit);
        $currentPage = max(1, min((int) ($_GET['p'] ?? 1), max(1, $totalPages)));
        $offset = ($currentPage - 1) * $limit;
        $esaldos = array_slice($all_esaldos, $offset, $limit);

        $this->view('esaldo/index', [
            'title' => 'E-Saldo',
            'esaldos' => $esaldos,
            'totalItems' => $totalItems,
            'totalPages' => $totalPages,
            'currentPage' => $currentPage,
            'editEsaldo' => $editEsaldo,
            'flash' => flash(),
        ]);
    }
}
