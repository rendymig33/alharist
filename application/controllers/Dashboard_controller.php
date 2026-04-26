<?php
declare(strict_types=1);

class Dashboard_controller extends Controller
{
    public function index(): void
    {
        $dashboardModel = $this->model('Dashboard_model');
        $transactionModel = $this->model('Transaksi_model');
        $dateFrom = isset($_GET['date_from']) ? trim((string) $_GET['date_from']) : '';
        $dateTo = isset($_GET['date_to']) ? trim((string) $_GET['date_to']) : '';
        $viewDate = isset($_GET['view_date']) ? trim((string) $_GET['view_date']) : '';

        if ($_SERVER['REQUEST_METHOD'] === 'POST' && post('action') === 'delete_sale') {
            $deleted = $transactionModel->deleteSale((int) post('sale_id'));
            flash($deleted ? 'Transaksi berhasil dihapus.' : 'Transaksi tidak ditemukan.', $deleted ? 'success' : 'warning');

            $query = ['route' => 'dashboard'];
            if ($dateFrom !== '') {
                $query['date_from'] = $dateFrom;
            }
            if ($dateTo !== '') {
                $query['date_to'] = $dateTo;
            }

            header('Location: index.php?' . http_build_query($query));
            exit;
        }

        $selectedDateHistory = $viewDate !== '' ? $transactionModel->salesHistoryByDate($viewDate) : [];

        $this->view('dashboard/index', [
            'title' => 'Dashboard',
            'summary' => $dashboardModel->summaryToday(),
            'latestSales' => $transactionModel->salesSummaryByDate($dateFrom ?: null, $dateTo ?: null, 20),
            'filterDateFrom' => $dateFrom,
            'filterDateTo' => $dateTo,
            'selectedViewDate' => $viewDate,
            'selectedDateHistory' => $selectedDateHistory,
            'flash' => flash(),
        ]);
    }
}
