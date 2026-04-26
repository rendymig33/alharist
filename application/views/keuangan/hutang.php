<style>
    .debt-table-wrap {
        overflow-x: auto;
    }

    .debt-table {
        width: 100%;
    }

    .debt-pay-form {
        display: grid;
        grid-template-columns: 1fr 1fr auto;
        gap: 8px;
    }

    @media (max-width: 640px) {
        .debt-table thead {
            display: none;
        }

        .debt-table,
        .debt-table tbody,
        .debt-table tr,
        .debt-table td {
            display: block;
            width: 100%;
        }

        .debt-table tr {
            padding: 10px 0;
            border-bottom: 1px solid var(--line);
        }

        .debt-table tr:last-child {
            border-bottom: none;
        }

        .debt-table td {
            border-bottom: none;
            padding: 8px 0;
        }

        .debt-table td::before {
            content: attr(data-label);
            display: block;
            margin-bottom: 4px;
            font-size: 11px;
            font-weight: 800;
            letter-spacing: .05em;
            text-transform: uppercase;
            color: #98a2b3;
        }

        .debt-pay-form {
            grid-template-columns: 1fr;
        }
    }
</style>
<div class="card">
    <h3>Daftar Utang Pelanggan</h3>
    <div class="debt-table-wrap">
        <table class="debt-table">
            <thead><tr><th>Invoice</th><th>Pelanggan</th><th>Total</th><th>Terbayar</th><th>Status</th><th>Bayar</th></tr></thead>
            <tbody>
            <?php foreach ($debts as $debt): ?>
                <tr>
                    <td data-label="Invoice"><?= htmlspecialchars((string) $debt['invoice_no']) ?></td>
                    <td data-label="Pelanggan"><?= htmlspecialchars((string) $debt['customer_name']) ?></td>
                    <td data-label="Total"><?= rupiah((float) $debt['total_debt']) ?></td>
                    <td data-label="Terbayar"><?= rupiah((float) $debt['paid_amount']) ?></td>
                    <td data-label="Status"><?= htmlspecialchars($debt['status']) ?></td>
                    <td data-label="Bayar">
                        <form method="post" class="debt-pay-form">
                            <input type="hidden" name="debt_id" value="<?= (int) $debt['id'] ?>">
                            <input type="number" name="amount" placeholder="Nominal" required>
                            <input name="notes" placeholder="Catatan">
                            <button class="btn-green" type="submit">Simpan</button>
                        </form>
                    </td>
                </tr>
            <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>
