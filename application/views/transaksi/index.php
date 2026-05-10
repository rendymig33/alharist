<?php
$categoryIcons = [
    'Staple Foods' => '🍚',
    'Food & Beverage' => '🥤',
    'Condiments & Spices' => '🧂',
    'Personal Care' => '🧴',
    'Household Supplies' => '🧹',
    'Tobacco' => '🚬',
    'Toys & Games' => '🎮',
    'Healthcare' => '💊',
    'Stationery' => '✏️',
    'Utilities' => '💡',
    'E-SALDO' => '📱',
    'Etc' => '📦'
];

$subtotal = array_sum(array_column($cart, 'line_total'));
$profit = array_sum(array_column($cart, 'line_profit'));
$transactionMode = $transactionMode ?? 'barang';
$currentShift = (int) ($currentShift ?? 1);
$esaldoItems = $esaldoItems ?? [];
$modeLabel = $transactionMode === 'esaldo' ? 'E-Transaction' : 'Transaksi Biasa';
?>
<style>
    .transaction-shell *,
    .transaction-checkout-form *,
    .item-modal-dialog * {
        box-sizing: border-box;
        min-width: 0;
    }

    .transaction-shell,
    .transaction-settings-card,
    .transaction-payment-card,
    .transaction-cart-card {
        --txn-navy: #152033;
        --txn-slate: #5b6475;
        --txn-line: #d8e0ea;
        --txn-soft: #f5f7fb;
        --txn-warm: #b7791f;
        --txn-warm-bg: #fff7e8;
        --txn-success: #0f766e;
        --txn-success-bg: #ecfdf5;
    }

    .transaction-shell {
        padding: 0;
        overflow: hidden;
        border: 1px solid var(--txn-line);
        border-radius: 24px;
        background: linear-gradient(180deg, #ffffff 0%, #fbfcfe 100%);
        box-shadow: 0 18px 44px rgba(15, 23, 42, .08);
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

    .transaction-overview {
        padding: 22px 24px 18px;
        background:
            radial-gradient(circle at top right, rgba(255, 209, 102, .22), transparent 28%),
            linear-gradient(135deg, #142033 0%, #1d2b44 60%, #243551 100%);
        color: #fff;
    }

    .transaction-overview-top {
        display: flex;
        justify-content: space-between;
        align-items: flex-start;
        gap: 16px;
        flex-wrap: wrap;
    }

    .transaction-overview-label {
        font-size: 11px;
        font-weight: 800;
        letter-spacing: .14em;
        text-transform: uppercase;
        color: rgba(255, 255, 255, .72);
        margin-bottom: 8px;
    }

    .transaction-overview-title {
        margin: 0;
        font-size: 26px;
        line-height: 1.1;
        letter-spacing: -.03em;
    }

    .transaction-overview-badge {
        display: inline-flex;
        align-items: center;
        gap: 8px;
        min-height: 42px;
        padding: 0 16px;
        border-radius: 999px;
        background: rgba(255, 255, 255, .12);
        border: 1px solid rgba(255, 255, 255, .18);
        font-size: 12px;
        font-weight: 800;
        letter-spacing: .08em;
        text-transform: uppercase;
    }

    .transaction-overview-metrics {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(150px, 1fr));
        gap: 12px;
        margin-top: 16px;
    }

    .transaction-overview-metric {
        padding: 14px 16px;
        border-radius: 18px;
        background: rgba(255, 255, 255, .08);
        border: 1px solid rgba(255, 255, 255, .12);
        min-width: 0;
    }

    .transaction-overview-metric .label {
        display: block;
        margin-bottom: 6px;
        font-size: 11px;
        font-weight: 800;
        letter-spacing: .08em;
        text-transform: uppercase;
        color: rgba(255, 255, 255, .64);
    }

    .transaction-overview-metric strong {
        display: block;
        color: #fff;
        font-size: 14px;
        line-height: 1.35;
        word-break: break-word;
    }

    .transaction-overview-metric select {
        width: 100%;
        height: 42px;
        border-radius: 12px;
        border: 1px solid rgba(255, 255, 255, .18);
        background: rgba(255, 255, 255, .12);
        color: #fff;
        font-weight: 700;
        padding: 0 12px;
        outline: none;
    }

    .transaction-overview-metric select option {
        color: #111827;
    }

    .transaction-head-grid {
        display: none;
    }

    .transaction-main-grid {
        display: grid;
        grid-template-columns: minmax(0, 1fr) minmax(300px, .42fr);
        gap: 18px;
        padding: 18px;
        align-items: start;
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
        overflow-x: hidden;
        max-width: 100%;
    }

    .transaction-payment-panel {
        padding: 0;
        background: transparent;
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
        grid-template-columns: repeat(auto-fill, minmax(240px, 1fr));
        gap: 16px;
        margin-top: 20px;
        align-items: stretch;
    }

    .item-card {
        border: 1px solid #e2e8f0;
        border-radius: 20px;
        padding: 18px;
        background: #ffffff;
        box-shadow: 0 4px 15px rgba(0, 0, 0, 0.04);
        display: flex;
        flex-direction: column;
        justify-content: space-between;
        gap: 14px;
        transition: all 0.2s ease;
        position: relative;
        overflow: hidden;
    }

    .item-card:hover {
        transform: translateY(-3px);
        box-shadow: 0 10px 25px rgba(0, 0, 0, 0.08);
        border-color: #cbd5e1;
    }

    .item-card-icon {
        position: absolute;
        top: 14px;
        right: 14px;
        font-size: 20px;
        opacity: 0.8;
    }

    .item-card-head {
        display: flex;
        flex-direction: column;
        gap: 4px;
        padding-right: 30px; /* Space for icon */
    }

    .item-card-code {
        font-size: 10px;
        font-weight: 800;
        color: #94a3b8;
        letter-spacing: 0.05em;
        text-transform: uppercase;
    }

    .item-card-name {
        font-size: 16px;
        font-weight: 800;
        color: #1e293b;
        line-height: 1.2;
    }

    .item-card-meta {
        display: flex;
        flex-direction: column;
        gap: 10px;
    }

    .item-stock-badge {
        display: inline-flex;
        padding: 4px 10px;
        background: #f1f5f9;
        border-radius: 8px;
        font-size: 11px;
        font-weight: 700;
        color: #475569;
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
        display: flex;
        flex-direction: column;
        gap: 10px;
        width: 100%;
        margin-top: 10px;
    }

    .purchase-form-inputs {
        display: grid;
        grid-template-columns: 1.5fr 1fr;
        gap: 8px;
    }

    .purchase-form select,
    .purchase-form input {
        height: 42px;
        border-radius: 12px;
        border: 1px solid #e2e8f0;
        padding: 0 12px;
        font-weight: 700;
        font-size: 13px;
        outline: none;
        background: #f8fafc;
        transition: all 0.2s;
    }

    .purchase-form select:focus,
    .purchase-form input:focus {
        border-color: #3b82f6;
        background: #fff;
        box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.1);
    }

    .purchase-form button {
        height: 44px;
        border-radius: 12px;
        background: #e11d48;
        color: #fff;
        border: none;
        font-weight: 800;
        font-size: 14px;
        cursor: pointer;
        transition: all 0.2s;
        box-shadow: 0 4px 12px rgba(225, 29, 72, 0.15);
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 8px;
    }

    .purchase-form button:hover {
        background: #be123c;
        transform: translateY(-2px);
        box-shadow: 0 8px 20px rgba(225, 29, 72, 0.25);
    }

    .purchase-form button::before {
        content: '+';
        font-size: 20px;
        font-weight: 400;
    }

    .transaction-cart-card,
    .transaction-settings-card,
    .transaction-payment-card {
        border: 1px solid var(--txn-line);
        border-radius: 22px;
        background: #fff;
        box-shadow: 0 12px 32px rgba(15, 23, 42, .05);
    }

    .transaction-cart-card {
        padding: 18px;
    }

    .transaction-panel-head {
        display: flex;
        justify-content: space-between;
        align-items: center;
        gap: 14px;
        flex-wrap: wrap;
        margin-bottom: 16px;
    }

    .transaction-panel-title {
        margin: 0;
        color: var(--txn-navy);
        font-size: 20px;
        letter-spacing: -.03em;
    }

    .transaction-inline-badge {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        min-height: 36px;
        padding: 0 14px;
        border-radius: 999px;
        background: var(--txn-warm-bg);
        color: var(--txn-warm);
        font-size: 12px;
        font-weight: 800;
        letter-spacing: .05em;
        text-transform: uppercase;
        border: 1px solid #f7d58d;
        white-space: nowrap;
        flex-shrink: 0;
    }

    .transaction-summary-box {
        background: linear-gradient(180deg, #ffffff 0%, #fbfcfe 100%);
        border: 1px solid var(--txn-line);
        border-radius: 22px;
        padding: 22px;
        box-shadow: 0 14px 30px rgba(15, 23, 42, .05);
    }

    .transaction-payment-grid {
        display: grid;
        gap: 12px;
    }

    .mode-switch {
        display: inline-flex;
        gap: 8px;
        flex-wrap: wrap;
    }

    .mode-pill {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        min-height: 44px;
        padding: 0 18px;
        border-radius: 999px;
        border: 1px solid var(--txn-line);
        background: #fff;
        color: var(--txn-slate);
        font-weight: 700;
        text-decoration: none;
        transition: all .18s ease;
        box-shadow: 0 8px 18px rgba(15, 23, 42, .04);
    }

    .mode-pill.active {
        background: var(--txn-navy);
        border-color: var(--txn-navy);
        color: #fff;
        box-shadow: 0 14px 24px rgba(21, 32, 51, .22);
    }

    .transaction-mobile-stack {
        display: none;
    }

    .transaction-ledger {
        display: grid;
        gap: 10px;
        margin-top: 14px;
    }

    .transaction-ledger-row {
        display: grid;
        grid-template-columns: minmax(0, 1fr) minmax(360px, .65fr);
        gap: 12px;
        padding: 12px 14px;
        border: 1px solid var(--txn-line);
        border-radius: 18px;
        background: linear-gradient(180deg, #ffffff 0%, #fbfdff 100%);
        align-items: center;
    }

    .transaction-ledger-main {
        min-width: 0;
    }

    .transaction-ledger-name {
        font-size: 15px;
        font-weight: 800;
        color: var(--txn-navy);
        line-height: 1.3;
    }

    .transaction-ledger-meta {
        display: flex;
        gap: 10px;
        flex-wrap: wrap;
        margin-top: 5px;
        color: var(--txn-slate);
        font-size: 12px;
    }

    .transaction-ledger-meta strong {
        color: var(--txn-navy);
    }

    .transaction-ledger-side {
        display: grid;
        grid-template-columns: minmax(150px, 1fr) auto 42px;
        align-items: center;
        gap: 8px;
        min-width: 0;
    }

    .transaction-vault-form {
        width: 100%;
    }

    .transaction-vault-select {
        height: 38px;
        padding: 7px 10px;
        border-radius: 10px;
        font-size: 12px;
        font-weight: 700;
    }

    .transaction-item-name {
        font-weight: 800;
        color: var(--txn-navy);
    }

    .transaction-item-note {
        margin-top: 5px;
        color: var(--txn-warm);
    }

    .transaction-item-price {
        font-weight: 800;
        color: var(--txn-navy);
        white-space: nowrap;
    }

    .transaction-inline-total {
        display: inline-flex;
        align-items: center;
        justify-content: space-between;
        gap: 8px;
        min-width: 0;
        width: 148px;
        min-height: 38px;
        padding: 7px 10px;
        border-radius: 10px;
        background: var(--txn-warm-bg);
        border: 1px solid #f6d999;
        color: var(--txn-warm);
    }

    .transaction-inline-total span {
        font-size: 10px;
        font-weight: 800;
        letter-spacing: .05em;
        text-transform: uppercase;
    }

    .transaction-inline-total strong {
        font-size: 13px;
        color: #252525;
        white-space: nowrap;
    }

    .transaction-delete-form {
        width: 42px;
    }

    .transaction-delete-btn {
        width: 38px;
        min-width: 38px;
        min-height: 38px;
        height: 38px;
        padding: 0;
        border-radius: 10px;
        font-size: 18px;
        line-height: 1;
        box-shadow: none;
    }

    .transaction-payment-card {
        padding: 14px;
    }

    .transaction-payment-panel {
        position: sticky;
        top: 18px;
    }

    .transaction-total-hero {
        padding: 18px 16px 20px;
        border-radius: 18px;
        background: linear-gradient(180deg, #142033 0%, #1e2f4b 100%);
        color: #fff;
        box-shadow: 0 18px 34px rgba(20, 32, 51, .18);
    }

    .transaction-total-hero .small {
        color: rgba(255, 255, 255, .72);
        font-weight: 700;
        letter-spacing: .08em;
        text-transform: uppercase;
    }

    .transaction-total-value {
        margin: 8px 0 0;
        font-size: 34px;
        line-height: 1.05;
        font-weight: 800;
        letter-spacing: -.04em;
        text-align: right;
        word-break: break-word;
    }

    .payment-config-modal {
        width: min(540px, calc(100vw - 32px));
    }

    .transaction-payment-input {
        height: 52px;
        font-size: 24px;
        font-weight: 800;
        text-align: right;
    }

    .transaction-payment-submit {
        min-height: 48px;
        letter-spacing: .06em;
    }

    .payment-pop-alert {
        margin-top: 14px;
        padding: 12px 14px;
        border: 1px solid #b7d5ff;
        border-radius: 12px;
        background: #eff6ff;
        color: #1849a9;
        font-size: 13px;
        font-weight: 700;
        line-height: 1.45;
    }

    .transaction-checkout-head {
        display: flex;
        justify-content: space-between;
        gap: 12px;
        align-items: flex-end;
        flex-wrap: wrap;
        margin-bottom: 14px;
    }

    @media (max-width: 1180px) {
        .transaction-main-grid {
            grid-template-columns: 1fr;
        }

        .transaction-payment-panel {
            position: static;
        }
    }

    @media (max-width: 900px) {
        .transaction-add-grid {
            grid-template-columns: 1fr;
        }

        .purchase-form {
            grid-template-columns: 1fr;
        }

        .transaction-panel-head {
            align-items: stretch;
        }
    }

    @media (max-width: 640px) {
        .transaction-main-grid {
            gap: 14px;
            padding: 14px;
        }

        .transaction-overview {
            padding: 18px 16px 16px;
        }

        .transaction-panel-head {
            margin-bottom: 12px;
        }

        .transaction-payment-panel {
            padding: 0;
        }

        .transaction-summary-box {
            padding: 14px;
        }

        .transaction-payment-grid {
            gap: 10px;
        }

        .transaction-total-value {
            font-size: 30px;
            text-align: left;
        }

        .item-modal-dialog {
            width: calc(100vw - 20px);
        }

        .item-price-box {
            width: 100%;
        }

        .transaction-ledger-row {
            grid-template-columns: 1fr;
            align-items: stretch;
        }

        .transaction-ledger-side {
            grid-template-columns: 1fr auto 40px;
        }

        .transaction-cart-summary {
            grid-template-columns: 1fr;
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
            width: 132px;
            padding: 7px 9px;
            border-radius: 12px;
            background: #fff8db;
            color: #8a5a00;
        }

        .transaction-inline-total strong {
            font-size: 12px;
            color: #252525;
        }
    }

    @media (max-width: 420px) {
        .item-grid {
            grid-template-columns: 1fr;
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
    <div class="transaction-overview">
        <div class="transaction-overview-top">
            <div>
                <div class="transaction-overview-label">Kasir Penjualan</div>
                <h2 class="transaction-overview-title">Pencatatan Transaksi</h2>
            </div>
            <div class="transaction-overview-badge"><?= htmlspecialchars($modeLabel) ?></div>
        </div>
        <div class="transaction-overview-metrics">
            <div class="transaction-overview-metric">
                <span class="label">Pembayaran</span>
                <strong id="header-payment-label">Tunai</strong>
            </div>
            <div class="transaction-overview-metric">
                <span class="label">Shift Aktif</span>
                <form method="post">
                    <input type="hidden" name="action" value="set_shift">
                    <select name="shift" onchange="this.form.submit()">
                        <option value="1" <?= $currentShift === 1 ? 'selected' : '' ?>>Shift 1</option>
                        <option value="2" <?= $currentShift === 2 ? 'selected' : '' ?>>Shift 2</option>
                    </select>
                </form>
            </div>
            <div class="transaction-overview-metric">
                <span class="label">Tanggal</span>
                <strong><?= date('d M Y') ?></strong>
            </div>
            <div class="transaction-overview-metric">
                <span class="label">Nomor Transaksi</span>
                <strong><?= htmlspecialchars((string) ($nextInvoiceNo ?? 'AUTO')) ?></strong>
            </div>
        </div>
    </div>
    <div class="transaction-main-grid">
        <div class="transaction-cart-card">
            <div class="transaction-panel-head">
                <div>
                    <div class="small" style="font-weight:700; color:#d27a00;">Mode Penjualan</div>
                    <h3 class="transaction-panel-title">Item Transaksi</h3>
                </div>
            </div>
            <div style="display:flex; justify-content:space-between; gap:12px; align-items:flex-end; flex-wrap:wrap;">
                <div>
                    <div class="mode-switch" style="margin-top:10px;">
                        <a href="index.php?route=transaksi&mode=barang" class="mode-pill <?= $transactionMode === 'barang' ? 'active' : '' ?>">Transaksi Biasa</a>
                        <a href="index.php?route=transaksi&mode=esaldo" class="mode-pill <?= $transactionMode === 'esaldo' ? 'active' : '' ?>">E-Transaction</a>
                    </div>
                </div>
                <div style="display:flex; gap:10px; align-items:center; flex-wrap:wrap;">
                    <div class="transaction-inline-badge"><?= count($cart) ?> Item di Keranjang</div>
                    <div class="transaction-inline-badge">Shift <?= $currentShift ?></div>
                </div>
            </div>
            <?php if ($transactionMode === 'barang'): ?>
                <div class="transaction-add-grid">
                    <input id="open-item-modal" type="text" inputmode="numeric" autocomplete="off" placeholder="Scan / input barcode lalu Enter" style="background:#fff;">
                    <button type="button" class="btn" onclick="toggleItemModal(true)">Pilih Barang</button>
                    <button type="button" class="btn btn-info scan-btn" id="open-scanner-btn">Scan Barcode</button>
                </div>
                <div class="small scan-status" id="scan-status"></div>
            <?php else: ?>
                <div class="transaction-add-grid" style="grid-template-columns:1fr auto; margin-top:14px;">
                    <input id="open-item-modal" type="text" inputmode="text" autocomplete="off" placeholder="Cari nama E-Saldo" style="background:#fff;">
                    <button type="button" class="btn" onclick="toggleItemModal(true)">Pilih E-Saldo</button>
                </div>
                <div class="small scan-status" id="scan-status">Mode E-Transaction aktif. Modal dan harga jual diisi manual pada setiap item.</div>
            <?php endif; ?>

            <div class="transaction-ledger">
                <?php foreach ($cart as $index => $row): ?>
                    <div class="transaction-ledger-row">
                        <div class="transaction-ledger-main">
                            <div class="transaction-ledger-name"><?= htmlspecialchars($row['name']) ?></div>
                            <div class="transaction-ledger-meta">
                                <span>No. <?= $index + 1 ?></span>
                                <span><strong>Qty</strong> <?= format_qty((float) ($row['display_qty'] ?? $row['qty'])) ?></span>
                                <span><strong>Satuan</strong> <?= htmlspecialchars((string) ($row['purchase_label'] ?? ($row['stock_display'] ?? ''))) ?></span>
                                <span><strong>Harga</strong> <?= rupiah((float) $row['selling_price']) ?></span>
                            </div>
                            <?php if (!empty($row['promo_label'])): ?>
                                <div class="small transaction-item-note"><?= htmlspecialchars((string) $row['promo_label']) ?></div>
                            <?php endif; ?>
                        </div>
                        <div class="transaction-ledger-side">
                            <form method="post" class="transaction-vault-form">
                                <input type="hidden" name="action" value="update_item_vault">
                                <input type="hidden" name="index" value="<?= (int) $index ?>">
                                <select name="vault_id" class="transaction-vault-select" onchange="this.form.submit()" aria-label="Pilih dana item">
                                    <option value="0">Pilih Dana</option>
                                    <?php foreach ($vaults as $vault): ?>
                                        <option value="<?= (int) $vault['id'] ?>" <?= (int) ($row['vault_id'] ?? 0) === (int) $vault['id'] ? 'selected' : '' ?>>
                                            <?= htmlspecialchars((string) ($vault['bank_name'] ?: 'Vault')) ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </form>
                            <div class="transaction-inline-total">
                                <span>Subtotal</span>
                                <strong><?= rupiah((float) $row['line_total']) ?></strong>
                            </div>
                            <form method="post" class="transaction-delete-form">
                                <input type="hidden" name="action" value="remove_item">
                                <input type="hidden" name="index" value="<?= (int) $index ?>">
                                <button class="btn-secondary transaction-delete-btn" type="submit" title="Hapus item" aria-label="Hapus item">&#128465;</button>
                            </form>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
        <div class="transaction-payment-panel">
            <div class="transaction-payment-card">
                <div class="transaction-panel-head" style="margin-bottom:10px;">
                    <div>
                        <h3 class="transaction-panel-title">Pembayaran</h3>
                    </div>
                </div>
                <div class="transaction-total-hero">
                    <div class="small">Total Belanja</div>
                    <div class="transaction-total-value"><?= rupiah($subtotal) ?></div>
                </div>
                <div class="transaction-summary-box" style="margin-top:12px; padding:14px;">
                    <div class="transaction-payment-grid">
                        <div class="small">Uang Bayar</div>
                        <input type="text" id="cash_paid_display" class="transaction-payment-input" inputmode="numeric" autocomplete="off" placeholder="0">
                        <input type="hidden" id="cash_paid" name="cash_paid" form="checkout-form">
                        <div class="small">Kembalian</div>
                        <input type="text" id="change_amount" class="transaction-payment-input" placeholder="0" readonly style="background:#fff;">
                        <button type="button" class="btn-green transaction-payment-submit" id="confirm-checkout">BAYAR</button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="transaction-checkout-form">
    <form method="post" id="checkout-form">
        <input type="hidden" name="action" value="checkout">
        <input type="hidden" name="payment_type" id="payment_type" value="Tunai">
        <input type="hidden" name="customer_id" id="customer_id" value="0">
        <input type="hidden" name="due_date" id="due_date" value="">
    </form>
</div>

<div class="modal-backdrop" id="payment-config-modal">
    <div class="modal payment-config-modal">
        <div class="modal-head">
            <div>
                <div class="section-title">Pengaturan Pembayaran</div>
                <h3 style="margin:0;">Finalisasi Transaksi</h3>
            </div>
            <button type="button" class="modal-close" id="close-payment-config">Tutup</button>
        </div>
        <div class="form-grid" style="margin-top:16px;">
            <div>
                <div class="small">Pembayaran</div>
                <select id="payment_type_modal" required>
                    <option value="Tunai">Tunai</option>
                    <option value="QRIS">QRIS</option>
                    <option value="Hutang">Hutang</option>
                    <option value="Prive">Prive</option>
                </select>
            </div>
            <div id="customer-field-modal" style="display:none;">
                <div class="small">Pelanggan</div>
                <select id="customer_id_modal">
                    <option value="0">Pilih Pelanggan</option>
                    <?php foreach ($customers as $customer): ?>
                        <option value="<?= (int) $customer['id'] ?>"><?= htmlspecialchars((string) $customer['name']) ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div id="due-date-field-modal" style="display:none;">
                <div class="small">Jatuh Tempo</div>
                <input type="date" id="due_date_modal">
            </div>
        </div>
        <div class="payment-pop-alert" id="payment-config-notice">
            Konfirmasi Pembayaran : Silahkan Pilih Jenis Metode Pembayaran
        </div>
        <div style="display:flex; justify-content:space-between; gap:12px; margin-top:18px; flex-wrap:wrap;">
            <div class="transaction-inline-badge">Shift <?= $currentShift ?></div>
            <div style="display:flex; gap:10px;">
                <button type="button" class="btn btn-secondary" id="cancel-payment-config">Batal</button>
                <button type="button" class="btn-green" id="submit-payment-config">Simpan Transaksi</button>
            </div>
        </div>
    </div>
</div>

<div class="modal-backdrop" id="item-modal">
    <div class="modal item-modal-dialog">
        <div class="modal-head">
            <h3 style="margin:0;">Pilih Barang</h3>
            <button type="button" class="modal-close" onclick="toggleItemModal(false)">Tutup</button>
        </div>
        <input class="item-modal-search" id="item-search" type="text" inputmode="text" autocomplete="off" placeholder="Cari kode / nama barang / barcode">
        <div class="scanner-panel" id="scanner-panel">
            <video id="scanner-video" class="scanner-video" autoplay muted playsinline></video>
            <div class="small">Arahkan kamera ke barcode produk. Jika barcode cocok, barang akan langsung dipilih.</div>
            <button type="button" class="btn btn-secondary" id="close-scanner-btn">Tutup Scanner</button>
        </div>
        <div class="item-modal-table-wrap">
            <div class="item-grid">
                <?php if ($transactionMode === 'barang'): ?>
                    <?php foreach ($items as $item): ?>
                        <div class="item-row item-card" data-search="<?= htmlspecialchars(strtolower(trim(($item['code'] ?? '') . ' ' . ($item['barcode'] ?? '') . ' ' . ($item['name'] ?? '')))) ?>" data-barcode="<?= htmlspecialchars((string) ($item['barcode'] ?? '')) ?>">
                            <div class="item-card-icon"><?= $categoryIcons[$item['category'] ?? 'Etc'] ?? '📦' ?></div>
                            <div class="item-card-head">
                                <div class="item-card-code"><?= htmlspecialchars($item['code']) ?></div>
                                <?php if (!empty($item['barcode'])): ?>
                                    <div class="small" style="font-size:9px; color:#94a3b8;">#<?= htmlspecialchars((string) $item['barcode']) ?></div>
                                <?php endif; ?>
                                <div class="item-card-name"><?= htmlspecialchars($item['name']) ?></div>
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
                                    <input type="hidden" name="action" value="add_item">
                                    <input type="hidden" name="transaction_mode" value="barang">
                                    <input type="hidden" name="item_id" value="<?= (int) $item['id'] ?>">
                                    <div class="purchase-form-inputs">
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
                                    </div>
                                    <button type="submit">Tambah</button>
                                </form>
                            </div>
                        </div>
                    <?php endforeach; ?>
                <?php else: ?>
                    <?php foreach ($esaldoItems as $item): ?>
                        <div class="item-row item-card" data-search="<?= htmlspecialchars(strtolower(trim(($item['code'] ?? '') . ' ' . ($item['name'] ?? '')))) ?>" data-barcode="">
                            <div class="item-card-icon"><?= $categoryIcons['E-SALDO'] ?></div>
                            <div class="item-card-head">
                                <div class="item-card-code"><?= htmlspecialchars((string) $item['code']) ?></div>
                                <div class="item-card-name"><?= htmlspecialchars((string) $item['name']) ?></div>
                                <div class="small">Produk digital tanpa stok fisik.</div>
                            </div>
                            <div class="item-card-meta">
                                <div class="item-price-box">
                                    <span class="small" style="color:#8a5a00;">Nama master</span>
                                    <strong><?= htmlspecialchars((string) $item['name']) ?></strong>
                                </div>
                            </div>
                            <div>
                                <form method="post" class="purchase-form" style="grid-template-columns:1fr 1fr;">
                                    <input type="hidden" name="action" value="add_item">
                                    <input type="hidden" name="transaction_mode" value="esaldo">
                                    <input type="hidden" name="item_id" value="<?= (int) $item['id'] ?>">
                                    <input type="number" name="qty" value="1" min="1" inputmode="numeric" placeholder="Qty">
                                    <input type="text" name="purchase_price" inputmode="numeric" autocomplete="off" placeholder="Modal">
                                    <input type="text" name="selling_price" inputmode="numeric" autocomplete="off" placeholder="Harga jual">
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
        const transactionMode = <?= json_encode($transactionMode) ?>;
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
            if (transactionMode !== 'barang') {
                return false;
            }
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
            if (transactionMode !== 'barang') {
                return;
            }
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

        document.querySelectorAll('input[name="purchase_price"], input[name="selling_price"]').forEach(function(input) {
            input.addEventListener('input', function() {
                const digits = String(this.value || '').replace(/[^\d]/g, '');
                this.value = digits === '' ? '' : Number(digits).toLocaleString('id-ID');
            });
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
            if (transactionMode !== 'barang') {
                openScannerBtn.disabled = true;
                openScannerBtn.title = 'Scanner hanya aktif untuk transaksi barang';
            } else if (!supportsCameraScan) {
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
        const dueDateInput = document.getElementById('due_date');
        const paymentTypeModal = document.getElementById('payment_type_modal');
        const customerSelectModal = document.getElementById('customer_id_modal');
        const customerFieldModal = document.getElementById('customer-field-modal');
        const dueDateFieldModal = document.getElementById('due-date-field-modal');
        const dueDateInputModal = document.getElementById('due_date_modal');
        const headerPaymentLabel = document.getElementById('header-payment-label');
        const subtotal = <?= json_encode((float) $subtotal) ?>;
        const checkoutForm = document.getElementById('checkout-form');
        const confirmCheckout = document.getElementById('confirm-checkout');
        const paymentConfigModal = document.getElementById('payment-config-modal');
        const closePaymentConfig = document.getElementById('close-payment-config');
        const cancelPaymentConfig = document.getElementById('cancel-payment-config');
        const submitPaymentConfig = document.getElementById('submit-payment-config');

        function formatInputNumber(value) {
            const digits = String(value || '').replace(/[^\d]/g, '');
            return digits === '' ? '' : Number(digits).toLocaleString('id-ID');
        }

        function updateChange() {
            const paid = parseFloat((cashPaid.value || '0'));
            changeAmount.value = formatInputNumber(Math.max(0, paid - subtotal));
        }

        function syncCheckoutMode() {
            const paymentType = paymentTypeModal ? paymentTypeModal.value : 'Tunai';
            const isDebt = paymentType === 'Hutang';
            const isCash = paymentType === 'Tunai';

            if (customerFieldModal) {
                customerFieldModal.style.display = isDebt ? '' : 'none';
            }
            if (dueDateFieldModal) {
                dueDateFieldModal.style.display = isDebt ? '' : 'none';
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
            if (customerSelectModal && !isDebt) {
                customerSelectModal.value = '0';
            }
            if (dueDateInputModal && !isDebt) {
                dueDateInputModal.value = '';
            }
            if (headerPaymentLabel) {
                headerPaymentLabel.textContent = paymentType;
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

        function togglePaymentConfig(show) {
            if (!paymentConfigModal) {
                return;
            }
            paymentConfigModal.classList.toggle('active', show);
        }

        if (paymentTypeModal) {
            paymentTypeModal.addEventListener('change', syncCheckoutMode);
        }

        syncCheckoutMode();

        if (confirmCheckout && checkoutForm) {
            confirmCheckout.addEventListener('click', function() {
                togglePaymentConfig(true);
            });
        }

        if (closePaymentConfig) {
            closePaymentConfig.addEventListener('click', function() {
                togglePaymentConfig(false);
            });
        }

        if (cancelPaymentConfig) {
            cancelPaymentConfig.addEventListener('click', function() {
                togglePaymentConfig(false);
            });
        }

        if (submitPaymentConfig) {
            submitPaymentConfig.addEventListener('click', function() {
                const paymentType = paymentTypeModal ? paymentTypeModal.value : 'Tunai';
                const paid = parseFloat(cashPaid.value || '0');
                const change = Math.max(0, paid - subtotal);

                if (paymentType === 'Hutang' && customerSelectModal && customerSelectModal.value === '0') {
                    if (typeof showToast === 'function') {
                        showToast('Pilih pelanggan terlebih dahulu untuk transaksi hutang.', 'warning');
                    }
                    customerSelectModal.focus();
                    return;
                }

                if (paymentTypeSelect) {
                    paymentTypeSelect.value = paymentType;
                }
                if (customerSelect) {
                    customerSelect.value = customerSelectModal ? customerSelectModal.value : '0';
                }
                if (dueDateInput) {
                    dueDateInput.value = dueDateInputModal ? dueDateInputModal.value : '';
                }

                const customerName = customerSelectModal && customerSelectModal.selectedIndex > 0 ?
                    customerSelectModal.options[customerSelectModal.selectedIndex].text :
                    '-';
                const summary = paymentType === 'Hutang' ?
                    'Total belanja: ' + rupiah(subtotal) + '\nPembayaran: Hutang\nPelanggan: ' + customerName + '\n\nLanjut simpan transaksi?' :
                    'Total belanja: ' + rupiah(subtotal) + '\nPembayaran: ' + paymentType + '\nUang bayar: ' + rupiah(paid) + '\nKembalian: ' + rupiah(change) + '\n\nLanjut simpan transaksi?';

                function submitCheckout() {
                    checkoutForm.submit();
                }

                togglePaymentConfig(false);
                if (typeof askConfirmation === 'function') {
                    askConfirmation(summary, submitCheckout, 'Pemberitahuan Pembayaran', 'Ya, Simpan', 'btn-success');
                } else if (window.confirm(summary)) {
                    submitCheckout();
                }
            });
        }
    }());
</script>