<?php
$currentPage = $currentPage ?? 1;
$totalPages = $totalPages ?? 1;
?>
<style>
    .barang-modal {
        width: min(1180px, 100%);
        padding: 28px;
    }

    .barang-form {
        display: grid;
        gap: 18px;
    }

    .barang-hero {
        background: linear-gradient(135deg, #fffaf0, #fffdf8);
        border: 1px solid #f2dfb2;
        border-radius: 18px;
        padding: 18px;
    }

    .barang-two-col {
        display: grid;
        grid-template-columns: 1.1fr .9fr;
        gap: 18px;
    }

    .barang-section {
        background: #fff;
        border: 1px solid var(--line);
        border-radius: 20px;
        padding: 20px;
        box-shadow: 0 10px 24px rgba(28, 39, 60, .04);
    }

    .barang-section .section-title {
        margin-bottom: 12px;
    }

    .barang-section .form-grid {
        gap: 14px;
    }

    .barang-section input[readonly] {
        background: #f8fafc;
        color: #344054;
    }

    .barang-section .info-strip {
        border-radius: 16px;
    }

    .barang-preview {
        background: linear-gradient(135deg, #fff6dc, #fffdf4);
        border: 1px solid #f3d68a;
    }

    .detail-box.highlight-price {
        background: linear-gradient(135deg, #eff8ff, #f8fbff);
        border-color: #b2ddff;
    }

    .detail-box.highlight-profit {
        background: linear-gradient(135deg, #ecfdf3, #f6fff9);
        border-color: #a6f4c5;
    }

    .barang-option {
        background: #fcfcfd;
        border: 1px solid var(--line);
        border-radius: 16px;
        padding: 14px 16px;
    }

    .barang-footer {
        display: flex;
        gap: 10px;
        justify-content: flex-end;
        padding-top: 4px;
    }

    .barang-footer .btn {
        width: auto;
        min-width: 140px;
    }

    .promo-stack {
        display: grid;
        grid-template-columns: repeat(2, minmax(0, 1fr));
        gap: 12px;
    }

    .promo-panel {
        height: 100%;
    }

    .promo-panel .promo-stack {
        height: 100%;
        align-content: start;
    }

    .barang-search {
        display: grid;
        grid-template-columns: minmax(0, 1fr) auto auto;
        gap: 12px;
        margin-bottom: 16px;
        align-items: end;
    }

    .barang-search-actions {
        display: flex;
        gap: 12px;
        align-items: center;
    }

    .barang-search .search-btn,
    .barang-search .reset-btn {
        min-width: 120px;
        height: 44px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        border-radius: 12px;
        text-align: center;
        text-decoration: none;
    }

    .barang-table-wrap {
        overflow-x: auto;
    }

    .barang-list-table {
        width: 100%;
    }

    .stock-input-focus {
        border-color: #f3d68a !important;
        background: #fff8db !important;
        box-shadow: 0 0 0 3px rgba(243, 214, 138, .25);
    }

    .stock-input-focus.adjust {
        border-color: #84cc16 !important;
        background: #f7fee7 !important;
        box-shadow: 0 0 0 3px rgba(132, 204, 22, .18);
    }

    @media (max-width: 920px) {
        .barang-modal {
            width: min(100%, 100%);
            padding: 20px;
        }

        .barang-two-col {
            grid-template-columns: 1fr;
        }

        .promo-stack {
            grid-template-columns: 1fr;
        }

        .barang-search {
            grid-template-columns: 1fr;
        }

        .barang-search-actions {
            width: 100%;
        }

        .barang-search .search-btn,
        .barang-search .reset-btn {
            flex: 1 1 0;
        }
    }

    @media (max-width: 640px) {
        .toolbar>div:first-child {
            width: 100%;
        }

        .barang-section,
        .barang-hero {
            padding: 14px;
            border-radius: 16px;
        }

        .barang-footer {
            flex-direction: column-reverse;
        }

        .barang-footer .btn {
            width: 100%;
            min-width: 0;
        }

        .barang-search-actions {
            display: grid;
            grid-template-columns: 1fr 1fr;
        }

        .barang-search .search-btn,
        .barang-search .reset-btn {
            width: 100%;
            min-width: 0;
        }

        .barang-modal {
            padding: 14px;
        }

        .barang-section .action-row .btn,
        .barang-section .action-row button {
            flex: 1 1 100%;
        }

        .barang-list-table thead {
            display: none;
        }

        .barang-list-table,
        .barang-list-table tbody,
        .barang-list-table tr,
        .barang-list-table td {
            display: block;
            width: 100%;
        }

        .barang-list-table tr {
            padding: 12px 0;
            border-bottom: 1px solid var(--line);
        }

        .barang-list-table tr:last-child {
            border-bottom: none;
        }

        .barang-list-table td {
            border-bottom: none;
            padding: 8px 0;
        }

        .barang-list-table td::before {
            content: attr(data-label);
            display: block;
            margin-bottom: 4px;
            font-size: 11px;
            font-weight: 800;
            letter-spacing: .05em;
            text-transform: uppercase;
            color: #98a2b3;
        }

        .barang-list-table td .small {
            line-height: 1.45;
        }

        .barang-modal .modal-head h3 {
            font-size: 18px;
            line-height: 1.35;
        }

        .barang-section .form-grid {
            grid-template-columns: 1fr !important;
        }

        .barang-search .search-btn,
        .barang-search .reset-btn {
            min-height: 44px;
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
        text-decoration: none;
    }

    .btn-pagination:hover:not(:disabled) {
        transform: translateY(-1px);
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.05);
    }
</style>
<div class="toolbar">
    <div>
        <div class="section-title">Master Barang</div>
        <div class="small">Master barang fokus untuk nama, barcode, konversi satuan, harga, promo, dan aturan jual.</div>
    </div>
    <button type="button" class="btn" onclick="toggleBarangModal(true)">Add Barang</button>
</div>

<div class="card" style="margin-top:18px;">
    <div style="display:flex; justify-content:space-between; gap:12px; align-items:flex-start; margin-bottom:14px; flex-wrap:wrap;">
        <div>
            <h3 style="margin-bottom:6px;">Daftar Barang</h3>
            <div class="small">Barang bisa diatur apakah boleh dijual eceran dan setengah atau tidak.</div>
        </div>
        <div class="badge">Total Item: <?= count($items ?? []) ?></div>
    </div>

    <form method="get" class="barang-search">
        <input type="hidden" name="route" value="barang">
        <div>
            <div class="small">Cari Barang</div>
            <input type="text" name="q" value="<?= htmlspecialchars((string) ($keyword ?? '')) ?>" placeholder="Cari kode, barcode, nama, atau kategori">
        </div>
        <div class="barang-search-actions">
            <button type="submit" class="btn btn-secondary search-btn">Search</button>
            <a href="index.php?route=barang" class="btn btn-info reset-btn">Reset</a>
        </div>
    </form>

    <div class="barang-table-wrap">
        <table class="barang-list-table">
            <thead>
                <tr>
                    <th>Kode</th>
                    <th>Barang</th>
                    <th>Stok</th>
                    <th>Aturan Jual</th>
                    <th>Untung</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($items ?? [] as $item): ?>
                    <tr>
                        <td data-label="Kode">
                            <strong><?= htmlspecialchars($item['code']) ?></strong>
                            <div class="small"><?= htmlspecialchars((string) (($item['barcode'] ?? '') !== '' ? $item['barcode'] : '-')) ?></div>
                            <div class="small"><?= htmlspecialchars((string) ($item['category'] ?: '-')) ?></div>
                        </td>
                        <td data-label="Barang">
                            <strong><?= htmlspecialchars($item['name']) ?></strong>
                            <div class="small">1 <?= htmlspecialchars((string) ($item['unit_large'] ?? 'Bungkus')) ?> = <?= (int) ($item['small_unit_qty'] ?? 1) ?> <?= htmlspecialchars((string) ($item['unit_small'] ?? 'Batang')) ?></div>
                        </td>
                        <td data-label="Stok"><?= htmlspecialchars((string) ($item['stock_display'] ?? format_qty((float) $item['stock']))) ?></td>
                        <td data-label="Aturan Jual">
                            <div class="small">Besar: <?= rupiah((float) $item['selling_price']) ?></div>
                            <div class="small">Eceran: <?= !empty($item['allow_small_sale']) ? rupiah((float) $item['unit_price']) : 'Tidak dijual' ?></div>
                        </td>
                        <td data-label="Untung">
                            <strong><?= rupiah((float) ($item['active_profit_value'] ?? 0)) ?></strong>
                            <div class="small">per <?= htmlspecialchars((string) ($item['active_profit_unit'] ?? 'Batang')) ?></div>
                        </td>
                        <td data-label="Aksi">
                            <div class="action-row">
                                <a class="btn btn-info" href="index.php?route=barang<?= !empty($keyword) ? '&q=' . urlencode((string) $keyword) : '' ?>&view=<?= (int) $item['id'] ?>">View</a>
                                <a class="btn btn-secondary" href="index.php?route=barang<?= !empty($keyword) ? '&q=' . urlencode((string) $keyword) : '' ?>&edit=<?= (int) $item['id'] ?>">Edit</a>
                                <form method="post" onsubmit="event.preventDefault(); askConfirmation('Hapus barang ini?', () => this.submit());" style="margin:0;">
                                    <input type="hidden" name="action" value="delete">
                                    <input type="hidden" name="id" value="<?= (int) $item['id'] ?>">
                                    <button type="submit" class="btn btn-danger">Delete</button>
                                </form>
                            </div>
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
                $prevQuery = ['route' => 'barang', 'p' => $currentPage - 1];
                if (!empty($keyword)) $prevQuery['q'] = $keyword;

                $nextQuery = ['route' => 'barang', 'p' => $currentPage + 1];
                if (!empty($keyword)) $nextQuery['q'] = $keyword;
                ?>

                <?php if ($currentPage > 1): ?>
                    <a href="index.php?<?= http_build_query($prevQuery) ?>" class="btn btn-secondary btn-pagination">
                        Prev
                    </a>
                <?php endif; ?>

                <?php if ($currentPage < $totalPages): ?>
                    <a href="index.php?<?= http_build_query($nextQuery) ?>" class="btn btn-secondary btn-pagination">
                        Next
                    </a>
                <?php endif; ?>
            </div>
        </div>
    <?php endif; ?>
</div>

<div class="modal-backdrop <?= !empty($editItem) ? 'active' : '' ?>" id="barang-modal">
    <div class="modal barang-modal">
        <div class="modal-head">
            <div>
                <div class="section-title"><?= !empty($editItem) ? 'Edit Barang' : 'Add Barang' ?></div>
                <h3 style="margin:0;"><?= !empty($editItem) ? 'Form barang' : 'Tambah barang baru' ?></h3>
            </div>
            <button type="button" class="modal-close" onclick="toggleBarangModal(false)">Tutup</button>
        </div>

        <form method="post" class="barang-form">
            <input type="hidden" name="action" value="save">
            <input type="hidden" name="id" value="<?= htmlspecialchars((string) ($editItem['id'] ?? '')) ?>">
            <input type="hidden" name="code" value="<?= htmlspecialchars((string) ($nextCode ?? '')) ?>">
            <input type="hidden" name="update_purchase" id="update_purchase" value="<?= empty($editItem) ? '1' : '0' ?>">

            <div class="barang-hero">
                <div class="form-grid">
                    <div>
                        <div class="small">Kode Barang</div>
                        <input value="<?= htmlspecialchars((string) ($nextCode ?? '')) ?>" readonly>
                    </div>
                    <div>
                        <div class="small">Barcode</div>
                        <input name="barcode" inputmode="numeric" autocomplete="off" placeholder="Barcode produk" value="<?= htmlspecialchars((string) ($editItem['barcode'] ?? '')) ?>">
                    </div>
                    <div>
                        <div class="small">Kategori</div>
                        <select name="category">
                            <?php
                            $categories = [
                                'Staple Foods',
                                'Food & Beverage',
                                'Condiments & Spices',
                                'Personal Care',
                                'Household Supplies',
                                'Tobacco',
                                'Toys & Games',
                                'Healthcare',
                                'Stationery',
                                'Utilities',
                                'Etc'
                            ];
                            $currentCategory = $editItem['category'] ?? '';
                            ?>
                            <option value="">- Pilih Kategori -</option>
                            <?php foreach ($categories as $cat): ?>
                                <option value="<?= htmlspecialchars($cat) ?>" <?= $currentCategory === $cat ? 'selected' : '' ?>><?= htmlspecialchars($cat) ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div style="grid-column:1 / -1;">
                        <div class="small">Nama Barang</div>
                        <input name="name" inputmode="text" autocomplete="off" placeholder="Nama barang" value="<?= htmlspecialchars((string) ($editItem['name'] ?? '')) ?>" required>
                    </div>
                </div>
            </div>

            <div class="barang-two-col">
                <div class="barang-section">
                    <div class="section-title">Satuan Dan Ringkasan Stok</div>
                    <div class="form-grid">
                        <div>
                            <div class="small">Satuan Besar</div><input id="unit_large" name="unit_large" value="<?= htmlspecialchars((string) ($editItem['unit_large'] ?? 'Bungkus')) ?>" required>
                        </div>
                        <div>
                            <div class="small">Satuan Kecil</div><input id="unit_small" name="unit_small" value="<?= htmlspecialchars((string) ($editItem['unit_small'] ?? 'Batang')) ?>" required>
                        </div>
                        <div>
                            <div class="small">Qty Satuan/Item</div><input id="small_unit_qty" name="small_unit_qty" type="number" min="1" value="<?= htmlspecialchars((string) ($editItem['small_unit_qty'] ?? '1')) ?>">
                        </div>
                        <div>
                            <div class="small">Stock</div><input id="stock_display_readonly" value="<?= htmlspecialchars((string) ($editItem['stock_display'] ?? '0')) ?>" readonly>
                        </div>
                        <div>
                            <div class="small">Total Stok Kecil</div><input id="stock_total" type="number" min="0" value="<?= htmlspecialchars((string) ($editItem['stock'] ?? '0')) ?>" readonly style="background:#f8fafc;">
                        </div>
                    </div>
                    <div class="info-strip" style="margin-top:14px;">
                        <div id="stock-preview" style="font-weight:700;">Stok aktif: <?= htmlspecialchars((string) ($editItem['stock_display'] ?? '0')) ?></div>
                        <div class="small" id="stock-total-note" style="margin-top:6px;">Perubahan stok dilakukan dari modul Receive Item dan Stok Opname.</div>
                    </div>
                </div>

                <div class="barang-section">
                    <div class="section-title">Harga</div>
                    <?php if (!empty($editItem)): ?>
                        <div class="barang-option" style="margin-bottom:14px;">
                            <label style="display:flex; align-items:center; gap:10px;">
                                <input type="checkbox" id="update_purchase_toggle" value="1" style="width:auto;">
                                <span>Buka mode pembelian baru untuk mengganti Harga Beli di Struk</span>
                            </label>
                        </div>
                    <?php endif; ?>
                    <div class="form-grid">
                        <div>
                            <div class="small">Harga Beli di Struk</div>
                            <input class="money-input" id="purchase_receipt_total" name="purchase_receipt_total" type="text" value="<?= htmlspecialchars(number_format((float) ($editItem['purchase_price'] ?? 0), 0, ',', '.')) ?>" <?= !empty($editItem) ? 'readonly' : '' ?>>
                        </div>
                        <div>
                            <div class="small">Qty Yang Dibeli</div><input id="purchase_large_qty" name="purchase_large_qty" type="number" min="0" value="<?= htmlspecialchars((string) ($editItem['purchase_basis_large'] ?? '0')) ?>" <?= !empty($editItem) ? 'readonly' : '' ?>>
                        </div>
                        <div>
                            <div class="small">Total Harga Beli</div><input id="purchase_total_display" type="text" value="<?= htmlspecialchars(number_format((float) ($editItem['purchase_total'] ?? 0), 0, ',', '.')) ?>" readonly style="background:#f8fafc;">
                        </div>
                        <div>
                            <div class="small">Harga per pcs</div><input id="purchase_price" name="purchase_price" type="text" value="<?= htmlspecialchars(number_format((float) ($editItem['purchase_price'] ?? 0), 0, ',', '.')) ?>" readonly style="background:#f8fafc;">
                        </div>
                        <div>
                            <div class="small">Harga Jual</div><input class="money-input" id="selling_price" name="selling_price" type="text" value="<?= htmlspecialchars(number_format((float) ($editItem['selling_price'] ?? 0), 0, ',', '.')) ?>">
                        </div>
                        <div>
                            <div class="small">Harga Modal Ecer</div><input id="unit_cost_display" type="text" value="0" readonly style="background:#f8fafc;">
                        </div>
                        <div>
                            <div class="small">Harga Ecer</div>
                            <input id="unit_price" class="money-input" name="unit_price" type="text" value="<?= htmlspecialchars(number_format((float) ($editItem['unit_price'] ?? 0), 0, ',', '.')) ?>">
                        </div>
                    </div>
                    <input type="hidden" id="half_price" name="half_price" value="<?= htmlspecialchars(number_format((float) ($editItem['half_price'] ?? 0), 0, ',', '.')) ?>">
                    <div class="info-strip barang-preview" style="margin-top:14px;">
                        <div id="purchase-preview" style="font-weight:700;">Harga per pcs: Rp 0 / 0 = Rp 0</div>
                        <div id="price-preview" style="font-weight:700; margin-top:6px;">Harga Modal Ecer: Rp 0 / 0 = Rp 0</div>
                        <div class="small" id="profit-note" style="margin-top:6px;">Profit (satuan Besar): Rp 0 - Rp 0 = Rp 0</div>
                        <div class="small" id="profit-small-note" style="margin-top:6px;">Profit (satuan Kecil): Rp 0 - Rp 0 = Rp 0</div>
                    </div>
                </div>
            </div>

            <div class="barang-two-col">
                <div class="barang-section promo-panel">
                    <div class="section-title">Promo</div>
                    <div class="small" style="margin-bottom:10px;">Sediakan sampai 6 promo untuk penjualan eceran.</div>
                    <div class="promo-stack">
                        <?php for ($i = 1; $i <= 6; $i++): ?>
                            <div class="detail-box">
                                <div class="form-grid">
                                    <div>
                                        <div class="small">Promo <?= $i ?> Qty</div><input id="promo_qty_<?= $i ?>" name="promo_qty_<?= $i ?>" type="number" min="0" value="<?= htmlspecialchars((string) ($editItem['promo_qty_' . $i] ?? '0')) ?>">
                                    </div>
                                    <div>
                                        <div class="small">Promo <?= $i ?> Harga</div><input id="promo_price_<?= $i ?>" name="promo_price_<?= $i ?>" class="money-input" type="text" value="<?= htmlspecialchars(number_format((float) ($editItem['promo_price_' . $i] ?? 0), 0, ',', '.')) ?>">
                                    </div>
                                </div>
                                <div class="small" id="promo-note-<?= $i ?>" style="margin-top:8px;">Promo <?= $i ?> belum diisi.</div>
                            </div>
                        <?php endfor; ?>
                    </div>
                </div>
                <div class="barang-section">
                    <div class="section-title">Promo</div>
                    <div class="detail-box">
                        <div class="small">Promo aktif akan dipakai otomatis saat qty transaksi cocok. Promo termurah yang valid diprioritaskan lebih dulu.</div>
                    </div>
                    <div class="info-strip" style="margin-top:12px;">
                        <div class="small" id="promo-best-note">Belum ada promo yang aktif.</div>
                    </div>
                </div>
            </div>

            <div class="barang-section">
                <div class="section-title">Aturan Jual</div>
                <input type="hidden" name="allow_half_sale" value="<?= !empty($editItem['allow_half_sale']) ? '1' : '0' ?>">
                <div class="barang-option">
                    <label style="display:flex; align-items:center; gap:10px;">
                        <input type="checkbox" name="allow_small_sale" value="1" style="width:auto;" <?= !empty($editItem['allow_small_sale']) || empty($editItem) ? 'checked' : '' ?>>
                        <span>Barang ini boleh dijual eceran / satuan kecil</span>
                    </label>
                </div>
                <div class="small" style="margin-top:10px;">Kalau tidak dicentang, opsi itu tidak muncul di transaksi.</div>
            </div>

            <div class="barang-footer">
                <button type="button" class="btn btn-secondary" onclick="toggleBarangModal(false)">Batal</button>
                <button type="submit" class="btn">Simpan Barang</button>
            </div>
        </form>
    </div>
</div>

<?php $barangBaseUrl = 'index.php?route=barang' . (!empty($keyword) ? '&q=' . urlencode((string) $keyword) : ''); ?>

<div class="modal-backdrop <?= !empty($viewItem) ? 'active' : '' ?>" id="barang-view-modal">
    <div class="modal" style="width:min(720px, 100%);">
        <div class="modal-head">
            <div>
                <div class="section-title">Detail Barang</div>
                <h3 style="margin:0;"><?= htmlspecialchars((string) ($viewItem['name'] ?? '-')) ?></h3>
            </div>
            <a href="<?= htmlspecialchars($barangBaseUrl) ?>" class="modal-close">Tutup</a>
        </div>

        <?php if (!empty($viewItem)): ?>
            <div class="detail-grid">
                <div class="detail-box">
                    <div class="small">Kode</div><strong><?= htmlspecialchars((string) $viewItem['code']) ?></strong>
                </div>
                <div class="detail-box">
                    <div class="small">Barcode</div><strong><?= htmlspecialchars((string) (($viewItem['barcode'] ?? '') !== '' ? $viewItem['barcode'] : '-')) ?></strong>
                </div>
                <div class="detail-box">
                    <div class="small">Kategori</div><strong><?= htmlspecialchars((string) ($viewItem['category'] ?: '-')) ?></strong>
                </div>
                <div class="detail-box">
                    <div class="small">Stok</div><strong><?= htmlspecialchars((string) ($viewItem['stock_display'] ?? '-')) ?></strong>
                </div>
                <div class="detail-box">
                    <div class="small">Konversi</div><strong>1 <?= htmlspecialchars((string) ($viewItem['unit_large'] ?? 'Bungkus')) ?> = <?= (int) ($viewItem['small_unit_qty'] ?? 1) ?> <?= htmlspecialchars((string) ($viewItem['unit_small'] ?? 'Batang')) ?></strong>
                </div>
                <div class="detail-box">
                    <div class="small">Eceran</div><strong><?= !empty($viewItem['allow_small_sale']) ? 'Ya' : 'Tidak' ?></strong>
                </div>
                <div class="detail-box">
                    <div class="small">Harga Beli di Struk</div><strong><?= rupiah((float) ($viewItem['purchase_price'] ?? 0)) ?></strong>
                </div>
                <div class="detail-box">
                    <div class="small">Harga Modal Satuan Kecil</div><strong><?= rupiah((float) ($viewItem['cost_per_small'] ?? 0)) ?></strong>
                </div>
                <div class="detail-box highlight-price">
                    <div class="small">Harga Ecer</div><strong><?= rupiah((float) ($viewItem['unit_price'] ?? 0)) ?></strong>
                </div>
                <div class="detail-box highlight-profit">
                    <div class="small">Untung</div>
                    <strong><?= rupiah((float) ($viewItem['active_profit_value'] ?? 0)) ?></strong>
                    <div class="small" style="margin-top:4px;">per <?= htmlspecialchars((string) ($viewItem['active_profit_unit'] ?? '-')) ?></div>
                </div>
            </div>

            <div class="action-row" style="margin-top:16px;">
                <a class="btn btn-secondary" href="<?= htmlspecialchars($barangBaseUrl . '&edit=' . (int) $viewItem['id']) ?>">Edit</a>
                <form method="post" onsubmit="event.preventDefault(); askConfirmation('Hapus barang ini?', () => this.submit());" style="margin:0;">
                    <input type="hidden" name="action" value="delete">
                    <input type="hidden" name="id" value="<?= (int) $viewItem['id'] ?>">
                    <button type="submit" class="btn btn-danger">Delete</button>
                </form>
            </div>
        <?php endif; ?>
    </div>
</div>

<script>
    function toggleBarangModal(show) {
        document.getElementById('barang-modal').classList.toggle('active', show);
        if (!show) {
            window.location.href = <?= json_encode($barangBaseUrl) ?>;
        }
    }

    (function() {
        const updatePurchaseInput = document.getElementById('update_purchase');
        const updatePurchaseToggle = document.getElementById('update_purchase_toggle');
        const purchaseReceiptField = document.getElementById('purchase_receipt_total');
        const purchaseQtyField = document.getElementById('purchase_large_qty');
        const initialPurchaseReceiptValue = purchaseReceiptField ? purchaseReceiptField.value : '';
        const initialPurchaseQtyValue = purchaseQtyField ? purchaseQtyField.value : '';

        function rupiah(value) {
            return 'Rp ' + Math.round(value || 0).toLocaleString('id-ID');
        }

        function formatNumber(value) {
            const digits = String(value || '').replace(/[^\d]/g, '');
            return digits === '' ? '' : Number(digits).toLocaleString('id-ID');
        }

        function getMoneyValue(id) {
            const node = document.getElementById(id);
            return node ? (parseFloat((node.value || '0').replace(/[^\d]/g, '')) || 0) : 0;
        }

        function renderPromoNote(index, unitSmall) {
            const qty = Math.max(0, parseInt(document.getElementById('promo_qty_' + index).value || '0', 10));
            const price = getMoneyValue('promo_price_' + index);
            const note = document.getElementById('promo-note-' + index);
            note.textContent = qty > 0 && price > 0 ? ('Promo ' + index + ': ' + qty + ' ' + unitSmall + ' = ' + rupiah(price)) : ('Promo ' + index + ' belum diisi.');
            const summary = document.getElementById('promo-summary-' + index);
            if (summary) {
                summary.textContent = qty > 0 && price > 0 ?
                    ('Promo ' + index + ': beli ' + qty + ' ' + unitSmall + ' bayar ' + rupiah(price)) :
                    ('Promo ' + index + ': belum aktif.');
            }
        }

        function recalc(fromTotal) {
            const purchaseReceiptTotal = getMoneyValue('purchase_receipt_total');
            const selling = getMoneyValue('selling_price');
            const allowSmallSaleField = document.querySelector('input[name="allow_small_sale"]');
            const allowSmallSale = !!(allowSmallSaleField && allowSmallSaleField.checked);
            const smallQty = Math.max(1, parseInt(document.getElementById('small_unit_qty').value || '1', 10));
            const unitLarge = document.getElementById('unit_large').value || 'Bungkus';
            const unitSmall = document.getElementById('unit_small').value || 'Batang';
            const stockTotalInput = document.getElementById('stock_total');
            const purchaseLargeQtyInput = document.getElementById('purchase_large_qty');
            const unitCostDisplay = document.getElementById('unit_cost_display');
            const stockDisplayReadonly = document.getElementById('stock_display_readonly');
            const purchaseTotalDisplay = document.getElementById('purchase_total_display');
            const purchaseLargeQty = Math.max(0, parseInt((purchaseLargeQtyInput ? purchaseLargeQtyInput.value : '0') || '0', 10));
            const unitPriceValue = getMoneyValue('unit_price');
            const purchasePerPcs = purchaseReceiptTotal;
            const totalHargaBeli = purchaseReceiptTotal * purchaseLargeQty;
            const modalEcer = smallQty > 0 ? Math.round(purchasePerPcs / smallQty) : 0;
            const profitPerSmall = allowSmallSale ? (unitPriceValue - modalEcer) : 0;
            const profitPerLarge = selling - purchasePerPcs;
            const previewStockTotal = Math.max(0, parseInt((stockTotalInput ? stockTotalInput.value : '0') || '0', 10));

            if (purchaseTotalDisplay) {
                purchaseTotalDisplay.value = formatNumber(totalHargaBeli);
            }
            const purchasePriceInput = document.getElementById('purchase_price');
            if (purchasePriceInput) {
                purchasePriceInput.value = formatNumber(purchasePerPcs);
            }
            if (unitCostDisplay) {
                unitCostDisplay.value = formatNumber(modalEcer);
            }
            if (stockDisplayReadonly) {
                stockDisplayReadonly.value = stockDisplayReadonly.value || '';
            }
            document.getElementById('stock-preview').textContent = 'Stok aktif: ' + (stockDisplayReadonly ? stockDisplayReadonly.value : (previewStockTotal + ' ' + unitSmall));
            document.getElementById('stock-total-note').textContent = 'Perubahan stok dilakukan dari modul Receive Item dan Stok Opname.';
            document.getElementById('purchase-preview').textContent = 'Total Harga Beli: ' + rupiah(purchaseReceiptTotal) + ' x ' + purchaseLargeQty + ' = ' + rupiah(totalHargaBeli);
            document.getElementById('price-preview').textContent = allowSmallSale ?
                ('Harga Modal Ecer: ' + rupiah(purchasePerPcs) + ' / ' + smallQty + ' = ' + rupiah(modalEcer)) :
                'Harga Modal Ecer: Tidak dipakai karena barang tidak dijual ecer';
            document.getElementById('profit-note').textContent = 'Profit (satuan Besar): ' + rupiah(selling) + ' - ' + rupiah(purchasePerPcs) + ' = ' + rupiah(profitPerLarge);
            document.getElementById('profit-small-note').textContent = allowSmallSale ?
                ('Profit (satuan Kecil): ' + rupiah(unitPriceValue) + ' - ' + rupiah(modalEcer) + ' = ' + rupiah(profitPerSmall)) :
                'Profit (satuan Kecil): Tidak dipakai karena barang tidak dijual ecer';
            [1, 2, 3, 4, 5, 6].forEach(function(index) {
                renderPromoNote(index, unitSmall);
            });
            const activePromos = [1, 2, 3, 4, 5, 6].map(function(index) {
                return {
                    index: index,
                    qty: Math.max(0, parseInt(document.getElementById('promo_qty_' + index).value || '0', 10)),
                    price: getMoneyValue('promo_price_' + index)
                };
            }).filter(function(promo) {
                return promo.qty > 0 && promo.price > 0;
            }).sort(function(a, b) {
                return a.price - b.price;
            });
            document.getElementById('promo-best-note').textContent = activePromos.length > 0 ?
                ('Promo termurah saat ini: Promo ' + activePromos[0].index + ' dengan harga ' + rupiah(activePromos[0].price)) :
                'Belum ada promo yang aktif.';
        }

        function setPurchaseLockState(isUnlocked) {
            if (updatePurchaseInput) {
                updatePurchaseInput.value = isUnlocked ? '1' : '0';
            }

            [purchaseReceiptField, purchaseQtyField].forEach(function(field) {
                if (!field) {
                    return;
                }
                field.readOnly = !isUnlocked;
            });

            if (isUnlocked) {
                if (purchaseReceiptField) {
                    purchaseReceiptField.value = '';
                }
                if (purchaseQtyField) {
                    purchaseQtyField.value = '0';
                }
            } else {
                if (purchaseReceiptField) {
                    purchaseReceiptField.value = initialPurchaseReceiptValue;
                }
                if (purchaseQtyField) {
                    purchaseQtyField.value = initialPurchaseQtyValue;
                }
            }
        }

        document.querySelectorAll('.money-input').forEach(function(el) {
            el.addEventListener('input', function() {
                this.value = formatNumber(this.value);
                recalc(true);
            });
        });

        document.querySelectorAll('#small_unit_qty, #unit_large, #unit_small, #purchase_large_qty, #promo_qty_1, #promo_qty_2, #promo_qty_3, #promo_qty_4, #promo_qty_5, #promo_qty_6').forEach(function(el) {
            el.addEventListener('input', function() {
                recalc(false);
            });
        });
        document.querySelectorAll('#purchase_price, #selling_price, #unit_price, #promo_price_1, #promo_price_2, #promo_price_3, #promo_price_4, #promo_price_5, #promo_price_6').forEach(function(el) {
            el.addEventListener('input', function() {
                recalc(false);
            });
        });
        document.querySelectorAll('input[name="allow_small_sale"]').forEach(function(el) {
            el.addEventListener('change', function() {
                recalc(false);
            });
        });

        if (updatePurchaseToggle) {
            updatePurchaseToggle.addEventListener('change', function() {
                setPurchaseLockState(this.checked);
                recalc(true);
            });
            setPurchaseLockState(updatePurchaseToggle.checked);
        }

        recalc(true);
    }());
</script>