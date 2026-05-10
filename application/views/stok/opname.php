<?php
$currentPage = $currentPage ?? 1;
$totalPages = $totalPages ?? 1;
?>
<style>
    .stok-search {
        display: grid;
        grid-template-columns: minmax(0, 1fr) auto;
        gap: 12px;
        align-items: end;
        margin-bottom: 20px;
        background: #ffffff;
        padding: 16px;
        border-radius: 16px;
        border: 1px solid var(--line);
        box-shadow: 0 4px 12px rgba(0,0,0,0.02);
    }

    .stok-list-wrap {
        overflow-x: auto;
        border-radius: 16px;
        border: 1px solid var(--line);
        background: #fff;
        box-shadow: 0 4px 12px rgba(0,0,0,0.02);
    }

    .stok-item-table {
        width: 100%;
        border-collapse: collapse;
    }

    .stok-item-table th {
        background: #f8fafc;
        color: #475569;
        font-weight: 700;
        text-transform: uppercase;
        font-size: 12px;
        letter-spacing: 0.5px;
        padding: 14px 16px;
        border-bottom: 2px solid var(--line);
        position: sticky;
        top: 0;
        z-index: 10;
        text-align: left;
    }

    .stok-item-table td {
        padding: 16px;
        vertical-align: middle;
        border-bottom: 1px solid var(--line);
        transition: all 0.2s ease;
    }

    .stok-item-table tr {
        transition: all 0.3s ease;
    }

    .stok-item-table tbody tr:hover {
        background-color: #f8fafc;
        transform: translateY(-1px);
        box-shadow: 0 4px 12px rgba(0,0,0,0.03);
        z-index: 1;
        position: relative;
    }

    .stok-item-table tr.is-low-stock {
        background: linear-gradient(to right, #fff5f5, #fffafa);
    }
    
    .stok-item-table tr.is-low-stock:hover {
        background: #ffecec;
    }

    .stok-item-table tr.is-low-stock td {
        color: #b42318;
    }

    .stok-item-table tr.is-selected {
        background: linear-gradient(to right, #eff6ff, #f8fafc);
        border-left: 4px solid #3b82f6;
    }

    .stok-item-table tr.is-selected td {
        border-bottom-color: #bfdbfe;
    }

    .stok-pick-btn {
        min-width: 84px;
        padding: 8px 16px;
        border-radius: 10px;
        font-weight: 600;
        transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        text-decoration: none;
    }

    .stok-summary {
        display: grid;
        grid-template-columns: repeat(2, minmax(0, 1fr));
        gap: 16px;
        margin-bottom: 20px;
    }

    .stok-summary .detail-box {
        padding: 16px;
        background: linear-gradient(135deg, #ffffff, #f8fafc);
        border: 1px solid #e2e8f0;
        border-radius: 16px;
        box-shadow: 0 4px 15px rgba(0, 0, 0, 0.03);
        transition: transform 0.3s ease, box-shadow 0.3s ease;
        position: relative;
        overflow: hidden;
    }
    
    .stok-summary .detail-box::before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        width: 4px;
        height: 100%;
        background: var(--yellow);
        border-radius: 4px 0 0 4px;
    }
    
    .stok-summary .detail-box:nth-child(2)::before {
        background: #3b82f6;
    }

    .card-modern {
        background: #ffffff;
        border-radius: 20px;
        box-shadow: 0 10px 30px rgba(0, 0, 0, 0.04);
        border: 1px solid rgba(226, 228, 234, 0.5);
        padding: 24px;
        transition: box-shadow 0.3s ease;
        animation: fadeUp 0.6s ease-out forwards;
    }

    @keyframes fadeUp {
        from { opacity: 0; transform: translateY(15px); }
        to { opacity: 1; transform: translateY(0); }
    }

    @media (max-width: 920px) {
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
            padding: 16px 12px;
            border-bottom: 1px solid var(--line);
            border-radius: 12px;
            margin-bottom: 12px;
            background: #fff;
            box-shadow: 0 2px 8px rgba(0,0,0,0.02);
        }

        .stok-item-table td {
            border-bottom: none;
            padding: 8px 0;
        }

        .stok-item-table td::before {
            content: attr(data-label);
            display: block;
            margin-bottom: 6px;
            font-size: 11px;
            font-weight: 800;
            letter-spacing: .05em;
            text-transform: uppercase;
            color: #64748b;
        }

        .stok-pick-btn {
            width: 100%;
            margin-top: 8px;
            text-align: center;
        }
    }

    .pagination-wrap {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-top: 24px;
        padding-top: 24px;
        border-top: 1px solid var(--line);
    }

    .pagination-info {
        font-size: 13px;
        font-weight: 700;
        color: #475569;
        background: #f8fafc;
        padding: 8px 16px;
        border-radius: 999px;
        border: 1px solid #e2e8f0;
        box-shadow: 0 2px 5px rgba(0,0,0,0.02);
    }

    .pagination-btns {
        display: flex;
        gap: 10px;
    }

    .btn-pagination {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        padding: 10px 18px;
        border-radius: 12px;
        font-weight: 600;
        transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        background: #ffffff;
        border: 1px solid var(--line);
        color: #334155;
        box-shadow: 0 2px 6px rgba(0,0,0,0.03);
        text-decoration: none;
    }

    .btn-pagination:hover:not(:disabled) {
        transform: translateY(-2px);
        box-shadow: 0 6px 15px rgba(0, 0, 0, 0.06);
        border-color: #cbd5e1;
        background: #f8fafc;
    }
    
    .modal-input {
        background: #f8fafc;
        border: 1px solid #e2e8f0;
        border-radius: 12px;
        padding: 12px 16px;
        transition: all 0.3s ease;
        width: 100%;
        box-sizing: border-box;
    }
    
    .modal-input:focus {
        background: #ffffff;
        box-shadow: 0 0 0 4px rgba(59, 130, 246, 0.1);
        border-color: #3b82f6;
        outline: none;
    }
    
    .modern-btn {
        border-radius: 12px;
        padding: 12px 20px;
        font-weight: 700;
        letter-spacing: 0.3px;
        text-transform: uppercase;
        font-size: 13px;
        transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        display: inline-flex;
        align-items: center;
        justify-content: center;
        cursor: pointer;
        text-decoration: none;
    }

    .modern-btn:hover {
        transform: translateY(-2px);
    }
    
    .section-header {
        position: relative;
        padding-bottom: 12px;
        margin-bottom: 20px;
    }
    
    .section-header::after {
        content: '';
        position: absolute;
        bottom: 0;
        left: 0;
        width: 40px;
        height: 3px;
        background: var(--red);
        border-radius: 3px;
    }

    .modal-tabs {
        display: flex;
        border-bottom: 1px solid var(--line);
        margin-bottom: 20px;
        gap: 20px;
    }

    .modal-tab {
        padding: 12px 0;
        font-weight: 600;
        color: #64748b;
        cursor: pointer;
        border-bottom: 2px solid transparent;
        transition: all 0.2s;
        margin-bottom: -1px;
    }

    .modal-tab:hover {
        color: #0f172a;
    }

    .modal-tab.active {
        color: var(--red);
        border-bottom-color: var(--red);
    }

    .tab-content {
        display: none;
    }

    .tab-content.active {
        display: block;
        animation: fadeIn 0.3s ease;
    }

    @keyframes fadeIn {
        from { opacity: 0; transform: translateY(5px); }
        to { opacity: 1; transform: translateY(0); }
    }
</style>

<div class="card card-modern">
    <div class="section-header">
        <h3 style="margin:0;">Daftar Barang untuk Opname</h3>
    </div>
    <div class="small" style="margin-bottom:24px; color:#64748b; font-size:14px;">Cari barang dan klik tombol <strong>Koreksi Stok</strong> untuk melakukan penyesuaian jumlah stok fisik.</div>

    <form method="get" class="stok-search" style="grid-template-columns: minmax(0, 1.2fr) minmax(0, 1.8fr) auto;">
        <input type="hidden" name="route" value="stok/opname">
        <div>
            <div class="small" style="font-weight:600; margin-bottom:8px;">Kategori</div>
            <select name="cat" class="modal-input" onchange="this.form.submit()">
                <option value="">- Semua Kategori -</option>
                <?php foreach (($categories ?? []) as $cat): ?>
                    <option value="<?= htmlspecialchars($cat) ?>" <?= ($category ?? '') === $cat ? 'selected' : '' ?>><?= htmlspecialchars($cat) ?></option>
                <?php endforeach; ?>
            </select>
        </div>
        <div>
            <div class="small" style="font-weight:600; margin-bottom:8px;">Cari Barang</div>
            <input type="text" name="q" value="<?= htmlspecialchars((string) ($keyword ?? '')) ?>" class="modal-input" placeholder="Cari kode, barcode, atau nama barang">
        </div>
        <div class="search-reset-actions" style="display:flex; flex-direction:column; gap:8px;">
            <div style="display:flex; gap:8px; height:100%;">
                <button type="submit" class="modern-btn" style="flex:1; background:#eff6ff; color:#1d4ed8; border:1px solid #bfdbfe;">Cari</button>
                <a href="index.php?route=stok/opname" class="modern-btn" style="flex:1; background:#f1f5f9; color:#475569; border:1px solid #e2e8f0;">Reset</a>
            </div>
            <div style="display:flex; gap:8px; height:100%;">
                <a href="index.php?route=stok/opname_print&q=<?= urlencode($keyword ?? '') ?>&cat=<?= urlencode($category ?? '') ?>" target="_blank" class="modern-btn" style="flex:1; background:#f0fdf4; color:#166534; border:1px solid #bbf7d0; text-align:center; padding:12px 10px;">🖨️ Cetak Kartu</a>
                <a href="index.php?route=stok/opname_print_list&q=<?= urlencode($keyword ?? '') ?>&cat=<?= urlencode($category ?? '') ?>" target="_blank" class="modern-btn" style="flex:1; background:#fffbeb; color:#92400e; border:1px solid #fde68a; text-align:center; padding:12px 10px;">🖨️ Cetak List</a>
            </div>
        </div>
    </form>

    <div class="stok-list-wrap">
        <table class="stok-item-table">
            <thead>
                <tr>
                    <th>Kode</th>
                    <th>Barang</th>
                    <th>Stok Sistem</th>
                    <th style="text-align:right;">Aksi</th>
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
                        <td data-label="Stok Sistem">
                            <strong><?= htmlspecialchars((string) ($item['stock_display'] ?? '0')) ?></strong>
                            <?php if ($isLowStock): ?>
                                <div class="small" style="color:#b42318; font-weight:700; margin-top:4px;"><?= htmlspecialchars((string) ($item['low_stock_note'] ?? 'Stok kurang dari 3')) ?></div>
                            <?php endif; ?>
                        </td>
                        <td data-label="Aksi" style="text-align:right;">
                            <a class="stok-pick-btn" href="index.php?<?= http_build_query($pickQuery) ?>" style="display:inline-block; background: linear-gradient(135deg, var(--red), #b91c1c); color: white; box-shadow: 0 4px 12px rgba(215, 25, 32, 0.15);">Koreksi Stok</a>
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

                $nextQuery = ['route' => 'stok/opname', 'p' => $currentPage + 1];
                if (!empty($keyword)) $nextQuery['q'] = $keyword;
                ?>

                <?php if ($currentPage > 1): ?>
                    <a href="index.php?<?= http_build_query($prevQuery) ?>" class="btn-pagination">
                        Prev
                    </a>
                <?php endif; ?>

                <?php if ($currentPage < $totalPages): ?>
                    <a href="index.php?<?= http_build_query($nextQuery) ?>" class="btn-pagination">
                        Next
                    </a>
                <?php endif; ?>
            </div>
        </div>
    <?php endif; ?>
</div>

<?php if (!empty($selectedItem)): ?>
<?php
$closeQuery = ['route' => 'stok/opname', 'p' => $currentPage];
if (!empty($keyword)) {
    $closeQuery['q'] = $keyword;
}
$closeUrl = 'index.php?' . http_build_query($closeQuery);
?>
<div class="modal-backdrop active" id="koreksi-modal">
    <div class="modal" style="width: min(700px, 100%);">
        <div class="modal-head">
            <h3 style="margin:0; font-size:20px; color:#0f172a;">Koreksi Stok: <?= htmlspecialchars((string) $selectedItem['name']) ?></h3>
            <a href="<?= $closeUrl ?>" class="modal-close" style="text-decoration:none; display:flex; align-items:center; justify-content:center; width:32px; height:32px; padding:0; border-radius:50%; background:#f1f5f9; color:#64748b;">✕</a>
        </div>

        <div class="modal-tabs">
            <div class="modal-tab active" onclick="switchTab('form', event)">Form Koreksi</div>
            <div class="modal-tab" onclick="switchTab('history', event)">History Koreksi</div>
        </div>

        <div id="tab-form" class="tab-content active">
            <?php
            $selectedSmallQty = max(1, (int) ($selectedItem['small_unit_qty'] ?? 1));
            $selectedStockDisplay = format_stock_breakdown((int) ($selectedItem['stock'] ?? 0), (string) ($selectedItem['unit_large'] ?? 'Bungkus'), (string) ($selectedItem['unit_small'] ?? 'Pcs'), $selectedSmallQty);
            $selectedParts = split_stock_units((int) ($selectedItem['stock'] ?? 0), $selectedSmallQty);
            ?>
            <div class="stok-summary" style="grid-template-columns: 1fr; margin-bottom: 24px; gap:0;">
                <div class="detail-box" style="display:flex; justify-content:space-between; align-items:center; background:#f8fafc; border:1px solid #e2e8f0; border-radius:12px;">
                    <div>
                        <div class="small" style="font-weight:600; color:#64748b;">Stok Sistem Saat Ini</div>
                        <strong style="font-size:18px; color:#0f172a;"><?= htmlspecialchars($selectedStockDisplay) ?></strong>
                    </div>
                    <div class="small" style="text-align:right; color:#64748b;">
                        Total satuan kecil:<br><strong style="color:#0f172a;"><?= (int) ($selectedItem['stock'] ?? 0) ?> <?= htmlspecialchars((string) ($selectedItem['unit_small'] ?? 'Pcs')) ?></strong>
                    </div>
                </div>
            </div>

            <form method="post" action="index.php?route=stok/opname">
                <input type="hidden" name="item_id" value="<?= (int) $selectedItem['id'] ?>">
                <?php if (!empty($keyword)): ?>
                <input type="hidden" name="q" value="<?= htmlspecialchars($keyword) ?>">
                <?php endif; ?>
                <input type="hidden" name="p" value="<?= $currentPage ?>">
                
                <div class="form-grid" style="margin-bottom: 20px;">
                    <div>
                        <div class="small" style="margin-bottom:8px; font-weight:600; color:#475569;">Stok Aktual (<?= htmlspecialchars((string) ($selectedItem['unit_large'] ?? 'Bungkus')) ?>)</div>
                        <input type="number" name="qty_large" min="0" value="<?= (int) $selectedParts['large'] ?>" required class="modal-input">
                    </div>
                    <div>
                        <div class="small" style="margin-bottom:8px; font-weight:600; color:#475569;">Stok Aktual (<?= htmlspecialchars((string) ($selectedItem['unit_small'] ?? 'Pcs')) ?>)</div>
                        <input type="number" name="qty_small" min="0" value="<?= (int) $selectedParts['small'] ?>" required class="modal-input">
                    </div>
                    <div style="grid-column:1 / -1;">
                        <div class="small" style="margin-bottom:8px; font-weight:600; color:#475569;">Catatan / Alasan Koreksi</div>
                        <input type="text" name="notes" class="modal-input" placeholder="Contoh: Barang rusak, hilang, perhitungan ulang dsb.">
                    </div>
                </div>
                
                <div style="display:flex; justify-content:flex-end; gap:12px; margin-top:30px; padding-top:20px; border-top:1px solid var(--line);">
                    <a href="<?= $closeUrl ?>" class="modern-btn" style="background:#f1f5f9; color:#475569;">Batal</a>
                    <button type="submit" class="modern-btn" style="box-shadow: 0 4px 14px rgba(215, 25, 32, 0.2); background: linear-gradient(135deg, var(--red), #b91c1c); border: none; color: white;">Simpan Opname</button>
                </div>
            </form>
        </div>

        <div id="tab-history" class="tab-content">
            <div style="border: 1px solid #e2e8f0; border-radius: 12px; overflow-x:auto;">
                <table style="width:100%; border-collapse:collapse; min-width:500px;">
                    <thead style="background:#f8fafc;">
                        <tr>
                            <th style="padding:12px 16px; text-align:left; font-size:12px; color:#64748b; text-transform:uppercase; border-bottom:1px solid #e2e8f0;">Tanggal & Ket</th>
                            <th style="padding:12px 16px; text-align:right; font-size:12px; color:#64748b; text-transform:uppercase; border-bottom:1px solid #e2e8f0;">Analisa</th>
                            <th style="padding:12px 16px; text-align:right; font-size:12px; color:#64748b; text-transform:uppercase; border-bottom:1px solid #e2e8f0;">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (!empty($selectedHistory)): ?>
                            <?php foreach ($selectedHistory as $index => $row): ?>
                                <?php
                                $historySmallQty = max(1, (int) ($row['small_unit_qty'] ?? 1));
                                $beforeDisplay = format_stock_breakdown((int) ($row['before_stock'] ?? 0), (string) ($row['unit_large'] ?? 'Bungkus'), (string) ($row['unit_small'] ?? 'Pcs'), $historySmallQty);
                                $actualDisplay = format_stock_breakdown((int) ($row['actual_stock'] ?? 0), (string) ($row['unit_large'] ?? 'Bungkus'), (string) ($row['unit_small'] ?? 'Pcs'), $historySmallQty);
                                $adj = (float)($row['adjustment'] ?? 0);
                                $isLatestHistory = $index === 0;
                                ?>
                                <tr style="border-bottom:1px solid #e2e8f0;">
                                    <td style="padding:16px;">
                                        <strong style="color:#0f172a; font-size:14px;"><?= htmlspecialchars((string) ($row['transaction_date'] ?? '-')) ?></strong>
                                        <div style="margin-top:4px; font-size:13px; color:#475569;"><?= htmlspecialchars((string) (($row['notes'] ?? '') !== '' ? $row['notes'] : 'Koreksi Stok')) ?></div>
                                        <div style="margin-top:4px; font-size:12px; color:#64748b; background:#f1f5f9; display:inline-block; padding:4px 8px; border-radius:6px;">Sistem: <strong><?= htmlspecialchars($beforeDisplay) ?></strong> &rarr; Aktual: <strong><?= htmlspecialchars($actualDisplay) ?></strong></div>
                                    </td>
                                    <td style="padding:16px; text-align:right; vertical-align:middle;">
                                        <?php if ($adj > 0): ?>
                                            <span style="display:inline-block; padding:4px 10px; border-radius:999px; font-size:11px; font-weight:700; background:#ecfdf5; color:#059669; border:1px solid #a7f3d0;">LEBIH (+<?= format_qty($adj) ?>)</span>
                                        <?php else: ?>
                                            <span style="display:inline-block; padding:4px 10px; border-radius:999px; font-size:11px; font-weight:700; background:#fef2f2; color:#dc2626; border:1px solid #fecaca;">KURANG (<?= format_qty($adj) ?>)</span>
                                        <?php endif; ?>
                                    </td>
                                    <td style="padding:16px; text-align:right; vertical-align:middle;">
                                        <?php if ($isLatestHistory): ?>
                                            <form method="post" action="index.php?route=stok/opname" class="opname-delete-form" onsubmit="event.preventDefault(); const f = this; askConfirmation('Hapus history stok opname ini? Stok akan dikembalikan ke nilai sebelum koreksi.', () => f.submit());">
                                                <input type="hidden" name="action" value="delete_opname">
                                                <input type="hidden" name="item_id" value="<?= (int) ($selectedItem['id'] ?? 0) ?>">
                                                <input type="hidden" name="opname_id" value="<?= (int) ($row['id'] ?? 0) ?>">
                                                <button type="submit" style="padding:6px 12px; font-size:12px; font-weight:600; border-radius:8px; background:#fef2f2; color:#dc2626; border:1px solid #fecaca; cursor:pointer;">Hapus</button>
                                            </form>
                                        <?php else: ?>
                                            <span style="font-size:11px; color:#94a3b8; font-weight:600; padding:6px 12px; background:#f8fafc; border-radius:8px; border:1px solid #e2e8f0;">TERKUNCI</span>
                                        <?php endif; ?>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="3" style="text-align:center; padding: 40px; color: #64748b; font-size:14px;">Belum ada riwayat koreksi.</td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
            
            <div style="display:flex; justify-content:flex-end; margin-top:20px; padding-top:20px; border-top:1px solid var(--line);">
                <a href="<?= $closeUrl ?>" class="modern-btn" style="background:#f1f5f9; color:#475569;">Tutup</a>
            </div>
        </div>
    </div>
</div>

<script>
    function switchTab(tabId, event) {
        document.querySelectorAll('.modal-tab').forEach(t => t.classList.remove('active'));
        document.querySelectorAll('.tab-content').forEach(c => c.classList.remove('active'));
        
        if (event) {
            event.currentTarget.classList.add('active');
        }
        document.getElementById('tab-' + tabId).classList.add('active');
    }
</script>
<?php endif; ?>