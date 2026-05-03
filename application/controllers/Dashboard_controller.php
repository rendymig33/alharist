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
            if ($dateFrom !== '') $query['date_from'] = $dateFrom;
            if ($dateTo !== '') $query['date_to'] = $dateTo;
            header('Location: index.php?' . http_build_query($query));
            exit;
        }

        $selectedDateHistory = $viewDate !== '' ? $transactionModel->salesHistoryByDate($viewDate) : [];

        $all_latest_sales = $transactionModel->salesSummaryByDate($dateFrom ?: null, $dateTo ?: null, 100);
        $limit = 5;
        $totalItems = count($all_latest_sales);
        $totalPages = (int) ceil($totalItems / $limit);
        $currentPage = max(1, min((int) ($_GET['p'] ?? 1), max(1, $totalPages)));
        $offset = ($currentPage - 1) * $limit;
        $latestSales = array_slice($all_latest_sales, $offset, $limit);

        $this->view('dashboard/index', [
            'title' => 'Dashboard',
            'summary' => $dashboardModel->summary($dateFrom ?: null, $dateTo ?: null, null),
            'latestSales' => $latestSales,
            'totalItems' => $totalItems,
            'totalPages' => $totalPages,
            'currentPage' => $currentPage,
            'filterDateFrom' => $dateFrom,
            'filterDateTo' => $dateTo,
            'selectedViewDate' => $viewDate,
            'selectedDateHistory' => $selectedDateHistory,
            'flash' => flash(),
        ]);
    }
}
