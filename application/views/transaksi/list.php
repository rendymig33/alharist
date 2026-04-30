<style>
    .txn-search {
        display: grid;
        grid-template-columns: minmax(0, 1fr) auto;
        gap: 12px;
        align-items: end;
        margin: 18px 0;
    }

    .txn-list-wrap {
        overflow-x: auto;
    }

    .txn-date-group {
        border: 1px solid var(--line);
        border-radius: 16px;
        overflow: hidden;
        background: #fff;
        margin-top: 14px;
    }

    .txn-date-head {
        display: flex;
        justify-content: space-between;
        align-items: center;
        gap: 12px;
        padding: 14px 16px;
        background: linear-gradient(180deg, #fcfcfd, #f8fafc);
        border-bottom: 1px solid var(--line);
    }

    .txn-date-head h4 {
        margin: 0;
    }

    @media (max-width: 720px) {
        .txn-search {
            grid-template-columns: 1fr;
        }
    }

    @media (max-width: 640px) {
        .txn-table thead {
            display: none;
        }

        .txn-table,
        .txn-table tbody,
        .txn-table tr,
        .txn-table td {
            display: block;
            width: 100%;
        }

        .txn-table tr {
            padding: 12px 0;
            border-bottom: 1px solid var(--line);
        }

        .txn-table td {
            border-bottom: none;
            padding: 8px 0;
        }

        .txn-table td::before {
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

<div class="card">
    <div style="display:flex; justify-content:space-between; gap:12px; align-items:flex-start; flex-wrap:wrap;">
        <div>
            <h3 style="margin-bottom:6px;">List Transaksi</h3>
            <div class="small">Riwayat transaksi terpisah dari halaman kasir agar lebih rapi.</div>
        </div>
        <div class="badge">Total Transaksi: <?= count($sales) ?></div>
    </div>

    <form method="get" class="txn-search">
        <input type="hidden" name="route" value="transaksi/list">
        <div>
            <div class="small">Cari Transaksi</div>
            <input type="text" name="q" value="<?= htmlspecialchars((string) ($keyword ?? '')) ?>" placeholder="Cari invoice, pembayaran, atau tanggal">
        </div>
        <div class="search-reset-actions">
            <button type="submit" class="btn btn-secondary">Search</button>
            <a href="index.php?route=transaksi/list" class="btn btn-info">Reset</a>
        </div>
    </form>

    <?php
    $groupedSales = [];
    foreach ($sales as $sale) {
        $dateKey = (string) ($sale['transaction_date'] ?? '');
        $groupedSales[$dateKey][] = $sale;
    }
    ?>
    <?php foreach ($groupedSales as $dateKey => $rows): ?>
        <?php
        $dateTotal = array_sum(array_map(fn($row) => (float) ($row['subtotal'] ?? 0), $rows));
        $dateProfit = array_sum(array_map(fn($row) => (float) ($row['total_profit_live'] ?? $row['total_profit'] ?? 0), $rows));
        ?>
        <div class="txn-date-group">
            <div class="txn-date-head">
                <div>
                    <h4><?= htmlspecialchars($dateKey) ?></h4>
                    <div class="small"><?= count($rows) ?> transaksi pada tanggal ini</div>
                </div>
                <div class="badge">Total <?= rupiah($dateTotal) ?> | Profit <?= rupiah($dateProfit) ?></div>
            </div>
            <div class="txn-list-wrap">
                <table class="txn-table">
                    <thead>
                        <tr>
                            <th>Invoice</th>
                            <th>Pembayaran</th>
                            <th>Total</th>
                            <th>Profit</th>
                            <th>Dibayar</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($rows as $sale): ?>
                            <tr>
                                <td data-label="Invoice"><strong><?= htmlspecialchars((string) $sale['invoice_no']) ?></strong></td>
                                <td data-label="Pembayaran"><?= htmlspecialchars((string) $sale['payment_type']) ?></td>
                                <td data-label="Total"><?= rupiah((float) $sale['subtotal']) ?></td>
                                <td data-label="Profit"><?= rupiah((float) ($sale['total_profit_live'] ?? $sale['total_profit'] ?? 0)) ?></td>
                                <td data-label="Dibayar"><?= rupiah((float) $sale['total_paid']) ?></td>
                                <td data-label="Aksi">
                                    <form method="post" onsubmit="return confirm('Hapus transaksi ini?');" style="margin:0;">
                                        <input type="hidden" name="action" value="delete_sale">
                                        <input type="hidden" name="sale_id" value="<?= (int) $sale['id'] ?>">
                                        <button class="btn btn-danger" type="submit">Hapus</button>
                                    </form>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    <?php endforeach; ?>
</div>
