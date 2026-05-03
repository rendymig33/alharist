<?php
$detailQuery = array_filter([
    'route' => 'dashboard',
    'date_from' => $filterDateFrom ?? '',
    'date_to' => $filterDateTo ?? '',
]);
$isFiltered = !empty($filterDateFrom) || !empty($filterDateTo);
$summary = array_merge([
    'sales' => 0,
    'profit' => 0,
    'vault' => 0,
    'debts' => 0,
], $summary ?? []);
?>
<?php
$currentPage = $currentPage ?? 1;
$totalPages = $totalPages ?? 1;
?>
<style>
    /* Pagination */
    .pagination-wrap {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-top: 24px;
        padding-top: 20px;
        border-top: 1px solid var(--line);
    }

    .pagination-info {
        font-size: 13px;
        font-weight: 700;
        color: #667085;
        background: #f9fafb;
        padding: 6px 12px;
        border-radius: 999px;
        border: 1px solid var(--line);
    }

    .btn-pagination {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        padding: 8px 16px;
        border-radius: 10px;
        font-weight: 600;
        transition: all 0.2s;
        text-decoration: none;
        border: 1px solid var(--line);
        background: #fff;
        color: #344054;
    }

    .btn-pagination:hover:not(:disabled) {
        background: #f9fafb;
        transform: translateY(-1px);
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.05);
    }

    .dashboard-transactions {
        margin-top: 18px;
    }

    .dashboard-transactions .section-head {
        display: flex;
        justify-content: space-between;
        align-items: flex-start;
        gap: 16px;
        flex-wrap: wrap;
        margin-bottom: 18px;
    }

    .dashboard-transactions .section-copy h3 {
        margin: 0 0 6px;
    }

    .dashboard-transactions .section-copy p {
        margin: 0;
        font-size: 13px;
        color: #667085;
    }

    .dashboard-transactions .section-actions {
        display: flex;
        gap: 10px;
        flex-wrap: wrap;
    }

    .dashboard-transactions .section-actions .btn {
        width: auto;
        padding: 10px 14px;
    }

    .dashboard-table-wrap {
        border: 1px solid var(--line);
        border-radius: 16px;
        overflow: hidden;
        background: #fff;
    }

    .dashboard-table-wrap table {
        margin: 0;
    }

    .dashboard-table-wrap thead th {
        font-size: 12px;
        text-transform: uppercase;
        letter-spacing: .04em;
    }

    .dashboard-table-wrap tbody tr:hover {
        background: #fcfcfd;
    }

    .amount-cell strong {
        display: block;
        color: #101828;
    }

    .amount-cell .small {
        margin-top: 4px;
    }

    .payment-badge {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        min-width: 88px;
        padding: 7px 12px;
        border-radius: 999px;
        font-size: 12px;
        font-weight: 700;
    }

    .payment-badge.tunai {
        background: #ecfdf3;
        color: #027a48;
    }

    .payment-badge.qris {
        background: #eff6ff;
        color: #175cd3;
    }

    .payment-badge.hutang {
        background: #fff4ed;
        color: #c4320a;
    }

    .date-cell {
        white-space: nowrap;
        color: #344054;
    }

    .table-actions {
        display: flex;
        gap: 8px;
        flex-wrap: wrap;
    }

    .table-actions .btn,
    .table-actions button {
        width: auto;
        min-width: 84px;
        padding: 8px 12px;
    }

    .empty-state {
        padding: 28px 18px;
        text-align: center;
        color: #667085;
    }

    .dashboard-filter {
        display: grid;
        grid-template-columns: repeat(3, minmax(0, 1fr));
        gap: 12px;
        padding: 14px;
        border: 1px solid var(--line);
        border-radius: 16px;
        background: linear-gradient(180deg, #fcfcfd, #f8fafc);
        margin-bottom: 16px;
    }

    .dashboard-filter .filter-field {
        display: grid;
        gap: 6px;
    }

    .dashboard-filter .filter-actions {
        display: flex;
        align-items: end;
        gap: 10px;
        flex-wrap: wrap;
    }

    .dashboard-filter .filter-actions .btn,
    .dashboard-filter .filter-actions button {
        width: auto;
        min-width: 108px;
        border-radius: 12px;
    }

    .dashboard-filter .filter-actions .btn {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        padding: 11px 12px;
    }

    .dashboard-filter .filter-actions .btn-secondary {
        background: var(--red);
        color: var(--white);
    }

    .history-stack {
        display: grid;
        gap: 14px;
    }

    .history-card {
        border: 1px solid var(--line);
        border-radius: 16px;
        overflow: hidden;
        background: #fff;
    }

    .history-head {
        display: flex;
        justify-content: space-between;
        align-items: flex-start;
        gap: 12px;
        padding: 14px 16px;
        background: linear-gradient(180deg, #fcfcfd, #f8fafc);
        border-bottom: 1px solid var(--line);
    }

    .history-head h4 {
        margin: 0 0 4px;
    }

    .history-summary {
        display: flex;
        gap: 10px;
        flex-wrap: wrap;
    }

    .history-summary .badge {
        background: #fff;
        border: 1px solid var(--line);
    }

    .history-table-wrap {
        overflow-x: auto;
    }

    .dashboard-history-modal {
        width: min(980px, 100%);
    }

    .mobile-stack-table {
        width: 100%;
    }

    @media (max-width: 920px) {
        .history-head {
            flex-direction: column;
        }

        .dashboard-filter {
            grid-template-columns: 1fr;
        }
    }

    @media (max-width: 640px) {
        .grid.cards {
            grid-template-columns: 1fr;
        }

        .metric {
            font-size: 22px;
        }

        .dashboard-transactions .section-head {
            flex-direction: column;
            align-items: stretch;
        }

        .dashboard-transactions .section-actions {
            width: 100%;
        }

        .dashboard-transactions .section-actions .btn {
            width: 100%;
        }

        .dashboard-table-wrap {
            overflow-x: auto;
        }

        .dashboard-filter {
            padding: 12px;
        }

        .dashboard-filter .filter-actions {
            display: grid;
            grid-template-columns: 1fr 1fr;
        }

        .dashboard-filter .filter-actions .btn,
        .dashboard-filter .filter-actions button {
            width: 100%;
            min-width: 0;
        }

        .history-summary {
            display: grid;
            grid-template-columns: 1fr;
        }

        .history-summary .badge {
            width: 100%;
            justify-content: flex-start;
        }

        .dashboard-transactions .card {
            padding: 14px;
        }

        .dashboard-table-wrap {
            border-radius: 14px;
        }

        .dashboard-history-modal {
            width: min(100%, 100%);
            padding: 14px;
        }

        .history-card {
            border-radius: 14px;
        }

        .history-head {
            padding: 12px 14px;
        }

        .history-head h4 {
            font-size: 15px;
        }

        .history-table-wrap table th,
        .history-table-wrap table td {
            font-size: 13px;
            padding: 10px 8px;
        }

        .mobile-stack-table {
            min-width: 100%;
        }

        .mobile-stack-table thead {
            display: none;
        }

        .mobile-stack-table tbody,
        .mobile-stack-table tr,
        .mobile-stack-table td {
            display: block;
            width: 100%;
        }

        .mobile-stack-table tbody tr {
            padding: 10px 0;
            border-bottom: 1px solid var(--line);
        }

        .mobile-stack-table tbody tr:last-child {
            border-bottom: none;
        }

        .mobile-stack-table td {
            border-bottom: none;
            padding: 8px 0;
        }

        .mobile-stack-table td::before {
            content: attr(data-label);
            display: block;
            margin-bottom: 4px;
            font-size: 11px;
            font-weight: 800;
            letter-spacing: .05em;
            text-transform: uppercase;
            color: #98a2b3;
        }
    }
</style>
<div style="display: grid; grid-template-columns: repeat(2, minmax(0, 1fr)); gap: 14px; margin-bottom: 20px;">
    <?php foreach ([1, 2] as $sIdx): ?>
        <?php
        $sData = array_values(array_filter($shiftSummary ?? [], fn($s) => (int)$s['shift'] === $sIdx))[0] ?? ['total_sales' => 0, 'total_profit' => 0];
        ?>
        <div class="card" style="background: #ecfdf5; color: #065f46; border: 1px solid #bbf7d0;">
            <div class="small" style="color: #047857; font-weight: 700;">PROFIT SHIFT <?= $sIdx ?></div>
            <div class="metric" style="color: #065f46; margin: 8px 0; font-size: 28px;"><?= rupiah($sData['total_profit']) ?></div>
        </div>
    <?php endforeach; ?>
</div>

<div class="grid cards">
    <div class="card">
        <div class="small" style="font-weight: 700; color: #64748b;"><?= $isFiltered ? 'TOTAL PENJUALAN' : 'OMZET HARI INI' ?></div>
        <div class="metric" style="color: #0f172a;"><?= rupiah($summary['sales']) ?></div>
    </div>
    <div class="card">
        <div class="small" style="font-weight: 700; color: #64748b;"><?= $isFiltered ? 'TOTAL KEUNTUNGAN' : 'PROFIT HARI INI' ?></div>
        <div class="metric" style="color: #064e3b;"><?= rupiah($summary['profit']) ?></div>
    </div>
    <div class="card">
        <div class="small" style="font-weight: 700; color: #64748b;">MODAL CASIER</div>
        <div class="metric" style="color: #1e293b;"><?= rupiah($summary['vault']) ?></div>
    </div>
    <div class="card">
        <div class="small" style="font-weight: 700; color: #64748b;">PIUTANG PELANGGAN</div>
        <div class="metric" style="color: #7f1d1d;"><?= rupiah($summary['debts']) ?></div>
    </div>
</div>

<div class="dashboard-transactions">
    <div class="card">
        <div class="section-head">
            <div class="section-copy">
                <h3>Transaksi Terbaru</h3>
                <p>Ringkasan transaksi penjualan harian dengan detail item dan aksi cepat langsung dari dashboard.</p>
            </div>
            <div class="section-actions">
                <a class="btn btn-secondary" href="index.php?route=transaksi">Lihat Semua Transaksi</a>
            </div>
        </div>

        <form method="get" class="dashboard-filter">
            <input type="hidden" name="route" value="dashboard">
            <div class="filter-field">
                <div class="small">Tanggal Dari</div>
                <input type="date" name="date_from" value="<?= date('Y-m-d') ?>">
            </div>
            <div class="filter-field">
                <div class="small">Tanggal Sampai</div>
                <input type="date" name="date_to" value="<?= date('Y-m-d') ?>">
            </div>
            <div class="filter-actions">
                <button type="submit">Filter</button>
                <a class="btn btn-secondary" href="index.php?route=dashboard">Reset</a>
            </div>
        </form>

        <div class="bca-ledger-wrap">
            <table class="bca-ledger">
                <thead>
                    <tr>
                        <th>Tanggal</th>
                        <th>Ringkasan</th>
                        <th style="text-align:right;">Penjualan</th>
                        <th style="text-align:right;">Profit</th>
                        <th style="text-align:right;">Dibayar</th>
                        <th style="text-align:right;">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (!empty($latestSales)): ?>
                        <?php foreach ($latestSales as $sale): ?>
                            <tr>
                                <td class="date" data-label="Tanggal"><?= htmlspecialchars((string) $sale['transaction_date']) ?></td>
                                <td class="desc" data-label="Ringkasan">
                                    <span class="desc-main"><?= (int) $sale['transaction_count'] ?> TRANSAKSI</span>
                                    <span class="desc-sub">Akumulasi transaksi harian</span>
                                </td>
                                <td class="amount cr" data-label="Penjualan">
                                    <?= number_format((float) $sale['subtotal_sum'], 0, ',', '.') ?>
                                    <span class="type-label type-cr">CR</span>
                                </td>
                                <td class="balance" style="color: #16794d;" data-label="Profit">
                                    <?= number_format((float) $sale['total_profit_sum'], 0, ',', '.') ?>
                                </td>
                                <td class="balance" data-label="Dibayar">
                                    <?= number_format((float) $sale['total_paid_sum'], 0, ',', '.') ?>
                                </td>
                                <td style="text-align:right;" data-label="Aksi">
                                    <div class="table-actions" style="justify-content: flex-end;">
                                        <a class="btn btn-info" style="padding: 4px 10px; font-size: 11px;" href="index.php?<?= http_build_query($detailQuery + ['view_date' => (string) $sale['transaction_date']]) ?>">Detail</a>
                                    </div>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="6" class="empty-state">Belum ada transaksi pada rentang tanggal yang dipilih.</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>

        <?php if ($totalPages > 1): ?>
            <div class="pagination-wrap">
                <div class="pagination-info">
                    Halaman <?= $currentPage ?> dari <?= $totalPages ?>
                </div>
                <div class="pagination-btns" style="display:flex; gap:8px;">
                    <?php
                    $prevParams = ['route' => 'dashboard', 'p' => $currentPage - 1];
                    if (!empty($filterDateFrom)) $prevParams['date_from'] = $filterDateFrom;
                    if (!empty($filterDateTo)) $prevParams['date_to'] = $filterDateTo;
                    $nextParams = ['route' => 'dashboard', 'p' => $currentPage + 1];
                    if (!empty($filterDateFrom)) $nextParams['date_from'] = $filterDateFrom;
                    if (!empty($filterDateTo)) $nextParams['date_to'] = $filterDateTo;
                    ?>

                    <?php if ($currentPage > 1): ?>
                        <a href="index.php?<?= http_build_query($prevParams) ?>" class="btn-pagination">
                            Prev
                        </a>
                    <?php endif; ?>

                    <?php if ($currentPage < $totalPages): ?>
                        <a href="index.php?<?= http_build_query($nextParams) ?>" class="btn-pagination">
                            Next
                        </a>
                    <?php endif; ?>
                </div>
            </div>
        <?php endif; ?>
    </div>
</div>

<div class="modal-backdrop <?= !empty($selectedDateHistory) ? 'active' : '' ?>" id="sale-detail-modal">
    <div class="modal dashboard-history-modal">
        <div class="modal-head">
            <div>
                <h3 style="margin:0;">History Transaksi Harian</h3>
                <?php if (!empty($selectedViewDate)): ?>
                    <div class="small" style="margin-top:4px;">Tanggal <?= htmlspecialchars((string) $selectedViewDate) ?></div>
                <?php endif; ?>
            </div>
            <a class="modal-close" href="index.php?route=dashboard<?= !empty($filterDateFrom) || !empty($filterDateTo) ? '&' . http_build_query(array_filter(['date_from' => $filterDateFrom ?? '', 'date_to' => $filterDateTo ?? ''])) : '' ?>">Tutup</a>
        </div>

        <?php if (!empty($selectedDateHistory)): ?>
            <div class="history-stack">
                <?php foreach ($selectedDateHistory as $history): ?>
                    <?php $paymentClass = strtolower((string) $history['payment_type']); ?>
                    <div class="history-card">
                        <div class="history-head">
                            <div>
                                <h4><?= htmlspecialchars((string) $history['invoice_no']) ?></h4>
                                <div class="small">Transaksi #<?= (int) $history['id'] ?></div>
                            </div>
                            <div class="history-summary">
                                <span class="payment-badge <?= htmlspecialchars($paymentClass) ?>"><?= htmlspecialchars((string) $history['payment_type']) ?></span>
                                <span class="badge">Total <?= rupiah((float) $history['subtotal']) ?></span>
                                <span class="badge">Profit <?= rupiah((float) ($history['total_profit_live'] ?? $history['total_profit'] ?? 0)) ?></span>
                                <span class="badge">Dibayar <?= rupiah((float) $history['total_paid']) ?></span>
                            </div>
                        </div>
                        <div class="bca-ledger-wrap">
                            <table class="bca-ledger">
                                <thead>
                                    <tr>
                                        <th>Item / Kode</th>
                                        <th style="text-align:right;">Qty</th>
                                        <th style="text-align:right;">Modal</th>
                                        <th style="text-align:right;">Jual</th>
                                        <th style="text-align:right;">Profit</th>
                                        <th style="text-align:right;">Aksi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php if (!empty($history['items'])): ?>
                                        <?php foreach ($history['items'] as $item): ?>
                                            <tr>
                                                <td class="desc" data-label="Item / Kode">
                                                    <span class="desc-main"><?= htmlspecialchars((string) ($item['item_name'] ?? 'Item dihapus')) ?></span>
                                                    <span class="desc-sub"><?= htmlspecialchars((string) ($item['item_code'] ?? '-')) ?></span>
                                                </td>
                                                <td class="amount" data-label="Qty">
                                                    <?= format_qty((float) $item['qty']) ?>
                                                </td>
                                                <td class="amount db" data-label="Modal">
                                                    <?= number_format((float) ($item['line_cost_live'] ?? $item['purchase_price_live'] ?? $item['purchase_price'] ?? 0), 0, ',', '.') ?>
                                                    <span class="type-label type-db">DB</span>
                                                </td>
                                                <td class="amount cr" data-label="Jual">
                                                    <?= number_format((float) $item['selling_price'], 0, ',', '.') ?>
                                                    <span class="type-label type-cr">CR</span>
                                                </td>
                                                <td class="balance" style="color: #16794d;" data-label="Profit">
                                                    <?= number_format((float) ($item['line_profit_live'] ?? $item['line_profit'] ?? 0), 0, ',', '.') ?>
                                                </td>
                                                <td style="text-align:right;" data-label="Aksi">
                                                    <form method="post" onsubmit="event.preventDefault(); const f = this; askConfirmation('Hapus transaksi ini? Stok akan dikembalikan.', () => f.submit());" style="display:inline;">
                                                        <input type="hidden" name="action" value="delete_sale">
                                                        <input type="hidden" name="sale_id" value="<?= (int) $history['id'] ?>">
                                                        <button class="btn btn-danger" style="padding: 4px 8px; font-size: 11px;" type="submit">Hapus</button>
                                                    </form>
                                                </td>
                                            </tr>
                                        <?php endforeach; ?>
                                    <?php else: ?>
                                        <tr>
                                            <td colspan="6" class="small">Detail item transaksi tidak ditemukan.</td>
                                        </tr>
                                    <?php endif; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </div>
</div>