<style>
    .stok-grid {
        display: grid;
        grid-template-columns: .95fr 1.05fr;
        gap: 18px;
        margin-top: 18px;
    }

    .stok-search {
        display: grid;
        grid-template-columns: minmax(0, 1fr) auto;
        gap: 12px;
        align-items: end;
        margin-bottom: 16px;
    }

    .stok-list-wrap {
        overflow-x: auto;
    }

    .stok-item-table {
        width: 100%;
    }

    .stok-item-table tr.is-low-stock {
        background: #fff1f1;
    }

    .stok-item-table tr.is-low-stock td {
        color: #b42318;
    }

    .stok-item-table tr.is-selected {
        background: #fff8db;
    }

    .stok-pick-btn {
        min-width: 84px;
        padding: 8px 12px;
    }

    .stok-summary {
        display: grid;
        grid-template-columns: repeat(2, minmax(0, 1fr));
        gap: 12px;
        margin-bottom: 14px;
    }

    .stok-summary .detail-box {
        padding: 12px 14px;
    }

    .receive-highlight {
        background: linear-gradient(180deg, #fff7e3 0%, #fffdf8 100%);
        border: 1px solid #f4d27a;
        border-radius: 16px;
        padding: 14px 16px;
        margin-top: 14px;
    }

    .receive-history-list {
        display: grid;
        gap: 10px;
        margin-top: 16px;
    }

    .receive-history-item {
        border: 1px solid var(--line);
        border-radius: 14px;
        padding: 12px 14px;
        background: #fcfcfd;
    }

    .receive-history-head {
        display: flex;
        justify-content: space-between;
        gap: 10px;
        flex-wrap: wrap;
        align-items: flex-start;
    }

    .receive-history-actions {
        display: flex;
        gap: 8px;
        align-items: center;
        flex-wrap: wrap;
    }

    .receive-delete-form {
        margin: 0;
    }

    .receive-delete-btn {
        padding: 8px 12px;
        min-width: 84px;
    }

    @media (max-width: 920px) {

        .stok-grid,
        .stok-search,
        .stok-summary {
            grid-template-columns: 1fr;
        }
    }

    @media (max-width: 640px) {
        .stok-item-table thead {
            display: none;
        }

        .stok-item-table,
        .stok-item-table tbody,
        .stok-item-table tr,
        .stok-item-table td {
            display: block;
            width: 100%;
        }

        .stok-item-table tr {
            padding: 12px 0;
            border-bottom: 1px solid var(--line);
        }

        .stok-item-table td {
            border-bottom: none;
            padding: 8px 0;
        }

        .stok-item-table td::before {
            content: attr(data-label);
            display: block;
            margin-bottom: 4px;
            font-size: 11px;
            font-weight: 800;
            letter-spacing: .05em;
            text-transform: uppercase;
            color: #98a2b3;
        }

        .stok-pick-btn {
            width: 100%;
        }
    }
</style>

<div class="stok-grid">
    <div class="card">
        <h3>Receive Item</h3>
        <div class="small" style="margin-bottom:12px;">Pilih barang dari daftar sebelah kanan, lalu isi pembelian baru di sini. Harga beli aktif akan mengikuti pembelian terakhir yang Anda simpan.</div>

        <?php if (!empty($selectedItem)): ?>
            <?php
            $selectedSmallQty = max(1, (int) ($selectedItem['small_unit_qty'] ?? 1));
            $selectedStockDisplay = format_stock_breakdown((int) ($selectedItem['stock'] ?? 0), (string) ($selectedItem['unit_large'] ?? 'Bungkus'), (string) ($selectedItem['unit_small'] ?? 'Pcs'), $selectedSmallQty);
            ?>
            <div class="stok-summary">
                <div class="detail-box">
                    <div class="small">Barang Dipilih</div>
                    <strong><?= htmlspecialchars((string) ($selectedItem['name'] ?? '-')) ?></strong>
                    <div class="small" style="margin-top:4px;"><?= htmlspecialchars((string) ($selectedItem['code'] ?? '-')) ?></div>
                </div>
                <div class="detail-box">
                    <div class="small">Stok Saat Ini</div>
                    <strong><?= htmlspecialchars($selectedStockDisplay) ?></strong>
                    <div class="small" style="margin-top:4px;">Harga beli aktif: <?= rupiah((float) ($selectedItem['purchase_price'] ?? 0)) ?></div>
                </div>
            </div>

            <form method="post">
                <input type="hidden" name="item_id" value="<?= (int) $selectedItem['id'] ?>">
                <div class="form-grid">
                    <div>
                        <div class="small">Qty Besar Masuk</div>
                        <input type="number" name="qty_large" min="0" value="0" required inputmode="numeric">
                    </div>
                    <div>
                        <div class="small">Qty Sisa Kecil</div>
                        <input type="number" name="qty_small" min="0" value="0" required inputmode="numeric">
                    </div>
                    <div>
                        <div class="small">Harga Beli di Struk</div>
                        <input type="text" class="money-input" name="purchase_price" placeholder="Harga beli per satuan besar" required inputmode="numeric">
                        <div class="small" style="margin-top:4px;">Harga beli Struk: <?= rupiah((float) ($selectedItem[''] ?? 0)) ?></div>
                    </div>
                    <div>
                        <div class="small">Catatan</div>
                        <input type="text" name="notes" placeholder="Catatan pembelian untuk <?= htmlspecialchars((string) ($selectedItem['name'] ?? 'barang')) ?>">
                    </div>
                </div>
                <div class="receive-highlight">
                    <div class="small" style="margin-bottom:6px;">Ringkasan Receive</div>
                    <strong>Stok akan bertambah dari pembelian baru ini.</strong>
                    <div class="small" style="margin-top:6px;">Gunakan form ini hanya untuk barang masuk/pembelian baru. Koreksi selisih stok tetap dilakukan dari modul Stok Opname.</div>
                </div>
                <div style="margin-top:12px;">
                    <button type="submit">Simpan Receive Item</button>
                </div>
            </form>

            <div class="receive-history-list">
                <div class="section-title" style="margin-bottom:0;">History Receive</div>
                <?php if (!empty($selectedHistory)): ?>
                    <?php foreach ($selectedHistory as $row): ?>
                        <?php
                        $historySmallQty = max(1, (int) ($row['small_unit_qty'] ?? 1));
                        $historyLargeQty = (int) ($row['qty_large'] ?? 0);
                        $historySmallRemainder = (int) ($row['qty_small'] ?? 0);
                        $historyEffectiveLargeQty = ((float) ($row['qty_total'] ?? 0)) / $historySmallQty;
                        ?>
                        <div class="receive-history-item">
                            <div class="receive-history-head">
                                <strong><?= htmlspecialchars((string) ($row['transaction_date'] ?? '-')) ?></strong>
                                <div class="receive-history-actions">
                                    <span class="badge">Total <?= rupiah((float) ($row['purchase_total'] ?? 0)) ?></span>
                                    <form method="post" class="receive-delete-form" onsubmit="return confirm('Hapus history receive ini? Stok barang akan dikurangi kembali.');">
                                        <input type="hidden" name="action" value="delete_receive">
                                        <input type="hidden" name="item_id" value="<?= (int) ($selectedItem['id'] ?? 0) ?>">
                                        <input type="hidden" name="receive_id" value="<?= (int) ($row['id'] ?? 0) ?>">
                                        <button type="submit" class="btn btn-danger receive-delete-btn">Delete</button>
                                    </form>
                                </div>
                            </div>
                            <div class="small" style="margin-top:8px;">Qty masuk: <?= $historyLargeQty ?> <?= htmlspecialchars((string) ($row['unit_large'] ?? 'Bungkus')) ?><?= $historySmallRemainder > 0 ? ' ' . $historySmallRemainder . ' ' . htmlspecialchars((string) ($row['unit_small'] ?? 'Pcs')) : '' ?></div>
                            <div class="small" style="margin-top:4px;">Harga beli di struk: <?= rupiah((float) ($row['purchase_price'] ?? 0)) ?></div>
                            <div class="small" style="margin-top:4px;">Rumus total: <?= format_qty($historyEffectiveLargeQty) ?> x <?= rupiah((float) ($row['purchase_price'] ?? 0)) ?> = <?= rupiah((float) ($row['purchase_total'] ?? 0)) ?></div>
                            <div class="small" style="margin-top:4px;">Catatan: <?= htmlspecialchars((string) (($row['notes'] ?? '') !== '' ? $row['notes'] : '-')) ?></div>
                        </div>
                    <?php endforeach; ?>
                <?php else: ?>
                    <div class="info-strip">
                        <div class="small">Belum ada history receive untuk barang ini.</div>
                    </div>
                <?php endif; ?>
            </div>
        <?php else: ?>
            <div class="info-strip">
                <div class="small">Belum ada barang yang dipilih. Pilih dulu dari daftar barang di sebelah kanan.</div>
            </div>
        <?php endif; ?>
    </div>

    <div class="card">
        <h3>Daftar Barang</h3>
        <form method="get" class="stok-search">
            <input type="hidden" name="route" value="stok/receive">
            <div>
                <div class="small">Cari Barang</div>
                <input type="text" name="q" value="<?= htmlspecialchars((string) ($keyword ?? '')) ?>" placeholder="Cari kode, barcode, atau nama barang">
            </div>
            <div class="search-reset-actions">
                <button type="submit" class="btn btn-secondary">Search</button>
                <a href="index.php?route=stok/receive" class="btn btn-info">Reset</a>
            </div>
        </form>

        <div class="stok-list-wrap">
            <table class="stok-item-table">
                <thead>
                    <tr>
                        <th>Kode</th>
                        <th>Barang</th>
                        <th>Stok</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach (($items ?? []) as $item): ?>
                        <?php
                        $isLowStock = !empty($item['low_stock']);
                        $isSelected = (int) ($selectedItem['id'] ?? 0) === (int) ($item['id'] ?? 0);
                        $pickQuery = ['route' => 'stok/receive', 'item' => (int) $item['id']];
                        if (!empty($keyword)) {
                            $pickQuery['q'] = $keyword;
                        }
                        ?>
                        <tr class="<?= $isLowStock ? 'is-low-stock' : '' ?> <?= $isSelected ? 'is-selected' : '' ?>">
                            <td data-label="Kode">
                                <strong><?= htmlspecialchars((string) ($item['code'] ?? '-')) ?></strong>
                                <div class="small"><?= htmlspecialchars((string) (($item['barcode'] ?? '') !== '' ? $item['barcode'] : '-')) ?></div>
                            </td>
                            <td data-label="Barang">
                                <strong><?= htmlspecialchars((string) ($item['name'] ?? '-')) ?></strong>
                                <div class="small">1 <?= htmlspecialchars((string) ($item['unit_large'] ?? 'Bungkus')) ?> = <?= (int) ($item['small_unit_qty'] ?? 1) ?> <?= htmlspecialchars((string) ($item['unit_small'] ?? 'Pcs')) ?></div>
                            </td>
                            <td data-label="Stok">
                                <strong><?= htmlspecialchars((string) ($item['stock_display'] ?? '0')) ?></strong>
                                <?php if ($isLowStock): ?>
                                    <div class="small" style="color:#b42318; font-weight:700; margin-top:4px;"><?= htmlspecialchars((string) ($item['low_stock_note'] ?? 'Stok kurang dari 3')) ?></div>
                                <?php endif; ?>
                            </td>
                            <td data-label="Aksi">
                                <a class="btn btn-secondary stok-pick-btn" href="index.php?<?= http_build_query($pickQuery) ?>">Pilih</a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<script>
    document.querySelectorAll('.money-input').forEach(function(input) {
        input.addEventListener('input', function() {
            const digits = this.value.replace(/[^\d]/g, '');
            this.value = digits === '' ? '' : Number(digits).toLocaleString('id-ID');
        });
    });
</script>