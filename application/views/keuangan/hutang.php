<style>
    .debt-table-wrap {
        overflow-x: auto;
    }

    .debt-table {
        width: 100%;
    }

    .debt-pay-form {
        display: grid;
        grid-template-columns: 140px 1fr 1fr 1fr auto;
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
    <h3>Tambah Hutang Manual</h3>
    <form method="post" class="form-grid" style="margin-bottom:18px;">
        <input type="hidden" name="action" value="create_manual_debt">
        <div>
            <div class="small">Pelanggan</div>
            <select name="customer_id" required>
                <option value="0">Pilih Pelanggan</option>
                <?php foreach (($customers ?? []) as $customer): ?>
                    <option value="<?= (int) $customer['id'] ?>"><?= htmlspecialchars((string) $customer['name']) ?></option>
                <?php endforeach; ?>
            </select>
        </div>
        <div>
            <div class="small">Total Hutang</div>
            <input type="text" name="total_debt" class="debt-payment-amount" placeholder="Nominal hutang" required>
        </div>
        <div>
            <div class="small">Jatuh Tempo</div>
            <input type="date" name="due_date">
        </div>
        <div>
            <div class="small">Catatan</div>
            <input name="notes" placeholder="Catatan hutang manual">
        </div>
        <div style="grid-column:1 / -1;">
            <button type="submit" class="btn-green">Simpan Hutang</button>
        </div>
    </form>
</div>

<div class="card">
    <h3>Daftar Utang Pelanggan</h3>
    <form method="get" style="display:grid; grid-template-columns:minmax(0,1fr) auto; gap:12px; align-items:end; margin:16px 0;">
        <input type="hidden" name="route" value="keuangan/hutang">
        <div>
            <div class="small">Cari Utang</div>
            <input type="text" name="q" value="<?= htmlspecialchars((string) ($keyword ?? '')) ?>" placeholder="Cari invoice, pelanggan, atau status">
        </div>
        <div class="search-reset-actions">
            <button type="submit" class="btn btn-secondary">Search</button>
            <a href="index.php?route=keuangan/hutang" class="btn btn-info">Reset</a>
        </div>
    </form>
    <div class="debt-table-wrap">
        <table class="debt-table">
            <thead><tr><th>Invoice</th><th>Pelanggan</th><th>Total</th><th>Terbayar</th><th>Sisa</th><th>Status</th><th>Bayar</th></tr></thead>
            <tbody>
            <?php foreach ($debts as $debt): ?>
                <tr>
                    <td data-label="Invoice"><?= htmlspecialchars((string) $debt['invoice_no']) ?></td>
                    <td data-label="Pelanggan"><?= htmlspecialchars((string) $debt['customer_name']) ?></td>
                    <td data-label="Total"><?= rupiah((float) $debt['total_debt']) ?></td>
                    <td data-label="Terbayar"><?= rupiah((float) $debt['paid_amount']) ?></td>
                    <td data-label="Sisa">
                        <?= rupiah((float) ($debt['remaining_debt'] ?? 0)) ?>
                        <?php if (!empty($debt['due_date'])): ?>
                            <div class="small">Jatuh tempo: <?= htmlspecialchars((string) $debt['due_date']) ?></div>
                        <?php endif; ?>
                    </td>
                    <td data-label="Status"><?= htmlspecialchars($debt['status']) ?></td>
                    <td data-label="Bayar">
                        <form method="post" class="debt-pay-form" data-remaining="<?= htmlspecialchars((string) (float) ($debt['remaining_debt'] ?? 0)) ?>">
                            <input type="hidden" name="debt_id" value="<?= (int) $debt['id'] ?>">
                            <select name="payment_mode" class="debt-payment-mode">
                                <option value="partial">Cicilan</option>
                                <option value="full">Lunas</option>
                            </select>
                            <select name="vault_id" required>
                                <option value="0">Pilih Brankas</option>
                                <?php foreach (($vaults ?? []) as $vault): ?>
                                    <option value="<?= (int) $vault['id'] ?>"><?= htmlspecialchars((string) $vault['bank_name']) ?></option>
                                <?php endforeach; ?>
                            </select>
                            <input type="text" name="amount" class="debt-payment-amount" placeholder="Nominal bayar" required>
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
<script>
    (function () {
        function formatInputNumber(value) {
            const digits = String(value || '').replace(/[^\d]/g, '');
            return digits === '' ? '' : Number(digits).toLocaleString('id-ID');
        }

        document.querySelectorAll('.debt-payment-amount').forEach(function (input) {
            input.addEventListener('input', function () {
                this.value = formatInputNumber(this.value);
            });
        });

        document.querySelectorAll('.debt-pay-form').forEach(function (form) {
            const mode = form.querySelector('.debt-payment-mode');
            const amount = form.querySelector('.debt-payment-amount');
            const remaining = parseFloat(form.dataset.remaining || '0');

            function syncPaymentMode() {
                if (!mode || !amount) {
                    return;
                }

                if (mode.value === 'full') {
                    amount.value = formatInputNumber(remaining);
                    amount.readOnly = true;
                    amount.style.background = '#f5f7fa';
                } else {
                    amount.readOnly = false;
                    amount.style.background = '#fff';
                    if (amount.value === formatInputNumber(remaining)) {
                        amount.value = '';
                    }
                }
            }

            if (mode) {
                mode.addEventListener('change', syncPaymentMode);
                syncPaymentMode();
            }
        });
    }());
</script>
