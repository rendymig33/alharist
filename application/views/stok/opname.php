<?php
$currentPage = $currentPage ?? 1;
$totalPages = $totalPages ?? 1;
?>
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
        background: #bee8fc;
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

    .opname-history-list {
        display: grid;
        gap: 10px;
        margin-top: 16px;
    }

    .opname-history-item {
        border: 1px solid var(--line);
        border-radius: 14px;
        padding: 12px 14px;
        background: #fcfcfd;
    }

    .opname-history-head {
        display: flex;
        justify-content: space-between;
        gap: 10px;
        flex-wrap: wrap;
        align-items: flex-start;
    }

    .opname-history-actions {
        display: flex;
        gap: 8px;
        align-items: center;
        flex-wrap: wrap;
    }

    .opname-delete-form {
        margin: 0;
    }

    .opname-delete-btn {
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

    .pagination-wrap {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-top: 20px;
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

    .pagination-btns {
        display: flex;
        gap: 8px;
    }

    .btn-pagination {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        padding: 8px 16px;
        border-radius: 10px;
        font-weight: 600;
        transition: all 0.2s;
    }

    .btn-pagination:hover:not(:disabled) {
        transform: translateY(-1px);
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.05);
    }
</style>

<div class="stok-grid">
    <div class="card">
        <h3>Koreksi Opname</h3>
        <div class="small" style="margin-bottom:12px;">Pilih barang dari daftar sebelah kanan, lalu isi stok aktual hasil opname di sini.</div>

        <?php if (!empty($selectedItem)): ?>
            <?php
            $selectedSmallQty = max(1, (int) ($selectedItem['small_unit_qty'] ?? 1));
            $selectedStockDisplay = format_stock_breakdown((int) ($selectedItem['stock'] ?? 0), (string) ($selectedItem['unit_large'] ?? 'Bungkus'), (string) ($selectedItem['unit_small'] ?? 'Pcs'), $selectedSmallQty);
            $selectedParts = split_stock_units((int) ($selectedItem['stock'] ?? 0), $selectedSmallQty);
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
                    <div class="small" style="margin-top:4px;">Total kecil: <?= (int) ($selectedItem['stock'] ?? 0) ?></div>
                </div>
            </div>

            <form method="post">
                <input type="hidden" name="item_id" value="<?= (int) $selectedItem['id'] ?>">
                <div class="form-grid">
                    <div>
                        <div class="small">Stok Aktual Besar</div>
                        <input type="number" name="qty_large" min="0" value="<?= (int) $selectedParts['large'] ?>" required>
                    </div>
                    <div>
                        <div class="small">Stok Aktual Kecil</div>
                        <input type="number" name="qty_small" min="0" value="<?= (int) $selectedParts['small'] ?>" required>
                    </div>
                    <div style="grid-column:1 / -1;">
                        <div class="small">Catatan</div>
                        <input type="text" name="notes" placeholder="Catatan opname untuk <?= htmlspecialchars((string) ($selectedItem['name'] ?? 'barang')) ?>">
                    </div>
                </div>
                <div style="margin-top:12px;">
                    <button type="submit">Simpan Stok Opname</button>
                </div>
            </form>

            <div class="opname-history-list">
                <div class="section-title" style="margin-bottom:0;">History Koreksi</div>
                <?php if (!empty($selectedHistory)): ?>
                    <?php foreach ($selectedHistory as $index => $row): ?>
                        <?php
                        $historySmallQty = max(1, (int) ($row['small_unit_qty'] ?? 1));
                        $beforeDisplay = format_stock_breakdown((int) ($row['before_stock'] ?? 0), (string) ($row['unit_large'] ?? 'Bungkus'), (string) ($row['unit_small'] ?? 'Pcs'), $historySmallQty);
                        $actualDisplay = format_stock_breakdown((int) ($row['actual_stock'] ?? 0), (string) ($row['unit_large'] ?? 'Bungkus'), (string) ($row['unit_small'] ?? 'Pcs'), $historySmallQty);
                        $isLatestHistory = $index === 0;
                        ?>
                        <div class="opname-history-item">
                            <div class="opname-history-head">
                                <strong><?= htmlspecialchars((string) ($row['transaction_date'] ?? '-')) ?></strong>
                                <div class="opname-history-actions">
                                    <span class="badge">Selisih <?= format_qty((float) ($row['adjustment'] ?? 0)) ?></span>
                                    <?php if ($isLatestHistory): ?>
                                        <form method="post" class="opname-delete-form" onsubmit="return confirm('Hapus history stok opname ini? Stok akan dikembalikan ke nilai sebelum koreksi.');">
                                            <input type="hidden" name="action" value="delete_opname">
                                            <input type="hidden" name="item_id" value="<?= (int) ($selectedItem['id'] ?? 0) ?>">
                                            <input type="hidden" name="opname_id" value="<?= (int) ($row['id'] ?? 0) ?>">
                                            <button type="submit" class="btn btn-danger opname-delete-btn">Delete</button>
                                        </form>
                                    <?php else: ?>
                                        <span class="small" style="color:#98a2b3; font-weight:700;">Terkunci</span>
                                    <?php endif; ?>
                                </div>
                            </div>
                            <div class="small" style="margin-top:8px;">Sebelum: <?= htmlspecialchars($beforeDisplay) ?></div>
                            <div class="small" style="margin-top:4px;">Aktual: <?= htmlspecialchars($actualDisplay) ?></div>
                            <div class="small" style="margin-top:4px;">Catatan: <?= htmlspecialchars((string) (($row['notes'] ?? '') !== '' ? $row['notes'] : '-')) ?></div>
                        </div>
                    <?php endforeach; ?>
                <?php else: ?>
                    <div class="info-strip">
                        <div class="small">Belum ada history koreksi untuk barang ini. Kalau ada penjualan atau perubahan stok, perbedaannya akan terlihat saat opname berikutnya.</div>
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
            <input type="hidden" name="route" value="stok/opname">
            <div>
                <div class="small">Cari Barang</div>
                <input type="text" name="q" value="<?= htmlspecialchars((string) ($keyword ?? '')) ?>" placeholder="Cari kode, barcode, atau nama barang">
            </div>
            <div class="search-reset-actions">
                <button type="submit" class="btn btn-secondary">Search</button>
                <a href="index.php?route=stok/opname" class="btn btn-info">Reset</a>
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
                        $pickQuery = ['route' => 'stok/opname', 'item' => (int) $item['id'], 'p' => $currentPage];
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

        <?php if ($totalPages > 1): ?>
            <div class="pagination-wrap">
                <div class="pagination-info">
                    Halaman <?= $currentPage ?> dari <?= $totalPages ?>
                </div>
                <div class="pagination-btns">
                    <?php
                    $prevQuery = ['route' => 'stok/opname', 'p' => $currentPage - 1];
                    if (!empty($keyword)) $prevQuery['q'] = $keyword;
                    if (!empty($selectedItem['id'])) $prevQuery['item'] = $selectedItem['id'];

                    $nextQuery = ['route' => 'stok/opname', 'p' => $currentPage + 1];
                    if (!empty($keyword)) $nextQuery['q'] = $keyword;
                    if (!empty($selectedItem['id'])) $nextQuery['item'] = $selectedItem['id'];
                    ?>

                    <?php if ($currentPage > 1): ?>
                        <a href="index.php?<?= http_build_query($prevQuery) ?>" class="btn btn-secondary btn-pagination">
                            <span>&larr;</span> Prev
                        </a>
                    <?php endif; ?>

                    <?php if ($currentPage < $totalPages): ?>
                        <a href="index.php?<?= http_build_query($nextQuery) ?>" class="btn btn-secondary btn-pagination">
                            Next <span>&rarr;</span>
                        </a>
                    <?php endif; ?>
                </div>
            </div>
        <?php endif; ?>
    </div>
</div>