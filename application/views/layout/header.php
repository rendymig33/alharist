<!doctype html>
<html lang="id">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?= htmlspecialchars($title ?? $config['app_name']) ?></title>
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
            padding: 18px 22px;
            border-radius: 18px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            border: 1px solid var(--line);
            box-shadow: 0 14px 30px rgba(28, 39, 60, .06);
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
            background: var(--red);
            color: var(--white);
            border: none;
            cursor: pointer;
            display: inline-block;
            text-align: center;
            transition: .2s ease;
        }

        button:hover,
        .btn:hover {
            transform: translateY(-1px);
            box-shadow: 0 10px 20px rgba(215, 25, 32, .16);
        }

        .btn-secondary {
            background: #eef1f6;
            color: var(--ink);
        }

        .btn-info {
            background: var(--soft-blue);
            color: #184e9e;
        }

        .btn-danger {
            background: var(--soft-red);
            color: #b42318;
        }

        .btn-green {
            background: var(--green);
        }

        .form-grid {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 12px;
        }

        .flash {
            padding: 12px 14px;
            border-radius: 10px;
            margin: 16px 0;
        }

        .flash.success {
            background: #e7f8ef;
            color: #0f6a40;
        }

        .flash.warning {
            background: #fff3d9;
            color: #8a5a00;
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
                align-items: flex-start;
                gap: 10px;
                padding: 16px;
            }

            .topbar-header {
                width: 100%;
                display: flex;
                align-items: center;
                justify-content: space-between;
                gap: 12px;
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
    </style>
</head>

<body>
    <div class="app">
        <aside class="sidebar" id="app-sidebar">
            <div class="brand">TOKO AL-HARIST</div>
            <nav class="menu">
                <a class="<?= active_menu('dashboard', $currentRoute) ?>" href="index.php?route=dashboard">Dashboard</a>
                <a class="<?= active_menu('barang', $currentRoute) ?>" href="index.php?route=barang">Master Barang</a>
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
                <div class="topbar-header">
                    <button type="button" class="sidebar-toggle" id="sidebar-toggle" aria-label="Buka menu">Menu</button>
                    <div style="font-size:22px;font-weight:700; flex:1;"><?= htmlspecialchars($title ?? $config['app_name']) ?></div>
                </div>
                <div class="small">Toko Al-Harist | Jl. Raya Serang Km.32 RT.09/RW.06 Ds. Sumur Bandung Kec. Jayanti</div>
                <div class="small"><?= date('d-m-Y H:i') ?></div>
            </div>
            <?php if (!empty($flash)): ?>
                <div class="flash <?= htmlspecialchars($flash['type']) ?>"><?= htmlspecialchars($flash['message']) ?></div>
            <?php endif; ?>
