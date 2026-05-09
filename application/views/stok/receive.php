<?php
$currentPage = $currentPage ?? 1;
$totalPages = $totalPages ?? 1;
?>
<style>
    .receive-layout {
        display: grid;
        grid-template-columns: 1fr 380px;
        gap: 24px;
        margin-top: 20px;
    }

    .receive-main {
        display: flex;
        flex-direction: column;
        gap: 20px;
    }

    .receive-card {
        background: #fff;
        border-radius: 20px;
        border: 1px solid #eaecf0;
        box-shadow: 0 4px 20px rgba(0, 0, 0, 0.03);
        overflow: hidden;
    }

    .receive-card-head {
        padding: 16px 20px;
        background: #fcfcfd;
        border-bottom: 1px solid #eaecf0;
        display: flex;
        align-items: center;
        justify-content: space-between;
    }

    .receive-card-body {
        padding: 24px;
    }

    .receive-info-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
        gap: 16px;
        margin-bottom: 24px;
    }

    .info-stat {
        background: #f8fafc;
        border: 1px solid #e2e8f0;
        border-radius: 16px;
        padding: 16px;
        display: flex;
        flex-direction: column;
        gap: 4px;
    }

    .info-stat.highlight {
        background: #f0f9ff;
        border-color: #bae6fd;
    }

    .info-stat .label {
        font-size: 11px;
        font-weight: 800;
        text-transform: uppercase;
        letter-spacing: 0.05em;
        color: #64748b;
    }

    .info-stat .value {
        font-size: 18px;
        font-weight: 700;
        color: #1e293b;
    }

    .receive-form-group {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 20px;
        padding: 24px;
        background: #fff;
        border-radius: 16px;
        border: 1px solid #eaecf0;
    }

    .input-box {
        display: flex;
        flex-direction: column;
        gap: 8px;
    }

    .input-box label {
        font-weight: 700;
        font-size: 13px;
        color: #344054;
    }

    .input-with-icon {
        position: relative;
        display: flex;
        align-items: center;
    }

    .input-with-icon .icon {
        position: absolute;
        left: 14px;
        font-size: 18px;
        color: #98a2b3;
    }

    .input-with-icon input {
        padding-left: 44px !important;
        height: 52px;
        font-size: 16px;
        font-weight: 600;
    }

    .receive-summary-bar {
        background: #101828;
        border-radius: 16px;
        padding: 20px 24px;
        display: flex;
        align-items: center;
        justify-content: space-between;
        color: #fff;
        margin-top: 24px;
    }

    .summary-item .label {
        font-size: 11px;
        color: #98a2b3;
        font-weight: 800;
        text-transform: uppercase;
        letter-spacing: 0.05em;
    }

    .summary-item .value {
        font-size: 20px;
        font-weight: 700;
        color: #fff;
    }

    .summary-item .value-large {
        font-size: 28px;
        font-weight: 800;
        color: #3b82f6;
    }

    .item-search-wrap {
        position: sticky;
        top: 20px;
    }

    .mini-item-card {
        padding: 12px;
        border-radius: 12px;
        border: 1px solid #eaecf0;
        margin-bottom: 10px;
        transition: all 0.2s;
        cursor: pointer;
        display: flex;
        align-items: center;
        gap: 12px;
        background: #fff;
    }

    .mini-item-card:hover {
        border-color: #3b82f6;
        background: #f0f9ff;
    }

    .mini-item-card.active {
        border-color: #3b82f6;
        background: #eff6ff;
        box-shadow: 0 0 0 4px rgba(59, 130, 246, 0.1);
    }

    .mini-item-card .item-icon {
        width: 40px;
        height: 40px;
        background: #f2f4f7;
        border-radius: 10px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 20px;
    }

    .mini-item-card.active .item-icon {
        background: #3b82f6;
        color: #fff;
    }

    .history-strip {
        display: flex;
        align-items: center;
        gap: 12px;
        padding: 10px 14px;
        border-radius: 10px;
        background: #f9fafb;
        border: 1px solid #eaecf0;
        margin-bottom: 8px;
        font-size: 13px;
    }

    @media (max-width: 1100px) {
        .receive-layout {
            grid-template-columns: 1fr;
        }
        .item-search-wrap {
            position: static;
        }
    }
</style>

<div class="receive-layout">
    <div class="receive-main">
        <?php if (!empty($selectedItem)): ?>
            <?php
            $selectedSmallQty = max(1, (int) ($selectedItem['small_unit_qty'] ?? 1));
            $selectedStockDisplay = format_stock_breakdown((int) ($selectedItem['stock'] ?? 0), (string) ($selectedItem['unit_large'] ?? 'Bungkus'), (string) ($selectedItem['unit_small'] ?? 'Pcs'), $selectedSmallQty);
            ?>
            
            <div class="receive-card">
                <div class="receive-card-head">
                    <div style="display:flex; align-items:center; gap:12px;">
                        <span style="font-size:24px;">📦</span>
                        <div>
                            <h3 style="margin:0;">Input Pembelian Baru</h3>
                            <div class="small" style="color:#667085;">Silakan masukkan detail struk pembelian barang.</div>
                        </div>
                    </div>
                    <button type="button" class="btn btn-secondary" onclick="toggleHistoryModal(true)">Lihat History</button>
                </div>
                
                <div class="receive-card-body">
                    <div class="receive-info-grid">
                        <div class="info-stat">
                            <span class="label">Barang Terpilih</span>
                            <span class="value"><?= htmlspecialchars((string) ($selectedItem['name'] ?? '-')) ?></span>
                            <div class="small" style="margin-top:2px; color:#64748b;"><?= htmlspecialchars((string) ($selectedItem['code'] ?? '-')) ?></div>
                        </div>
                        <div class="info-stat">
                            <span class="label">Stok Saat Ini</span>
                            <span class="value"><?= htmlspecialchars($selectedStockDisplay) ?></span>
                        </div>
                        <div class="info-stat highlight">
                            <span class="label">Harga Beli Terakhir</span>
                            <span class="value"><?= rupiah((float) ($selectedItem['purchase_price'] ?? 0)) ?></span>
                            <div class="small" style="color:#0369a1;">per <?= htmlspecialchars((string) ($selectedItem['unit_large'] ?? 'Unit')) ?></div>
                        </div>
                    </div>

                    <form method="post" id="receive-form">
                        <input type="hidden" name="item_id" value="<?= (int) $selectedItem['id'] ?>">
                        <div class="receive-form-group">
                            <div class="input-box">
                                <label>Jumlah Masuk (<?= htmlspecialchars((string) ($selectedItem['unit_large'] ?? 'Besar')) ?>)</label>
                                <div class="input-with-icon">
                                    <span class="icon">📦</span>
                                    <input type="number" name="qty_large" id="input_qty_large" min="0" value="0" required inputmode="numeric" autofocus oninput="updateSummary()">
                                </div>
                            </div>
                            <div class="input-box">
                                <label>Bonus/Ecer (<?= htmlspecialchars((string) ($selectedItem['unit_small'] ?? 'Kecil')) ?>)</label>
                                <div class="input-with-icon">
                                    <span class="icon">🍬</span>
                                    <input type="number" name="qty_small" id="input_qty_small" min="0" value="0" required inputmode="numeric" oninput="updateSummary()">
                                </div>
                            </div>
                            <div class="input-box">
                                <label>Total Harga di Nota (Untuk Semua Qty)</label>
                                <div class="input-with-icon">
                                    <span class="icon">💰</span>
                                    <input type="text" class="money-input" name="purchase_price" id="input_purchase_price" placeholder="Ketik total harga nota..." required inputmode="numeric" oninput="updateSummary()">
                                </div>
                                <div class="small" style="color:#667085; font-style:italic;">* Masukkan total bayar untuk barang ini di nota.</div>
                            </div>
                            <div class="input-box">
                                <label>Harga Modal Satuan Kecil (Otomatis)</label>
                                <div class="input-with-icon">
                                    <span class="icon">🏷️</span>
                                    <input type="text" id="display_modal_ecer" placeholder="Otomatis..." readonly style="background:#f8fafc; border-color:#e2e8f0; color:#475569;">
                                </div>
                                <div class="small" style="color:#64748b;">Harga modal per 1 <?= htmlspecialchars((string) ($selectedItem['unit_small'] ?? 'Pcs')) ?>.</div>
                            </div>
                        </div>

                        <div class="receive-summary-bar" id="summary-bar">
                            <div class="summary-item">
                                <div class="label">Analisa Harga Satuan</div>
                                <div class="value" id="summary_unit_price">Rp 0</div>
                                <div class="small" style="color:#94a3b8; margin-top:4px;" id="summary_modal_ecer_text">Modal Ecer: Rp 0</div>
                            </div>
                            <div style="width:1px; height:48px; background:rgba(255,255,255,0.1);"></div>
                            <div class="summary-item" style="text-align:right;">
                                <div class="label">Total Yang Dibayar</div>
                                <div class="value-large" id="summary_total_price">Rp 0</div>
                                <div class="small" style="color:#94a3b8; margin-top:4px;" id="summary_total_volume">0 volume</div>
                            </div>
                        </div>

                        <div style="margin-top:24px; display:flex; gap:12px;">
                            <button type="submit" class="btn btn-primary" style="flex:1; height:60px; font-size:20px; border-radius:16px; box-shadow:0 12px 20px -5px rgba(59, 130, 246, 0.3);">Konfirmasi & Simpan</button>
                        </div>
                    </form>
                </div>
            </div>

        <?php else: ?>
            <div class="receive-card" style="text-align:center; padding:60px 20px;">
                <span style="font-size:60px; display:block; margin-bottom:20px;">🔍</span>
                <h3>Belum Ada Barang Dipilih</h3>
                <p style="color:#667085;">Silakan pilih barang dari daftar di samping kanan atau cari melalui barcode.</p>
            </div>
        <?php endif; ?>
    </div>

    <div class="item-search-wrap">
        <div class="receive-card">
            <div class="receive-card-head">
                <h4 style="margin:0;">Cari & Pilih Barang</h4>
            </div>
            <div class="receive-card-body" style="padding:16px;">
                <form method="get" style="margin-bottom:20px;">
                    <input type="hidden" name="route" value="stok/receive">
                    <div class="input-with-icon">
                        <span class="icon">🔍</span>
                        <input type="text" name="q" value="<?= htmlspecialchars((string) ($keyword ?? '')) ?>" placeholder="Cari nama / barcode..." style="height:44px; border-radius:10px;">
                    </div>
                </form>

                <div style="display:flex; flex-direction:column; gap:8px;">
                    <?php foreach (($items ?? []) as $item): ?>
                        <?php
                        $isLowStock = !empty($item['low_stock']);
                        $isSelected = (int) ($selectedItem['id'] ?? 0) === (int) ($item['id'] ?? 0);
                        $pickQuery = ['route' => 'stok/receive', 'item' => (int) $item['id'], 'p' => $currentPage];
                        if (!empty($keyword)) $pickQuery['q'] = $keyword;
                        ?>
                        <div class="mini-item-card <?= $isSelected ? 'active' : '' ?>" onclick="window.location.href='index.php?<?= http_build_query($pickQuery) ?>'">
                            <div class="item-icon"><?= $isLowStock ? '⚠️' : '📦' ?></div>
                            <div style="flex:1; min-width:0;">
                                <div style="font-weight:700; color:#101828; white-space:nowrap; overflow:hidden; text-overflow:ellipsis;"><?= htmlspecialchars((string) ($item['name'] ?? '-')) ?></div>
                                <div class="small" style="color:#667085;">Stok: <?= htmlspecialchars((string) ($item['stock_display'] ?? '0')) ?></div>
                            </div>
                            <span style="font-size:12px; color:#98a2b3;">➔</span>
                        </div>
                    <?php endforeach; ?>
                </div>

                <?php if ($totalPages > 1): ?>
                    <div style="margin-top:16px; display:flex; justify-content:center; gap:10px;">
                        <?php
                        $prevQuery = ['route' => 'stok/receive', 'p' => $currentPage - 1];
                        if (!empty($keyword)) $prevQuery['q'] = $keyword;
                        if (!empty($selectedItem['id'])) $prevQuery['item'] = $selectedItem['id'];
                        $nextQuery = ['route' => 'stok/receive', 'p' => $currentPage + 1];
                        if (!empty($keyword)) $nextQuery['q'] = $keyword;
                        if (!empty($selectedItem['id'])) $nextQuery['item'] = $selectedItem['id'];
                        ?>
                        <?php if ($currentPage > 1): ?>
                            <a href="index.php?<?= http_build_query($prevQuery) ?>" class="btn btn-secondary" style="padding:6px 12px; font-size:12px;">Prev</a>
                        <?php endif; ?>
                        <span class="small" style="align-self:center;"><?= $currentPage ?> / <?= $totalPages ?></span>
                        <?php if ($currentPage < $totalPages): ?>
                            <a href="index.php?<?= http_build_query($nextQuery) ?>" class="btn btn-secondary" style="padding:6px 12px; font-size:12px;">Next</a>
                        <?php endif; ?>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<?php if (!empty($selectedItem)): ?>
<div class="modal-backdrop" id="history-modal">
    <div class="modal" style="width: min(900px, 100%);">
        <div class="modal-head">
            <h3 style="margin:0;">History Receive: <?= htmlspecialchars((string) $selectedItem['name']) ?></h3>
            <button type="button" class="modal-close" onclick="toggleHistoryModal(false)">Tutup</button>
        </div>
        <div class="card" style="padding: 0; overflow: hidden; border: 1px solid #eaecf0; border-radius: 12px;">
                <div class="bca-ledger-wrap">
                    <table class="bca-ledger">
                        <thead>
                            <tr>
                                <th>Tanggal</th>
                                <th>Keterangan</th>
                                <th style="text-align:right;">Analisa Harga</th>
                                <th style="text-align:right;">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (!empty($selectedHistory)): ?>
                                <?php foreach ($selectedHistory as $index => $row): ?>
                                    <?php
                                    $currentPrice = (float)($row['purchase_price'] ?? 0);
                                    $prevPrice = isset($selectedHistory[$index + 1]) ? (float)$selectedHistory[$index + 1]['purchase_price'] : $currentPrice;
                                    $priceDiff = $currentPrice - $prevPrice;
                                    ?>
                                    <tr>
                                        <td class="date" data-label="Tanggal"><?= htmlspecialchars((string) ($row['transaction_date'] ?? '-')) ?></td>
                                        <td class="desc" data-label="Keterangan">
                                            <span class="desc-main"><?= htmlspecialchars((string) (($row['notes'] ?? '') !== '' ? $row['notes'] : 'Pembelian Baru')) ?></span>
                                            <span class="desc-sub">Masuk: <?= (int)$row['qty_large'] ?> <?= htmlspecialchars((string) ($row['unit_large'] ?? '')) ?></span>
                                            <span class="desc-sub">Hrg: <?= number_format($currentPrice, 0, ',', '.') ?> | Total: <?= number_format((float) ($row['purchase_total'] ?? 0), 0, ',', '.') ?></span>
                                        </td>
                                        <td class="amount" data-label="Analisa">
                                            <?php if ($priceDiff > 0): ?>
                                                <span class="type-label type-db">NAIK (+<?= number_format($priceDiff, 0, ',', '.') ?>)</span>
                                            <?php elseif ($priceDiff < 0): ?>
                                                <span class="type-label type-cr">TURUN (<?= number_format($priceDiff, 0, ',', '.') ?>)</span>
                                            <?php else: ?>
                                                <span class="small" style="font-size:10px; font-weight:800; color:#98a2b3;">TETAP</span>
                                            <?php endif; ?>
                                        </td>
                                        <td style="text-align:right;" data-label="Aksi">
                                            <form method="post" onsubmit="event.preventDefault(); const f = this; askConfirmation('Hapus history receive ini? Stok barang akan dikurangi kembali.', () => f.submit());">
                                                <input type="hidden" name="action" value="delete_receive">
                                                <input type="hidden" name="item_id" value="<?= (int) ($selectedItem['id'] ?? 0) ?>">
                                                <input type="hidden" name="receive_id" value="<?= (int) ($row['id'] ?? 0) ?>">
                                                <button type="submit" class="btn btn-danger" style="padding: 6px 10px; font-size: 11px;">Hapus</button>
                                            </form>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <tr>
                                    <td colspan="4" style="text-align:center; padding: 40px; color: #667085;">Belum ada riwayat receive.</td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
        </div>
    </div>
</div>
<?php endif; ?>

<script>
    function updateSummary() {
        const qtyLargeInput = document.getElementById('input_qty_large');
        if (!qtyLargeInput) return;

        const qtyLarge = parseInt(qtyLargeInput.value || '0', 10);
        const qtySmallInput = document.getElementById('input_qty_small');
        const qtySmall = qtySmallInput ? parseInt(qtySmallInput.value || '0', 10) : 0;
        const priceInput = document.getElementById('input_purchase_price');
        const priceText = priceInput ? priceInput.value : '0';
        const totalPrice = parseFloat(priceText.replace(/[^\d]/g, '')) || 0;
        
        const unitLarge = "<?= htmlspecialchars((string) ($selectedItem['unit_large'] ?? 'Unit')) ?>";
        const unitSmall = "<?= htmlspecialchars((string) ($selectedItem['unit_small'] ?? 'Pcs')) ?>";
        const smallPerLarge = <?= (int) ($selectedItem['small_unit_qty'] ?? 1) ?>;
        
        const totalVolumeLarge = qtyLarge + (qtySmall / smallPerLarge);
        const unitPriceLarge = totalVolumeLarge > 0 ? Math.round(totalPrice / totalVolumeLarge) : 0;
        const modalEcer = smallPerLarge > 0 ? Math.round(unitPriceLarge / smallPerLarge) : 0;

        // Update Modal Ecer Display (Field)
        const ecerDisplay = document.getElementById('display_modal_ecer');
        if (ecerDisplay) ecerDisplay.value = modalEcer.toLocaleString('id-ID');

        // Update Summary Bar
        const summaryUnitPrice = document.getElementById('summary_unit_price');
        if (summaryUnitPrice) summaryUnitPrice.textContent = 'Rp ' + unitPriceLarge.toLocaleString('id-ID') + ' / ' + unitLarge;

        const summaryModalEcerText = document.getElementById('summary_modal_ecer_text');
        if (summaryModalEcerText) summaryModalEcerText.textContent = 'Modal Ecer: Rp ' + modalEcer.toLocaleString('id-ID') + ' / ' + unitSmall;

        const summaryTotalPrice = document.getElementById('summary_total_price');
        if (summaryTotalPrice) summaryTotalPrice.textContent = 'Rp ' + totalPrice.toLocaleString('id-ID');

        const summaryTotalVolume = document.getElementById('summary_total_volume');
        if (summaryTotalVolume) {
            let volText = qtyLarge + ' ' + unitLarge;
            if (qtySmall > 0) volText += ' + ' + qtySmall + ' ' + unitSmall;
            summaryTotalVolume.textContent = volText;
        }
    }

    function toggleHistoryModal(show) {
        const modal = document.getElementById('history-modal');
        if (modal) modal.classList.toggle('active', show);
    }

    document.querySelectorAll('.money-input').forEach(function(input) {
        input.addEventListener('input', function() {
            const digits = this.value.replace(/[^\d]/g, '');
            this.value = digits === '' ? '' : Number(digits).toLocaleString('id-ID');
        });
    });

    // Run initial summary
    updateSummary();
</script>