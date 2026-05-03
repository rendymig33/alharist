<!doctype html>
<html lang="id">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?= htmlspecialchars($title ?? ($config['app_name'] ?? 'App')) ?></title>
    <style>
        :root {
            --red: #d71920;
            --yellow: #ffd54a;
            --ink: #252525;
            --bg: #f6f7fb;
            --line: #e2e4ea;
            --white: #ffffff;
            --green: #16794d;
            --soft-red: #fff0f0;
            --soft-yellow: #fff8db;
            --soft-blue: #eff5ff;
        }

        * {
            box-sizing: border-box;
            font-family: "Segoe UI", Tahoma, sans-serif;
        }

        body {
            margin: 0;
            background: var(--bg);
            color: var(--ink);
        }

        a {
            color: inherit;
            text-decoration: none;
        }

        .app {
            display: grid;
            grid-template-columns: 240px 1fr;
            min-height: 100vh;
        }

        .sidebar {
            background: linear-gradient(180deg, #cf1a21, #961017);
            color: var(--white);
            padding: 20px;
            box-shadow: inset -1px 0 0 rgba(255, 255, 255, .08);
        }

        .sidebar-toggle {
            display: none;
            width: auto;
            min-width: 44px;
            padding: 10px 12px;
            border-radius: 12px;
            background: #fff;
            color: var(--red);
            border: 1px solid var(--line);
            font-weight: 800;
            box-shadow: 0 8px 20px rgba(28, 39, 60, .08);
        }

        .brand {
            background: linear-gradient(135deg, #ffe588, var(--yellow));
            color: var(--red);
            font-weight: 800;
            padding: 16px;
            border-radius: 16px;
            text-align: center;
            margin-bottom: 20px;
            letter-spacing: .5px;
            box-shadow: 0 10px 24px rgba(0, 0, 0, .12);
        }

        .menu a {
            display: block;
            padding: 12px 14px;
            border-radius: 12px;
            margin-bottom: 8px;
            background: rgba(255, 255, 255, .08);
            transition: .2s ease;
        }

        .menu a.active,
        .menu a:hover {
            background: var(--yellow);
            color: var(--red);
            font-weight: 700;
        }

        .content {
            padding: 24px;
            background: radial-gradient(circle at top right, #fff5d3 0, transparent 22%), var(--bg);
        }

        .topbar {
            background: linear-gradient(135deg, #ffffff, #fff9eb);
            padding: 6px 18px;
            border-radius: 18px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            border: 1px solid var(--line);
            box-shadow: 0 14px 30px rgba(28, 39, 60, .06);
            margin-bottom: 15px;
        }

        .topbar-left {
            display: flex;
            flex-direction: column;
            gap: 4px;
        }

        .topbar-right {
            display: flex;
            align-items: center;
            gap: 20px;
        }

        .clock-time {
            font-size: 24px;
            font-weight: 800;
            color: #020105;
            line-height: 1;
            font-family: 'JetBrains Mono', 'Courier New', monospace;
            text-shadow: 0 0 12px rgba(251, 191, 36, 0.4);
            letter-spacing: 1px;
        }

        .clock-date {
            font-size: 10px;
            font-weight: 700;
            color: #94a3b8;
            text-transform: uppercase;
            letter-spacing: 1.5px;
            margin-top: 4px;
        }

        .topbar-header {
            display: flex;
            align-items: center;
            gap: 16px;
            margin-bottom: 6px;
        }

        .grid {
            display: grid;
            gap: 18px;
        }

        .grid.cards {
            grid-template-columns: repeat(auto-fit, minmax(180px, 1fr));
            margin-top: 18px;
        }

        .card {
            background: var(--white);
            border: 1px solid var(--line);
            border-radius: 18px;
            padding: 18px;
            box-shadow: 0 12px 24px rgba(28, 39, 60, .05);
        }

        .card h3,
        .card h4 {
            margin-top: 0;
        }

        .two-col {
            display: grid;
            grid-template-columns: 1.1fr .9fr;
            gap: 18px;
            margin-top: 18px;
        }

        .three-col {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 18px;
        }

        table {
            width: 100%;
            border-collapse: separate;
            border-spacing: 0;
        }

        th,
        td {
            border-bottom: 1px solid var(--line);
            padding: 12px 10px;
            text-align: left;
            font-size: 14px;
            vertical-align: top;
        }

        th {
            background: var(--soft-yellow);
            color: #5a4700;
        }

        th:first-child {
            border-top-left-radius: 12px;
        }

        th:last-child {
            border-top-right-radius: 12px;
        }

        input,
        select,
        textarea,
        button {
            width: 100%;
            padding: 11px 12px;
            border: 1px solid #ccd1da;
            border-radius: 12px;
            font-size: 14px;
        }

        input:focus,
        select:focus,
        textarea:focus {
            outline: none;
            border-color: #f0b100;
            box-shadow: 0 0 0 4px rgba(255, 213, 74, .25);
        }

        button,
        .btn {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            padding: 10px 20px;
            border-radius: 12px;
            font-size: 14px;
            font-weight: 700;
            border: none;
            cursor: pointer;
            transition: all 0.2s cubic-bezier(0.4, 0, 0.2, 1);
            text-decoration: none;
            line-height: 1.4;
            white-space: nowrap;
            background: var(--red);
            color: var(--white);
            box-shadow: 0 4px 12px rgba(215, 25, 32, 0.15);
        }

        button:hover,
        .btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 25px rgba(215, 25, 32, 0.25);
            opacity: 0.95;
        }

        button:active,
        .btn:active {
            transform: translateY(0);
        }

        .btn-secondary {
            background: #f2f4f7;
            color: #344054;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.05);
        }

        .btn-secondary:hover {
            background: #eaecf0;
            box-shadow: 0 8px 20px rgba(0, 0, 0, 0.1);
        }

        .btn-info {
            background: #1570ef;
            color: var(--white);
            box-shadow: 0 4px 12px rgba(21, 112, 239, 0.2);
        }

        .btn-info:hover {
            box-shadow: 0 8px 25px rgba(21, 112, 239, 0.3);
        }

        .btn-danger {
            background: #d92d20;
            color: var(--white);
            box-shadow: 0 4px 12px rgba(217, 45, 32, 0.2);
        }

        .btn-danger:hover {
            box-shadow: 0 8px 25px rgba(217, 45, 32, 0.3);
        }

        .btn-success,
        .btn-green {
            background: #079455;
            color: var(--white);
            box-shadow: 0 4px 12px rgba(7, 148, 85, 0.2);
        }

        .btn-success:hover,
        .btn-green:hover {
            box-shadow: 0 8px 25px rgba(7, 148, 85, 0.3);
        }

        .btn-warning {
            background: #f79009;
            color: var(--white);
            box-shadow: 0 4px 12px rgba(247, 144, 9, 0.2);
        }

        .btn-warning:hover {
            box-shadow: 0 8px 25px rgba(247, 144, 9, 0.3);
        }

        .form-grid {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 12px;
        }

        .toast-container {
            position: fixed;
            top: 24px;
            right: 24px;
            z-index: 10005;
            display: grid;
            gap: 12px;
            pointer-events: none;
        }

        .toast {
            min-width: 320px;
            max-width: 420px;
            background: #fff;
            border-radius: 16px;
            padding: 16px;
            box-shadow: 0 12px 32px rgba(0, 0, 0, 0.12);
            display: flex;
            align-items: center;
            gap: 14px;
            pointer-events: auto;
            animation: toastIn 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            border-left: 6px solid #1e293b;
        }

        @keyframes toastIn {
            from {
                transform: translateX(100%);
                opacity: 0;
            }

            to {
                transform: translateX(0);
                opacity: 1;
            }
        }

        .toast.success {
            border-left-color: #079455;
        }

        .toast.warning {
            border-left-color: #f79009;
        }

        .toast.error {
            border-left-color: #d92d20;
        }

        .toast-content {
            flex: 1;
        }

        .toast-title {
            font-weight: 800;
            font-size: 14px;
            margin-bottom: 2px;
        }

        .toast-message {
            font-size: 13px;
            color: #667085;
        }

        .toast-close {
            background: none;
            border: none;
            color: #98a2b3;
            cursor: pointer;
            padding: 4px;
            font-size: 18px;
            line-height: 1;
            min-height: auto;
            width: auto;
        }

        .metric {
            font-size: 26px;
            font-weight: 700;
            margin-top: 8px;
        }

        .small {
            font-size: 13px;
            color: #667085;
        }

        .transaction-layout {
            display: grid;
            grid-template-columns: 1.1fr .9fr;
            gap: 18px;
            margin-top: 18px;
        }

        .toolbar {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-top: 18px;
            gap: 12px;
        }

        .toolbar .btn {
            width: auto;
            padding: 10px 16px;
        }

        .modal-backdrop {
            position: fixed;
            inset: 0;
            background: rgba(0, 0, 0, .35);
            display: none;
            align-items: center;
            justify-content: center;
            padding: 20px;
            z-index: 99;
        }

        .modal-backdrop.active {
            display: flex;
        }

        #success-modal-backdrop,
        #confirm-modal-backdrop {
            z-index: 10001;
        }

        .modal-success {
            text-align: center;
            padding: 40px 20px;
            max-width: 400px;
        }

        .modal-success .icon {
            width: 80px;
            height: 80px;
            background: #ecfdf5;
            color: #079455;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 40px;
            margin: 0 auto 20px;
        }

        .modal-success h2 {
            margin-bottom: 10px;
            color: #101828;
        }

        .modal-success p {
            color: #667085;
            margin-bottom: 24px;
        }

        .modal-confirm {
            text-align: center;
            padding: 30px 20px;
            max-width: 400px;
        }

        .modal-confirm .icon {
            width: 70px;
            height: 70px;
            background: #fef2f2;
            color: #d92d20;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 34px;
            margin: 0 auto 16px;
        }

        .modal-confirm h2 {
            font-size: 20px;
            margin-bottom: 8px;
            color: #101828;
        }

        .modal-confirm p {
            color: #667085;
            margin-bottom: 24px;
            font-size: 14px;
        }

        .confirm-actions {
            display: flex;
            gap: 12px;
            justify-content: center;
        }

        .confirm-actions .btn {
            flex: 1;
        }

        .modal {
            width: min(920px, 100%);
            max-height: 90vh;
            overflow: auto;
            background: #fff;
            border-radius: 20px;
            border: 1px solid var(--line);
            padding: 20px;
            box-shadow: 0 24px 48px rgba(0, 0, 0, .18);
        }

        .modal-head {
            display: flex;
            justify-content: space-between;
            align-items: center;
            gap: 12px;
            margin-bottom: 14px;
        }

        .modal-close {
            width: auto;
            padding: 8px 12px;
            background: #eef1f6;
            color: #252525;
        }

        .section-title {
            font-size: 12px;
            font-weight: 800;
            letter-spacing: .08em;
            text-transform: uppercase;
            color: #98a2b3;
            margin-bottom: 10px;
        }

        .soft-panel {
            background: linear-gradient(180deg, #fffef8, #fff7db);
            border: 1px solid #f1df9b;
            border-radius: 16px;
            padding: 14px;
        }

        .info-strip {
            background: linear-gradient(135deg, #fff4d0, #fff9eb);
            border: 1px solid #f1dd9b;
            border-radius: 14px;
            padding: 14px 16px;
        }

        .action-row {
            display: flex;
            gap: 8px;
            flex-wrap: wrap;
        }

        .action-row .btn,
        .action-row button {
            width: auto;
            min-width: 84px;
            padding: 9px 12px;
        }

        .badge {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            padding: 6px 10px;
            border-radius: 999px;
            background: #f3f4f6;
            font-size: 12px;
            font-weight: 700;
            color: #475467;
        }

        .search-reset-actions {
            display: flex;
            gap: 12px;
            align-items: stretch;
        }

        .search-reset-actions .btn,
        .search-reset-actions button {
            min-width: 120px;
            min-height: 44px;
            padding: 0 18px;
            border-radius: 14px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            font-weight: 700;
            text-decoration: none;
            box-shadow: none;
        }

        .detail-grid {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 12px;
        }

        .detail-box {
            background: #fafbfc;
            border: 1px solid var(--line);
            border-radius: 14px;
            padding: 14px;
        }

        @media (max-width: 920px) {

            .app,
            .two-col,
            .transaction-layout,
            .form-grid,
            .three-col {
                grid-template-columns: 1fr;
            }

            .app {
                min-height: auto;
            }

            .sidebar {
                position: fixed;
                top: 0;
                left: 0;
                bottom: 0;
                width: min(300px, 84vw);
                transform: translateX(-102%);
                transition: transform .24s ease;
                z-index: 120;
                padding: 18px 16px 20px;
                overflow-y: auto;
                border-radius: 0 20px 20px 0;
                min-height: 100vh;
            }

            .sidebar.open {
                transform: translateX(0);
            }

            .sidebar-toggle {
                display: inline-flex;
                align-items: center;
                justify-content: center;
            }

            .menu {
                display: grid;
                grid-template-columns: 1fr;
                gap: 10px;
            }

            .menu a {
                margin-bottom: 0;
                min-width: 0;
                white-space: normal;
                text-align: left;
                min-height: 48px;
                display: flex;
                align-items: center;
                justify-content: flex-start;
            }

            .content {
                padding: 16px;
            }

            .topbar {
                flex-direction: column;
                align-items: stretch;
                gap: 16px;
                padding: 16px;
            }

            .topbar-left {
                width: 100%;
            }

            .topbar-right {
                width: 100%;
                justify-content: center;
            }

            .header-clock {
                width: 100%;
                align-items: center;
                min-width: 0;
            }

            .toolbar {
                flex-direction: column;
                align-items: stretch;
            }

            .toolbar .btn {
                width: 100%;
            }

            .detail-grid {
                grid-template-columns: 1fr;
            }

            .modal-backdrop {
                padding: 12px;
            }

            .modal {
                width: min(100%, 100%);
                max-height: 92vh;
                padding: 16px;
                border-radius: 16px;
            }

            table {
                display: block;
                overflow-x: auto;
                white-space: nowrap;
            }
        }

        @media (max-width: 640px) {
            body {
                font-size: 14px;
            }

            .brand {
                margin-bottom: 14px;
                padding: 14px;
                font-size: 16px;
                overflow: hidden;
                text-overflow: ellipsis;
                white-space: nowrap;
            }

            .content {
                padding: 12px;
            }

            .topbar {
                border-radius: 14px;
                gap: 8px;
                padding: 14px;
            }

            .topbar .small {
                font-size: 12px;
                line-height: 1.45;
            }

            .card {
                padding: 14px;
                border-radius: 14px;
            }

            th,
            td {
                padding: 10px 8px;
                font-size: 13px;
            }

            input,
            select,
            textarea,
            button {
                min-height: 44px;
                font-size: 14px;
            }

            .action-row .btn,
            .action-row button {
                flex: 1 1 120px;
            }

            .menu {
                grid-template-columns: 1fr;
            }

            .modal {
                padding: 14px;
            }
        }

        /* BCA Ledger Style (Rekening Koran) */
        .bca-ledger-wrap {
            background: #fff;
            border: 1px solid var(--line);
            border-radius: 12px;
            overflow: hidden;
            margin-top: 10px;
        }

        .bca-ledger {
            width: 100%;
            border-collapse: collapse;
            font-size: 13px;
        }

        .bca-ledger th {
            background: #f2f4f7;
            color: #475467;
            font-weight: 700;
            text-transform: uppercase;
            font-size: 11px;
            letter-spacing: 0.05em;
            padding: 12px 15px;
            border-bottom: 2px solid #eaecf0;
            text-align: left;
        }

        .bca-ledger td {
            padding: 12px 15px;
            border-bottom: 1px solid #f2f4f7;
            vertical-align: top;
            line-height: 1.5;
        }

        .bca-ledger tr:last-child td {
            border-bottom: none;
        }

        .bca-ledger .date {
            white-space: nowrap;
            color: #667085;
            font-weight: 600;
            width: 100px;
        }

        .bca-ledger .desc {
            color: #1d2939;
            font-weight: 500;
        }

        .bca-ledger .desc-main {
            font-weight: 700;
            display: block;
            margin-bottom: 2px;
        }

        .bca-ledger .desc-sub {
            font-size: 11px;
            color: #667085;
            display: block;
        }

        .bca-ledger .amount {
            text-align: right;
            font-weight: 800;
            white-space: nowrap;
            width: 150px;
        }

        .bca-ledger .balance {
            text-align: right;
            color: #101828;
            font-weight: 700;
            width: 150px;
        }

        .bca-ledger .db {
            color: #d71920;
        }

        .bca-ledger .cr {
            color: #16794d;
        }

        .bca-ledger .type-label {
            font-size: 10px;
            padding: 2px 6px;
            border-radius: 4px;
            font-weight: 800;
            margin-left: 6px;
            display: inline-block;
            vertical-align: middle;
        }

        .bca-ledger .type-db {
            background: #fff0f0;
            color: #d71920;
        }

        .bca-ledger .type-cr {
            background: #e7f8ef;
            color: #16794d;
        }

        @media (max-width: 640px) {
            .bca-ledger thead {
                display: none;
            }

            .bca-ledger tr {
                display: block;
                padding: 15px;
                border-bottom: 1px solid #f2f4f7;
            }

            .bca-ledger td {
                display: block;
                padding: 0;
                border: none;
                width: 100% !important;
                text-align: left !important;
                margin-bottom: 8px;
            }

            .bca-ledger td:last-child {
                margin-bottom: 0;
            }

            .bca-ledger td::before {
                content: attr(data-label);
                display: block;
                font-size: 10px;
                font-weight: 800;
                color: #98a2b3;
                text-transform: uppercase;
                margin-bottom: 2px;
            }
        }
    </style>
    <script>
        let confirmCallback = null;

        function showToast(message, type = 'success') {
            const container = document.getElementById('toast-container');
            if (!container) return;
            const toast = document.createElement('div');
            toast.className = `toast ${type}`;
            const title = type === 'success' ? 'Berhasil' : (type === 'warning' ? 'Perhatian' : 'Info');
            toast.innerHTML = `
                <div class="toast-content">
                    <div class="toast-title">${title}</div>
                    <div class="toast-message">${message}</div>
                </div>
                <button class="toast-close" onclick="this.parentElement.remove()">&times;</button>
            `;
            container.appendChild(toast);
            setTimeout(() => {
                if (toast.parentElement) {
                    toast.style.opacity = '0';
                    toast.style.transform = 'translateX(100%)';
                    toast.style.transition = 'all 0.3s ease';
                    setTimeout(() => toast.remove(), 300);
                }
            }, 4000);
        }

        function showSuccessModal(message, title = 'Berhasil') {
            const modal = document.getElementById('success-modal-backdrop');
            if (!modal) return;
            document.getElementById('success-modal-title').textContent = title;
            document.getElementById('success-modal-message').textContent = message;
            modal.classList.add('active');
        }

        function askConfirmation(message, callback, title = 'Konfirmasi', btnText = 'Ya, Hapus', btnClass = 'btn-danger') {
            const modal = document.getElementById('confirm-modal-backdrop');
            if (!modal) return;
            const icon = document.getElementById('confirm-modal-icon');
            const yesBtn = document.getElementById('confirm-modal-yes');
            document.getElementById('confirm-modal-title').textContent = title;
            document.getElementById('confirm-modal-message').innerText = message;
            yesBtn.textContent = btnText;
            yesBtn.className = 'btn ' + btnClass;
            if (btnClass === 'btn-danger') {
                icon.style.background = '#fef2f2';
                icon.style.color = '#d92d20';
                icon.textContent = '!';
            } else {
                icon.style.background = '#eff6ff';
                icon.style.color = '#1d4ed8';
                icon.textContent = '?';
            }
            modal.classList.add('active');
            confirmCallback = callback;
        }

        function updateClock() {
            const now = new Date();
            const optionsDate = {
                weekday: 'long',
                year: 'numeric',
                month: 'long',
                day: 'numeric'
            };
            const dateStr = now.toLocaleDateString('id-ID', optionsDate);
            const timeStr = now.toLocaleTimeString('id-ID', {
                hour12: false,
                hour: '2-digit',
                minute: '2-digit',
                second: '2-digit'
            }).replace(/\./g, ':');

            const dateEl = document.getElementById('clock-date');
            const timeEl = document.getElementById('clock-time');

            if (dateEl) dateEl.textContent = dateStr;
            if (timeEl) timeEl.textContent = timeStr;
        }

        document.addEventListener('DOMContentLoaded', function() {
            updateClock();
            setInterval(updateClock, 1000);

            // Sidebar Toggle
            const toggle = document.getElementById('sidebar-toggle');
            const sidebar = document.getElementById('app-sidebar');
            if (toggle && sidebar) {
                toggle.addEventListener('click', () => sidebar.classList.toggle('open'));
            }
        });
    </script>
</head>

<body>
    <div class="app">
        <aside class="sidebar" id="app-sidebar">
            <div class="brand">TOKO AL-HARIST</div>
            <nav class="menu">
                <?php $currentRoute = $_GET['route'] ?? ''; ?>
                <a class="<?= active_menu('dashboard', $currentRoute) ?>" href="index.php?route=dashboard">Dashboard</a>
                <a class="<?= active_menu('barang', $currentRoute) ?>" href="index.php?route=barang">Master Barang</a>
                <a class="<?= active_menu('esaldo', $currentRoute) ?>" href="index.php?route=esaldo">Master E-Saldo</a>
                <a class="<?= active_menu('pelanggan', $currentRoute) ?>" href="index.php?route=pelanggan">Master Pelanggan</a>
                <a class="<?= active_menu('transaksi', $currentRoute) ?>" href="index.php?route=transaksi">Transaksi</a>
                <a class="<?= active_menu('transaksi/list', $currentRoute) ?>" href="index.php?route=transaksi/list">List Transaksi</a>
                <a class="<?= active_menu('stok/receive', $currentRoute) ?>" href="index.php?route=stok/receive">Receive Item</a>
                <a class="<?= active_menu('stok/opname', $currentRoute) ?>" href="index.php?route=stok/opname">Stok Opname</a>
                <a class="<?= active_menu('keuangan/hutang', $currentRoute) ?>" href="index.php?route=keuangan/hutang">Utang</a>
                <a class="<?= active_menu('keuangan/brankas', $currentRoute) ?>" href="index.php?route=keuangan/brankas">Brankas</a>
                <a class="<?= active_menu('importexport', $currentRoute) ?>" href="index.php?route=importexport">Import Export</a>
            </nav>
        </aside>
        <main class="content">
            <div class="topbar">
                <div class="topbar-left">
                    <div class="topbar-header">
                        <button type="button" class="sidebar-toggle" id="sidebar-toggle" aria-label="Buka menu">Menu</button>
                        <div style="font-size:22px;font-weight:700;"><?= htmlspecialchars($title ?? 'TOKO AL-HARIST') ?></div>
                    </div>
                    <div class="small" style="color: #667085; font-weight: 500;">
                        <span style="color: var(--red); font-weight: 700;">Toko Al-Harist</span>
                        <span style="margin: 0 4px; color: var(--line);">|</span>
                        Jl. Raya Serang Km.32 RT.09/RW.06 Ds. Sumur Bandung Kec. Jayanti
                    </div>
                </div>
                <div class="topbar-right">
                    <div class="header-clock">
                        <div class="clock-time" id="clock-time"><?= date('H:i:s') ?></div>
                        <div class="clock-date" id="clock-date"><?= date('l, d F Y') ?></div>
                    </div>
                </div>
            </div>
            <div class="toast-container" id="toast-container"></div>

            <div class="modal-backdrop" id="success-modal-backdrop">
                <div class="modal modal-success">
                    <div class="icon">✓</div>
                    <h2 id="success-modal-title">Berhasil</h2>
                    <p id="success-modal-message">Transaksi telah berhasil disimpan.</p>
                    <button type="button" class="btn btn-success" onclick="document.getElementById('success-modal-backdrop').classList.remove('active')">Tutup</button>
                </div>
            </div>

            <div class="modal-backdrop" id="confirm-modal-backdrop">
                <div class="modal modal-confirm">
                    <div class="icon" id="confirm-modal-icon">!</div>
                    <h2 id="confirm-modal-title">Konfirmasi</h2>
                    <p id="confirm-modal-message">Apakah Anda yakin?</p>
                    <div class="confirm-actions">
                        <button type="button" class="btn btn-secondary" onclick="document.getElementById('confirm-modal-backdrop').classList.remove('active')">Batal</button>
                        <button type="button" class="btn btn-danger" id="confirm-modal-yes">Ya, Lanjutkan</button>
                    </div>
                </div>
            </div>
            <script>
                document.getElementById('confirm-modal-yes').addEventListener('click', function() {
                    if (confirmCallback) confirmCallback();
                    document.getElementById('confirm-modal-backdrop').classList.remove('active');
                });
            </script>