<div class="toolbar">
    <div>
        <div class="section-title">Detail Transaksi</div>
        <h3><?= htmlspecialchars((string) $sale['invoice_no']) ?></h3>
    </div>
    <a href="index.php?route=transaksi/list" class="btn btn-secondary">Kembali ke List</a>
</div>

<div class="grid" style="grid-template-columns: 1.2fr .8fr; gap: 18px; margin-top: 18px;">
    <div>
        <div class="card">
            <h3>Daftar Item</h3>
            <div class="bca-ledger-wrap">
                <table class="bca-ledger">
                    <thead>
                        <tr>
                            <th>Item / Kode</th>
                            <th style="text-align:right;">Qty</th>
                            <th style="text-align:right;">Harga</th>
                            <th style="text-align:right;">Subtotal</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($items as $item): ?>
                            <tr>
                                <td class="desc" data-label="Item / Kode">
                                    <span class="desc-main"><?= htmlspecialchars((string) $item['item_name']) ?></span>
                                    <span class="desc-sub"><?= htmlspecialchars((string) $item['item_code']) ?></span>
                                </td>
                                <td class="amount" data-label="Qty">
                                    <?= format_qty((float) $item['qty']) ?>
                                </td>
                                <td class="amount" data-label="Harga">
                                    <?= number_format((float) $item['selling_price'], 0, ',', '.') ?>
                                </td>
                                <td class="amount cr" data-label="Subtotal">
                                    <?= number_format((float) $item['line_total'], 0, ',', '.') ?>
                                    <span class="type-label type-cr">CR</span>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <div>
        <div class="card">
            <h3>Ringkasan Pembayaran</h3>
            <div style="display: grid; gap: 14px; margin-top: 12px;">
                <div class="detail-box">
                    <div class="small">Total Transaksi</div>
                    <strong style="font-size: 24px; display: block; margin-top: 6px;"><?= rupiah((float) $sale['subtotal']) ?></strong>
                </div>
                <div class="detail-box">
                    <div class="small">Metode Pembayaran</div>
                    <strong><?= htmlspecialchars((string) $sale['payment_type']) ?></strong>
                </div>
                <div class="detail-box">
                    <div class="small">Total Dibayar</div>
                    <strong><?= rupiah((float) $sale['total_paid']) ?></strong>
                </div>
                <div class="detail-box" style="background: #f0fdf4; border-color: #bbf7d0;">
                    <div class="small" style="color: #166534;">Total Profit</div>
                    <strong style="color: #166534; font-size: 20px;"><?= rupiah((float) ($sale['total_profit_live'] ?? $sale['total_profit'] ?? 0)) ?></strong>
                </div>
                <div class="detail-box">
                    <div class="small">Tanggal Transaksi</div>
                    <strong><?= htmlspecialchars((string) $sale['transaction_date']) ?></strong>
                </div>
                <div class="detail-box">
                    <div class="small">Shift</div>
                    <strong>Shift <?= (int) ($sale['shift'] ?? 1) ?></strong>
                </div>
            </div>
        </div>
    </div>
</div>