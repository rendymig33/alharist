<?php
$cart = $cart ?? [];
$vaults = $vaults ?? [];
$items = $items ?? [];
$customers = $customers ?? [];
$subtotal = array_sum(array_column($cart, 'line_total'));
$profit = array_sum(array_column($cart, 'line_profit'));
$transactionMode = $transactionMode ?? 'biasa';
?>
<style>
    .transaction-shell {
        padding: 0;
        overflow: hidden;
    }

    .transaction-card-table td::before,
    .latest-sales-table td::before {
        display: none;
        content: attr(data-label);
        font-size: 11px;
        font-weight: 800;
        letter-spacing: .04em;
        text-transform: uppercase;
        color: #8a5a00;
        margin-bottom: 4px;
    }

    .transaction-head-grid {
        background: #111;
        color: #fff;
        display: grid;
        grid-template-columns: 1.2fr .8fr .8fr .8fr .8fr;
        gap: 0;
        font-size: 13px;
    }

    .transaction-main-grid {
        display: grid;
        grid-template-columns: 1.4fr .6fr;
        gap: 0;
    }

    .transaction-add-grid {
        display: grid;
        grid-template-columns: 1fr auto auto;
        gap: 10px;
        margin-top: 8px;
    }

    .transaction-table-wrap,
    .transaction-list-wrap,
    .item-modal-table-wrap {
        overflow-x: auto;
    }

    .transaction-payment-panel {
        padding: 16px;
        background: #fafafa;
    }

    .item-modal-dialog {
        width: min(1100px, calc(100vw - 32px));
        max-width: 1100px;
    }

    .item-modal-search {
        margin-top: 10px;
    }

    .scan-btn {
        min-width: 140px;
    }

    .scan-btn[disabled] {
        opacity: .55;
        cursor: not-allowed;
        box-shadow: none;
        transform: none;
    }

    .scan-status {
        min-height: 18px;
        margin-top: 8px;
    }

    .scanner-panel {
        margin-top: 12px;
        padding: 12px;
        border: 1px solid #d7deea;
        border-radius: 16px;
        background: #f7faff;
        display: none;
        gap: 10px;
    }

    .scanner-panel.active {
        display: grid;
    }

    .scanner-video {
        width: 100%;
        border-radius: 14px;
        background: #111;
        aspect-ratio: 16 / 10;
        object-fit: cover;
    }

    .item-grid {
        display: grid;
        grid-template-columns: repeat(3, minmax(240px, 1fr));
        gap: 16px;
        margin-top: 14px;
        align-items: stretch;
    }

    .item-card {
        border: 1px solid #d7deea;
        border-radius: 22px;
        padding: 16px;
        background: linear-gradient(180deg, #ffffff, #f7faff);
        box-shadow: 0 14px 30px rgba(28, 39, 60, .08);
        display: grid;
        gap: 12px;
        align-content: start;
        min-width: 0;
    }

    .item-card-head {
        display: grid;
        gap: 6px;
    }

    .item-card-code {
        font-size: 12px;
        font-weight: 800;
        letter-spacing: .04em;
        color: #5f6b85;
    }

    .item-card-name {
        font-size: 16px;
        font-weight: 800;
        color: #1f2a44;
        line-height: 1.3;
    }

    .item-card-meta {
        display: grid;
        gap: 8px;
    }

    .item-stock-badge {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        padding: 6px 10px;
        border-radius: 999px;
        background: #eff5ff;
        color: #184e9e;
        font-weight: 700;
        font-size: 12px;
    }

    .item-price-box {
        display: inline-flex;
        flex-direction: column;
        gap: 4px;
        padding: 10px 12px;
        border-radius: 12px;
        background: #fff8db;
        color: #8a5a00;
        min-width: 120px;
    }

    .item-price-box strong {
        color: #252525;
        font-size: 16px;
    }

    .purchase-form {
        display: grid;
        grid-template-columns: 110px 70px auto;
        gap: 8px;
        align-items: start;
    }

    .transaction-summary-box {
        background: #fff;
        border: 1px solid #e2e4ea;
        border-radius: 16px;
        padding: 18px;
    }

    .transaction-payment-grid {
        display: grid;
        gap: 12px;
    }

    .transaction-mobile-stack {
        display: none;
    }

    @media (max-width: 920px) {

        .transaction-head-grid,
        .transaction-main-grid {
            grid-template-columns: 1fr;
        }
    }

    @media (max-width: 640px) {
        .transaction-add-grid {
            grid-template-columns: 1fr;
        }

        .transaction-main-grid {
            gap: 0;
        }

        .transaction-head-grid>div {
            border-right: none !important;
            border-bottom: 1px solid #333;
        }

        .transaction-head-grid>div:last-child {
            border-bottom: none;
        }

        .transaction-main-grid>div:first-child {
            border-right: none !important;
            border-bottom: 1px solid #e2e4ea;
        }

        .transaction-payment-panel {
            padding: 14px;
        }

        .transaction-summary-box {
            padding: 14px;
        }

        .transaction-payment-grid {
            gap: 10px;
        }

        .transaction-payment-panel [style*="font-size:54px"] {
            font-size: 34px !important;
            text-align: left !important;
            margin: 10px 0 14px !important;
        }

        .transaction-payment-panel [style*="font-size:34px"] {
            font-size: 24px !important;
            height: 54px !important;
        }

        .purchase-form {
            grid-template-columns: 1fr !important;
        }

        .item-modal-dialog {
            width: calc(100vw - 20px);
        }

        .item-grid {
            grid-template-columns: repeat(2, minmax(0, 1fr));
            gap: 10px;
        }

        .item-price-box {
            width: 100%;
        }

        .transaction-table-wrap table,
        .transaction-table-wrap thead,
        .transaction-table-wrap tbody,
        .transaction-table-wrap tr,
        .transaction-table-wrap th,
        .transaction-table-wrap td,
        .transaction-list-wrap table,
        .transaction-list-wrap thead,
        .transaction-list-wrap tbody,
        .transaction-list-wrap tr,
        .transaction-list-wrap th,
        .transaction-list-wrap td {
            display: block;
            width: 100%;
        }

        .transaction-table-wrap thead,
        .transaction-list-wrap thead {
            display: none;
        }

        .transaction-table-wrap tbody,
        .transaction-list-wrap tbody {
            display: grid;
            gap: 12px;
        }

        .transaction-table-wrap tr,
        .transaction-list-wrap tr {
            background: #fff;
            border: 1px solid #e2e4ea;
            border-radius: 16px;
            padding: 14px;
            box-shadow: 0 10px 24px rgba(28, 39, 60, .06);
        }

        .transaction-table-wrap td,
        .transaction-list-wrap td {
            border: none;
            padding: 0;
            margin-bottom: 10px;
        }

        .transaction-table-wrap td:last-child,
        .transaction-list-wrap td:last-child {
            margin-bottom: 0;
        }

        .transaction-card-table td::before,
        .latest-sales-table td::before {
            display: block;
        }

        .transaction-mobile-stack {
            display: grid;
            gap: 10px;
        }

        .transaction-mobile-actions {
            display: grid;
            gap: 10px;
        }

        .transaction-list-wrap form .btn-secondary,
        .transaction-table-wrap form .btn-secondary,
        .transaction-table-wrap form select,
        .transaction-table-wrap form button {
            min-width: 60px;
            width: 100%;
        }

        .transaction-card-index {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            min-width: 34px;
            height: 34px;
            border-radius: 999px;
            background: #fff4cc;
            color: #8a5a00;
            font-weight: 800;
        }

        .transaction-inline-total {
            display: flex;
            justify-content: space-between;
            align-items: baseline;
            gap: 12px;
            padding: 10px 12px;
            border-radius: 12px;
            background: #fff8db;
            color: #8a5a00;
        }

        .transaction-inline-total strong {
            font-size: 18px;
            color: #252525;
        }
    }

    @media (max-width: 420px) {
        .item-grid {
            grid-template-columns: repeat(2, minmax(0, 1fr));
            gap: 8px;
        }

        .item-card {
            padding: 12px;
            border-radius: 18px;
            gap: 10px;
        }

        .item-card-name {
            font-size: 14px;
        }

        .item-stock-badge {
            padding: 5px 8px;
            font-size: 11px;
        }

        .item-price-box {
            min-width: 0;
            padding: 8px 10px;
        }

        .item-price-box strong {
            font-size: 14px;
        }
    }
</style>
<div class="card transaction-shell">
    <div class="transaction-head-grid">
        <div id="header-customer-label" style="padding:10px 14px; border-right:1px solid #333;">Customer: UMUM</div>
        <div style="padding:10px 14px; border-right:1px solid #333;">Salesman:</div>
        <div id="header-payment-label" style="padding:10px 14px; border-right:1px solid #333;">Termin: Tunai</div>
        <div style="padding:10px 14px; border-right:1px solid #333;">Tanggal: <?= date('d M Y') ?></div>
        <div style="padding:10px 14px;">No Penjualan: <?= htmlspecialchars((string) ($nextInvoiceNo ?? 'AUTO')) ?></div>
    </div>
    <div style="padding:10px 14px; background:#fff8db; color:#8a5a00; font-size:13px; font-weight:700;">Kategori Pembiayaan: Konsumsi Pribadi</div>
    <div style="padding:12px 14px; border-bottom:1px solid #e2e4ea; background:#fff;">
        <form method="post" style="display:grid; grid-template-columns:220px auto; gap:12px; align-items:end;">
            <input type="hidden" name="action" value="set_mode">
            <div>
                <div class="small">Mode Transaksi</div>
                <select name="transaction_mode" onchange="this.form.submit()">
                    <option value="biasa" <?= $transactionMode === 'biasa' ? 'selected' : '' ?>>Transaksi Biasa</option>
                    <option value="esaldo" <?= $transactionMode === 'esaldo' ? 'selected' : '' ?>>E-Transaction</option>
                </select>
            </div>
            <div class="small" style="align-self:end; padding-bottom:12px;">
                <?= $transactionMode === 'esaldo' ? 'Mode E-Transaction aktif. Modal dan harga jual diisi manual saat tambah item.' : 'Mode transaksi barang biasa aktif.' ?>
            </div>
        </form>
    </div>
    <div class="transaction-main-grid">
        <div style="padding:16px; border-right:1px solid #e2e4ea;">
            <div class="small" style="font-weight:700; color:#d27a00;"><?= $transactionMode === 'esaldo' ? 'TAMBAH E-TRANSACTION' : 'TAMBAH ITEM' ?></div>
            <div class="transaction-add-grid">
                <input id="open-item-modal" type="text" inputmode="numeric" autocomplete="off" placeholder="<?= $transactionMode === 'esaldo' ? 'Cari nama produk E-Saldo lalu Enter' : 'Scan / input barcode lalu Enter' ?>" style="background:#fff;">
                <button type="button" class="btn" onclick="toggleItemModal(true)"><?= $transactionMode === 'esaldo' ? 'Pilih E-Saldo' : 'Pilih Barang' ?></button>
                <?php if ($transactionMode === 'biasa'): ?>
                    <button type="button" class="btn btn-info scan-btn" id="open-scanner-btn">Scan Barcode</button>
                <?php else: ?>
                    <button type="button" class="btn btn-info scan-btn" disabled>Input Manual</button>
                <?php endif; ?>
            </div>
            <div class="small scan-status" id="scan-status"></div>

            <div class="transaction-table-wrap">
                <table class="transaction-card-table" style="margin-top:14px;">
                    <thead>
                        <tr>
                            <th>No</th>
                            <th>Nama Item</th>
                            <th>Qty</th>
                            <th>Satuan</th>
                            <th>Harga</th>
                            <th>Subtotal</th>
                            <th>Masuk Ke</th>
                            <th></th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($cart as $index => $row): ?>
                            <tr>
                                <td data-label="No"><span class="transaction-card-index"><?= $index + 1 ?></span></td>
                                <td data-label="Nama Item">
                                    <?= htmlspecialchars($row['name']) ?>
                                    <?php if (!empty($row['promo_label'])): ?>
                                        <div class="small" style="margin-top:4px; color:#b54708;"><?= htmlspecialchars((string) $row['promo_label']) ?></div>
                                    <?php endif; ?>
                                </td>
                                <td data-label="Qty"><?= format_qty((float) ($row['display_qty'] ?? $row['qty'])) ?></td>
                                <td data-label="Satuan"><?= htmlspecialchars((string) ($row['purchase_label'] ?? ($row['stock_display'] ?? ''))) ?></td>
                                <td data-label="Harga"><?= rupiah((float) $row['selling_price']) ?></td>
                                <td data-label="Subtotal">
                                    <div class="transaction-inline-total">
                                        <span>Subtotal</span>
                                        <strong><?= rupiah((float) $row['line_total']) ?></strong>
                                    </div>
                                </td>
                                <td data-label="Masuk Ke" style="min-width:220px;">
                                    <form method="post">
                                        <input type="hidden" name="transaction_mode" value="<?= htmlspecialchars((string) $transactionMode) ?>">
                                        <input type="hidden" name="action" value="update_item_vault">
                                        <input type="hidden" name="index" value="<?= (int) $index ?>">
                                        <select name="vault_id" onchange="this.form.submit()">
                                            <option value="0">Pilih Dana / Berangkas</option>
                                            <?php foreach ($vaults as $vault): ?>
                                                <option value="<?= (int) $vault['id'] ?>" <?= (int) ($row['vault_id'] ?? 0) === (int) $vault['id'] ? 'selected' : '' ?>>
                                                    <?= htmlspecialchars((string) ($vault['bank_name'] ?: 'Vault')) ?>
                                                </option>
                                            <?php endforeach; ?>
                                        </select>
                                    </form>
                                </td>
                                <td data-label="Aksi">
                                    <form method="post">
                                        <input type="hidden" name="transaction_mode" value="<?= htmlspecialchars((string) $transactionMode) ?>">
                                        <input type="hidden" name="action" value="remove_item">
                                        <input type="hidden" name="index" value="<?= (int) $index ?>">
                                        <button class="btn-secondary" type="submit">X</button>
                                    </form>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
        <div class="transaction-payment-panel">
            <div class="transaction-summary-box">
                <div class="small">Total</div>
                <div style="font-size:54px; font-weight:700; text-align:right; margin:12px 0 18px;"><?= rupiah($subtotal) ?></div>
                <div class="transaction-payment-grid">
                    <div class="small">Uang Bayar</div>
                    <input type="text" id="cash_paid_display" inputmode="numeric" autocomplete="off" placeholder="0" style="font-size:34px; font-weight:700; text-align:right; height:70px;">
                    <input type="hidden" id="cash_paid" name="cash_paid" form="checkout-form">
                    <div class="small">Kembalian</div>
                    <input type="text" id="change_amount" placeholder="0" readonly style="font-size:34px; font-weight:700; text-align:right; height:70px; background:#fff;">
                    <button type="button" class="btn-green" id="confirm-checkout">BAYAR</button>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="card" style="margin-top:18px;">
    <form method="post" id="checkout-form">
        <input type="hidden" name="action" value="checkout">
        <input type="hidden" name="transaction_mode" value="<?= htmlspecialchars((string) $transactionMode) ?>">
        <div class="form-grid">
            <div>
                <div class="small">Pembayaran</div>
                <select name="payment_type" id="payment_type" required>
                    <option value="Tunai">Tunai</option>
                    <option value="QRIS">QRIS</option>
                    <option value="Hutang">Hutang</option>
                    <option value="Prive">Prive</option>
                </select>
            </div>
            <div id="customer-field" style="display:none;">
                <div class="small">Pelanggan</div>
                <select name="customer_id" id="customer_id">
                    <option value="0">Pilih Pelanggan</option>
                    <?php foreach ($customers as $customer): ?>
                        <option value="<?= (int) $customer['id'] ?>"><?= htmlspecialchars((string) $customer['name']) ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div id="due-date-field" style="display:none;">
                <div class="small">Jatuh Tempo</div>
                <input type="date" name="due_date" id="due_date">
            </div>
        </div>
    </form>
</div>

<div class="modal-backdrop" id="item-modal">
    <div class="modal item-modal-dialog">
        <div class="modal-head">
            <h3 style="margin:0;"><?= $transactionMode === 'esaldo' ? 'Pilih E-Saldo' : 'Pilih Barang' ?></h3>
            <button type="button" class="modal-close" onclick="toggleItemModal(false)">Tutup</button>
        </div>
        <input class="item-modal-search" id="item-search" type="text" inputmode="text" autocomplete="off" placeholder="<?= $transactionMode === 'esaldo' ? 'Cari nama atau kode E-Saldo' : 'Cari kode / nama barang / barcode' ?>">
        <?php if ($transactionMode === 'biasa'): ?>
            <div class="scanner-panel" id="scanner-panel">
                <video id="scanner-video" class="scanner-video" autoplay muted playsinline></video>
                <div class="small">Arahkan kamera ke barcode produk. Jika barcode cocok, barang akan langsung dipilih.</div>
                <button type="button" class="btn btn-secondary" id="close-scanner-btn">Tutup Scanner</button>
            </div>
        <?php endif; ?>
        <div class="item-modal-table-wrap">
            <div class="item-grid">
                <?php if ($transactionMode === 'biasa'): ?>
                    <?php foreach ($items as $item): ?>
                        <div class="item-row item-card" data-search="<?= htmlspecialchars(strtolower(trim(($item['code'] ?? '') . ' ' . ($item['barcode'] ?? '') . ' ' . ($item['name'] ?? '')))) ?>" data-barcode="<?= htmlspecialchars((string) ($item['barcode'] ?? '')) ?>">
                            <div class="item-card-head">
                                <div class="item-card-code"><?= htmlspecialchars($item['code']) ?></div>
                                <?php if (!empty($item['barcode'])): ?>
                                    <div class="small">Barcode: <?= htmlspecialchars((string) $item['barcode']) ?></div>
                                <?php endif; ?>
                                <div class="item-card-name">
                                    <?= htmlspecialchars($item['name']) ?>
                                </div>
                                <?php for ($i = 1; $i <= 3; $i++): ?>
                                    <?php $promoQty = (int) ($item['promo_qty_' . $i] ?? 0); ?>
                                    <?php $promoPrice = (float) ($item['promo_price_' . $i] ?? 0); ?>
                                    <?php if ($promoQty > 0 && $promoPrice > 0): ?>
                                        <div class="small" style="margin-top:4px; color:#b54708;">Promo <?= $promoQty ?> <?= htmlspecialchars((string) $item['unit_small']) ?> = <?= rupiah($promoPrice) ?></div>
                                    <?php endif; ?>
                                <?php endfor; ?>
                            </div>
                            <div class="item-card-meta">
                                <div><span class="item-stock-badge"><?= htmlspecialchars((string) ($item['stock_display'] ?? format_qty((float) $item['stock']))) ?></span></div>
                                <div class="item-price-box">
                                    <span class="small" style="color:#8a5a00;">Harga aktif</span>
                                    <strong class="sale-price" data-base-price="<?= htmlspecialchars((string) $item['selling_price']) ?>" data-unit-price="<?= htmlspecialchars((string) $item['unit_price']) ?>" data-half-price="<?= htmlspecialchars((string) ($item['half_price'] ?? 0)) ?>"><?= rupiah((float) $item['selling_price']) ?></strong>
                                </div>
                            </div>
                            <div>
                                <form method="post" class="purchase-form">
                                    <input type="hidden" name="transaction_mode" value="biasa">
                                    <input type="hidden" name="action" value="add_item">
                                    <input type="hidden" name="item_id" value="<?= (int) $item['id'] ?>">
                                    <select name="purchase_mode" class="purchase-mode">
                                        <option value="besar"><?= htmlspecialchars((string) $item['unit_large']) ?></option>
                                        <?php if (!empty($item['allow_small_sale'])): ?>
                                            <option value="eceran">1 <?= htmlspecialchars((string) $item['unit_small']) ?></option>
                                        <?php endif; ?>
                                        <?php if (!empty($item['allow_half_sale'])): ?>
                                            <option value="setengah">Setengah</option>
                                        <?php endif; ?>
                                    </select>
                                    <input type="number" name="qty" value="1" min="1" inputmode="numeric">
                                    <button type="submit">Tambah</button>
                                </form>
                            </div>
                        </div>
                    <?php endforeach; ?>
                <?php else: ?>
                    <?php foreach (($esaldoItems ?? []) as $item): ?>
                        <div class="item-row item-card" data-search="<?= htmlspecialchars(strtolower(trim(($item['code'] ?? '') . ' ' . ($item['name'] ?? '') . ' ' . ($item['description'] ?? '')))) ?>" data-barcode="">
                            <div class="item-card-head">
                                <div class="item-card-code"><?= htmlspecialchars((string) $item['code']) ?></div>
                                <div class="item-card-name"><?= htmlspecialchars((string) $item['name']) ?></div>
                                <div class="small">Saldo: <?= rupiah((float) ($item['selling_price'] ?? 0)) ?></div>
                            </div>
                            <div>
                                <form method="post" class="purchase-form" style="grid-template-columns:1fr 1fr;">
                                    <input type="hidden" name="transaction_mode" value="esaldo">
                                    <input type="hidden" name="action" value="add_esaldo">
                                    <input type="hidden" name="item_id" value="<?= (int) $item['id'] ?>">
                                    <input type="text" name="manual_buy_price" class="money-inline" placeholder="Masukkan Modal" value="">
                                    <input type="text" name="manual_sell_price" class="money-inline" placeholder="Masukkan Harga Jual" value="">
                                    <button type="submit">Tambah</button>
                                </form>
                            </div>
                        </div>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<script>
    function toggleItemModal(show) {
        document.getElementById('item-modal').classList.toggle('active', show);
        if (show) {
            const search = document.getElementById('item-search');
            search.focus();
            search.select();
        } else if (typeof stopBarcodeScanner === 'function') {
            stopBarcodeScanner();
        }
    }

    (function() {
        function rupiah(value) {
            return 'Rp ' + Math.round(value || 0).toLocaleString('id-ID');
        }

        let scannerStream = null;
        let scannerTimer = null;
        let barcodeDetector = null;
        const supportsCameraScan = !!(window.isSecureContext && 'mediaDevices' in navigator && 'BarcodeDetector' in window);
        const itemModal = document.getElementById('item-modal');
        const itemSearch = document.getElementById('item-search');
        const barcodeQuickInput = document.getElementById('open-item-modal');
        const openScannerBtn = document.getElementById('open-scanner-btn');
        const closeScannerBtn = document.getElementById('close-scanner-btn');
        const scannerPanel = document.getElementById('scanner-panel');
        const scannerVideo = document.getElementById('scanner-video');
        const scanStatus = document.getElementById('scan-status');

        function setScanStatus(message, isWarning) {
            if (!scanStatus) {
                return;
            }
            scanStatus.textContent = message || '';
            scanStatus.style.color = isWarning ? '#b42318' : '#667085';
        }

        function submitItemByBarcode(code) {
            const normalized = String(code || '').trim();
            if (normalized === '') {
                return false;
            }

            let matchedCard = null;
            document.querySelectorAll('.item-row').forEach(function(row) {
                const barcode = String(row.dataset.barcode || '').trim();
                const matched = barcode !== '' && barcode === normalized;
                row.style.display = matched || (itemSearch && String(itemSearch.value || '').trim() === '' ? '' : row.style.display);
                if (matched && !matchedCard) {
                    matchedCard = row;
                }
            });

            if (!matchedCard) {
                setScanStatus('Barcode tidak ditemukan di master barang.', true);
                return false;
            }

            const form = matchedCard.querySelector('.purchase-form');
            if (!form) {
                setScanStatus('Form barang tidak tersedia.', true);
                return false;
            }

            if (itemSearch) {
                itemSearch.value = normalized;
            }
            setScanStatus('Barcode ditemukan. Barang sedang ditambahkan.');
            form.submit();
            return true;
        }

        function applyBarcodeFilter(keyword) {
            const normalized = String(keyword || '').toLowerCase().trim();
            document.querySelectorAll('.item-row').forEach(function(row) {
                const matched = normalized === '' || (row.dataset.search || '').includes(normalized);
                row.style.display = matched ? '' : 'none';
            });
        }

        async function startBarcodeScanner() {
            if (!scannerPanel || !scannerVideo) {
                return;
            }

            if (!supportsCameraScan) {
                setScanStatus('Scan kamera hanya aktif di HTTPS atau localhost. Gunakan input barcode manual.', true);
                toggleItemModal(true);
                return;
            }

            try {
                barcodeDetector = new BarcodeDetector({
                    formats: ['ean_13', 'ean_8', 'code_128', 'code_39', 'upc_a', 'upc_e', 'itf']
                });
                toggleItemModal(true);
                scannerPanel.classList.add('active');
                setScanStatus('Scanner aktif. Arahkan kamera ke barcode.');

                scannerStream = await navigator.mediaDevices.getUserMedia({
                    video: {
                        facingMode: {
                            ideal: 'environment'
                        }
                    },
                    audio: false
                });

                scannerVideo.srcObject = scannerStream;
                await scannerVideo.play();

                scannerTimer = window.setInterval(async function() {
                    if (!barcodeDetector || scannerVideo.readyState < 2) {
                        return;
                    }

                    try {
                        const barcodes = await barcodeDetector.detect(scannerVideo);
                        if (!barcodes || !barcodes.length) {
                            return;
                        }

                        const firstCode = String(barcodes[0].rawValue || '').trim();
                        if (firstCode !== '') {
                            stopBarcodeScanner();
                            submitItemByBarcode(firstCode);
                        }
                    } catch (error) {}
                }, 600);
            } catch (error) {
                stopBarcodeScanner();
                toggleItemModal(true);
                setScanStatus('Kamera tidak bisa dibuka. Pastikan izin kamera aktif.', true);
            }
        }

        window.stopBarcodeScanner = function stopBarcodeScanner() {
            if (scannerTimer) {
                window.clearInterval(scannerTimer);
                scannerTimer = null;
            }

            if (scannerVideo) {
                scannerVideo.pause();
                scannerVideo.srcObject = null;
            }

            if (scannerStream) {
                scannerStream.getTracks().forEach(function(track) {
                    track.stop();
                });
                scannerStream = null;
            }

            if (scannerPanel) {
                scannerPanel.classList.remove('active');
            }
        };

        document.querySelectorAll('.purchase-form').forEach(function(form) {
            const select = form.querySelector('.purchase-mode');
            const card = form.closest('.item-card');
            const priceNode = card ? card.querySelector('.sale-price') : null;

            if (!select || !priceNode) {
                return;
            }

            function updatePrice() {
                const mode = select.value;
                let price = parseFloat(priceNode.dataset.basePrice || '0');

                if (mode === 'eceran') {
                    price = parseFloat(priceNode.dataset.unitPrice || '0');
                } else if (mode === 'setengah') {
                    price = parseFloat(priceNode.dataset.halfPrice || '0');
                }

                priceNode.textContent = rupiah(price);
            }

            select.addEventListener('change', updatePrice);
            updatePrice();
        });

        if (itemSearch) {
            itemSearch.addEventListener('input', function() {
                applyBarcodeFilter(this.value);
            });

            itemSearch.addEventListener('keydown', function(event) {
                if (event.key === 'Enter') {
                    event.preventDefault();
                    submitItemByBarcode(this.value);
                }
            });
        }

        if (barcodeQuickInput) {
            barcodeQuickInput.addEventListener('input', function() {
                applyBarcodeFilter(this.value);
            });

            barcodeQuickInput.addEventListener('keydown', function(event) {
                if (event.key === 'Enter') {
                    event.preventDefault();
                    if (!submitItemByBarcode(this.value)) {
                        toggleItemModal(true);
                        if (itemSearch) {
                            itemSearch.value = this.value;
                            applyBarcodeFilter(this.value);
                            itemSearch.focus();
                            itemSearch.select();
                        }
                    }
                }
            });
        }

        if (openScannerBtn) {
            if (!supportsCameraScan) {
                openScannerBtn.disabled = true;
                openScannerBtn.title = 'Scan kamera butuh HTTPS atau localhost';
                setScanStatus('Mode lokal terdeteksi. Gunakan input barcode manual di kolom sebelah tombol Pilih Barang.');
            }

            openScannerBtn.addEventListener('click', function() {
                startBarcodeScanner();
            });
        }

        if (closeScannerBtn) {
            closeScannerBtn.addEventListener('click', function() {
                stopBarcodeScanner();
                setScanStatus('Scanner ditutup.');
            });
        }

        const cashPaid = document.getElementById('cash_paid');
        const cashPaidDisplay = document.getElementById('cash_paid_display');
        const changeAmount = document.getElementById('change_amount');
        const paymentTypeSelect = document.getElementById('payment_type');
        const customerSelect = document.getElementById('customer_id');
        const customerField = document.getElementById('customer-field');
        const dueDateField = document.getElementById('due-date-field');
        const dueDateInput = document.getElementById('due_date');
        const headerCustomerLabel = document.getElementById('header-customer-label');
        const headerPaymentLabel = document.getElementById('header-payment-label');
        const subtotal = <?= json_encode((float) $subtotal) ?>;
        const checkoutForm = document.getElementById('checkout-form');
        const confirmCheckout = document.getElementById('confirm-checkout');

        function formatInputNumber(value) {
            const digits = String(value || '').replace(/[^\d]/g, '');
            return digits === '' ? '' : Number(digits).toLocaleString('id-ID');
        }

        document.querySelectorAll('.money-inline').forEach(function(input) {
            input.addEventListener('input', function() {
                this.value = formatInputNumber(this.value);
            });
        });

        function updateChange() {
            const paid = parseFloat((cashPaid.value || '0'));
            changeAmount.value = formatInputNumber(Math.max(0, paid - subtotal));
        }

        function syncCheckoutMode() {
            const paymentType = paymentTypeSelect ? paymentTypeSelect.value : 'Tunai';
            const isDebt = paymentType === 'Hutang';
            const isCash = paymentType === 'Tunai';

            if (customerField) {
                customerField.style.display = isDebt ? '' : 'none';
            }
            if (dueDateField) {
                dueDateField.style.display = isDebt ? '' : 'none';
            }
            if (cashPaidDisplay) {
                cashPaidDisplay.readOnly = !isCash;
                cashPaidDisplay.style.background = isCash ? '#fff' : '#f5f7fa';
                if (!isCash) {
                    cashPaid.value = paymentType === 'QRIS' ? String(Math.round(subtotal)) : '0';
                    cashPaidDisplay.value = formatInputNumber(cashPaid.value);
                }
            }
            if (changeAmount) {
                changeAmount.value = isCash ? changeAmount.value : '0';
            }
            if (customerSelect && !isDebt) {
                customerSelect.value = '0';
            }
            if (dueDateInput && !isDebt) {
                dueDateInput.value = '';
            }
            if (headerPaymentLabel) {
                headerPaymentLabel.textContent = 'Termin: ' + paymentType;
            }
            if (headerCustomerLabel) {
                const customerText = isDebt && customerSelect && customerSelect.selectedIndex > 0 ?
                    customerSelect.options[customerSelect.selectedIndex].text :
                    'UMUM';
                headerCustomerLabel.textContent = 'Customer: ' + customerText;
            }
            updateChange();
        }

        if (cashPaid && cashPaidDisplay && changeAmount) {
            cashPaidDisplay.addEventListener('input', function() {
                const raw = this.value.replace(/[^\d]/g, '');
                cashPaid.value = raw;
                this.value = formatInputNumber(raw);
                updateChange();
            });
            updateChange();
        }

        if (paymentTypeSelect) {
            paymentTypeSelect.addEventListener('change', syncCheckoutMode);
        }

        if (customerSelect) {
            customerSelect.addEventListener('change', syncCheckoutMode);
        }

        syncCheckoutMode();

        if (confirmCheckout && checkoutForm) {
            confirmCheckout.addEventListener('click', function() {
                const paymentType = paymentTypeSelect ? paymentTypeSelect.value : 'Tunai';
                const paid = parseFloat(cashPaid.value || '0');
                const change = Math.max(0, paid - subtotal);
                if (paymentType === 'Hutang' && customerSelect && customerSelect.value === '0') {
                    window.alert('Pilih pelanggan terlebih dahulu untuk transaksi hutang.');
                    customerSelect.focus();
                    return;
                }
                const summary = paymentType === 'Hutang' ?
                    'Total belanja: ' + rupiah(subtotal) + '\nPembayaran: Hutang\nPelanggan: ' + (customerSelect && customerSelect.selectedIndex > 0 ? customerSelect.options[customerSelect.selectedIndex].text : '-') + '\n\nLanjut simpan transaksi?' :
                    'Total belanja: ' + rupiah(subtotal) + '\nUang bayar: ' + rupiah(paid) + '\nKembalian: ' + rupiah(change) + '\n\nLanjut simpan transaksi?';
                if (window.confirm(summary)) {
                    checkoutForm.submit();
                }
            });
        }
    }());
</script>