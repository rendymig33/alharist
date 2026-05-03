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
        $shiftKey = (int) ($sale['shift'] ?? 1);
        $groupedSales[$dateKey][$shiftKey][] = $sale;
    }
    ?>
    <?php foreach ($groupedSales as $dateKey => $shifts): ?>
        <div class="txn-date-group" style="margin-bottom: 24px;">
            <div class="txn-date-head" style="background: #fef9c3; color: #854d0e; border-radius: 12px 12px 0 0; border-bottom: 1px solid #fde047;">
                <div>
                    <h4 style="color: #854d0e;"><?= htmlspecialchars($dateKey) ?></h4>
                    <div class="small" style="color: #a16207;">Rekapitulasi Harian</div>
                </div>
            </div>

            <?php foreach ([1, 2] as $sIdx): ?>
                <?php if (isset($shifts[$sIdx])): ?>
                    <?php
                    $rows = $shifts[$sIdx];
                    $shiftTotal = array_sum(array_map(fn($row) => (float) ($row['subtotal'] ?? 0), $rows));
                    $shiftProfit = array_sum(array_map(fn($row) => (float) ($row['total_profit_live'] ?? $row['total_profit'] ?? 0), $rows));
                    ?>
                    <div style="padding: 10px 16px; background: #f8fafc; border-bottom: 1px solid #e2e8f0; display: flex; justify-content: space-between; align-items: center;">
                        <strong style="color: #334155;">SHIFT <?= $sIdx ?></strong>
                        <div class="badge" style="background: #1570ef; color: #fff; border: none;">Total <?= rupiah($shiftTotal) ?> | Profit <?= rupiah($shiftProfit) ?></div>
                    </div>
                    <div class="txn-list-wrap">
                        <div class="bca-ledger-wrap">
                            <table class="bca-ledger">
                                <tbody>
                                    <?php foreach ($rows as $sale): ?>
                                        <tr>
                                            <td class="desc" data-label="Invoice">
                                                <span class="desc-main"><?= htmlspecialchars((string) $sale['invoice_no']) ?></span>
                                                <span class="desc-sub"><?= htmlspecialchars((string) $sale['payment_type']) ?></span>
                                            </td>
                                            <td class="amount cr" data-label="Total">
                                                <?= number_format((float) $sale['subtotal'], 0, ',', '.') ?>
                                                <span class="type-label type-cr">CR</span>
                                            </td>
                                            <td class="balance" style="color: #064e3b; font-weight: 700;" data-label="Profit">
                                                <?= number_format((float) ($sale['total_profit_live'] ?? $sale['total_profit'] ?? 0), 0, ',', '.') ?>
                                            </td>
                                            <td style="text-align:right;" data-label="Aksi">
                                                <div class="action-row" style="justify-content: flex-end; gap: 6px;">
                                                    <a href="index.php?route=transaksi/detail&id=<?= (int) $sale['id'] ?>" class="btn btn-info" style="padding: 4px 10px; font-size: 11px;">View</a>
                                                    <form method="post" onsubmit="event.preventDefault(); const f = this; askConfirmation('Hapus transaksi ini?', () => f.submit());" style="margin:0;">
                                                        <input type="hidden" name="action" value="delete_sale">
                                                        <input type="hidden" name="sale_id" value="<?= (int) $sale['id'] ?>">
                                                        <button class="btn btn-danger" style="padding: 4px 10px; font-size: 11px;" type="submit">Hapus</button>
                                                    </form>
                                                </div>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                <?php endif; ?>
            <?php endforeach; ?>
        </div>
    <?php endforeach; ?>
</div>