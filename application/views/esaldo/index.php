<style>
    .esaldo-table-wrap {
        overflow-x: auto;
    }

    .esaldo-table {
        width: 100%;
    }

    @media (max-width: 640px) {
        .esaldo-table thead {
            display: none;
        }

        .esaldo-table,
        .esaldo-table tbody,
        .esaldo-table tr,
        .esaldo-table td {
            display: block;
            width: 100%;
        }

        .esaldo-table tr {
            padding: 10px 0;
            border-bottom: 1px solid var(--line);
        }

        .esaldo-table tr:last-child {
            border-bottom: none;
        }

        .esaldo-table td {
            border-bottom: none;
            padding: 8px 0;
        }

        .esaldo-table td::before {
            content: attr(data-label);
            display: block;
            margin-bottom: 4px;
            font-size: 11px;
            font-weight: 800;
            letter-spacing: .05em;
            text-transform: uppercase;
            color: #98a2b3;
        }
    }
</style>
<div class="toolbar">
    <div class="small">Master modal dasar untuk jual pulsa dan transaksi digital.</div>
</div>

<div class="card">
    <h3>Saldo Modal</h3>
    <div style="font-size: 24px; font-weight: bold; color: #333; margin: 16px 0;">
        <?= rupiah($modalBalance ?? 0) ?>
    </div>
    <p>Saldo modal ini berkurang sesuai dengan transaksi E-Saldo yang dilakukan.</p>
</div>