<style>
    .vault-table-wrap,
    .vault-history-wrap {
        overflow-x: auto;
    }

    .vault-table,
    .vault-history-table {
        width: 100%;
    }

    .vault-transaction-modal {
        width: min(980px, 100%);
    }

    .vault-transaction-grid {
        display: grid;
        grid-template-columns: repeat(2, minmax(0, 1fr));
        gap: 12px;
    }

    @media (max-width: 920px) {
        .vault-transaction-modal {
            width: min(100%, 100%);
        }
    }

    @media (max-width: 720px) {
        .vault-transaction-grid {
            grid-template-columns: 1fr;
        }

        .toolbar {
            flex-direction: column;
            align-items: stretch;
        }

        .toolbar .btn {
            width: 100%;
        }

        .action-row {
            flex-wrap: wrap;
        }

        .action-row .btn {
            flex: 1 1 140px;
        }
    }

    @media (max-width: 640px) {
        .vault-table thead,
        .vault-history-table thead {
            display: none;
        }

        .vault-table,
        .vault-table tbody,
        .vault-table tr,
        .vault-table td,
        .vault-history-table,
        .vault-history-table tbody,
        .vault-history-table tr,
        .vault-history-table td {
            display: block;
            width: 100%;
        }

        .vault-table tr,
        .vault-history-table tr {
            padding: 10px 0;
            border-bottom: 1px solid var(--line);
        }

        .vault-table tr:last-child,
        .vault-history-table tr:last-child {
            border-bottom: none;
        }

        .vault-table td,
        .vault-history-table td {
            border-bottom: none;
            padding: 8px 0;
        }

        .vault-table td::before,
        .vault-history-table td::before {
            content: attr(data-label);
            display: block;
            margin-bottom: 4px;
            font-size: 11px;
            font-weight: 800;
            letter-spacing: .05em;
            text-transform: uppercase;
            color: #98a2b3;
        }

        .vault-transaction-modal .card {
            padding: 14px;
        }

        .vault-transaction-modal .modal-head {
            align-items: flex-start;
        }
    }
</style>
<div class="toolbar">
    <div class="small">Daftar rekening / bank / dompet digital.</div>
    <button type="button" class="btn" onclick="toggleBrankasModal(true)">Add Brankas</button>
</div>

<div class="card">
    <h3>Daftar Brankas</h3>
    <div class="vault-table-wrap">
        <table class="vault-table">
            <thead><tr><th>BANK</th><th>Keterangan</th><th>Saldo</th><th>Aksi</th></tr></thead>
            <tbody>
            <?php foreach ($vaults as $vault): ?>
                <tr>
                    <td data-label="Bank"><?= htmlspecialchars($vault['bank_name']) ?></td>
                    <td data-label="Keterangan"><?= htmlspecialchars((string) $vault['account_name']) ?></td>
                    <td data-label="Saldo"><?= rupiah((float) $vault['balance']) ?></td>
                    <td data-label="Aksi">
                        <div class="action-row">
                            <button type="button" class="btn btn-secondary" onclick="toggleVaultTransactionModal(<?= (int) $vault['id'] ?>, true)">Transaksi</button>
                            <a class="btn btn-secondary" href="index.php?route=keuangan/brankas&edit=<?= (int) $vault['id'] ?>">Edit</a>
                        </div>
                    </td>
                </tr>
            <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>

<div class="modal-backdrop <?= !empty($editVault) ? 'active' : '' ?>" id="brankas-modal">
    <div class="modal">
        <div class="modal-head">
            <h3 style="margin:0;"><?= !empty($editVault) ? 'Edit Brankas' : 'Add Brankas' ?></h3>
            <button type="button" class="modal-close" onclick="toggleBrankasModal(false)">Tutup</button>
        </div>
        <form method="post">
            <input type="hidden" name="id" value="<?= htmlspecialchars((string) ($editVault['id'] ?? '')) ?>">
            <div class="form-grid">
                <div><div class="small">Nama Bank / Wallet</div><input name="bank_name" placeholder="Contoh: Mandiri / QRIS / DANA" value="<?= htmlspecialchars((string) ($editVault['bank_name'] ?? '')) ?>" required></div>
                <div><div class="small">Keterangan</div><input name="account_name" placeholder="Nama rekening / catatan" value="<?= htmlspecialchars((string) ($editVault['account_name'] ?? '')) ?>"></div>
                <div><div class="small">Saldo</div><input class="money-input" name="balance" type="text" placeholder="Saldo awal" value="<?= htmlspecialchars(number_format((float) ($editVault['balance'] ?? 0), 0, ',', '.')) ?>"></div>
            </div>
            <div style="margin-top:12px;"><button type="submit">Simpan Brankas</button></div>
        </form>
    </div>
</div>
<?php foreach ($vaults as $vault): ?>
    <div class="modal-backdrop" id="vault-transaction-modal-<?= (int) $vault['id'] ?>">
        <div class="modal vault-transaction-modal">
            <div class="modal-head">
                <h3 style="margin:0;">Transaksi <?= htmlspecialchars($vault['bank_name'] . (!empty($vault['account_name']) ? ' - ' . $vault['account_name'] : '')) ?></h3>
                <button type="button" class="modal-close" onclick="toggleVaultTransactionModal(<?= (int) $vault['id'] ?>, false)">Tutup</button>
            </div>
            <form method="post">
                <input type="hidden" name="action" value="save_transaction">
                <div class="vault-transaction-grid">
                    <div>
                        <div class="small">Jenis Transaksi</div>
                        <select name="transaction_type" id="transaction_type_<?= (int) $vault['id'] ?>" data-vault-id="<?= (int) $vault['id'] ?>" required>
                            <option value="switching_dana">Switching Dana</option>
                            <option value="pembelian">Pembelian</option>
                            <option value="dana_masuk">Dana Masuk</option>
                        </select>
                    </div>
                    <div>
                        <div class="small">Nominal</div>
                        <input class="money-input" name="amount" type="text" placeholder="Nominal transaksi" required>
                    </div>
                    <div id="source-vault-group-<?= (int) $vault['id'] ?>">
                        <div class="small">Dari Brankas</div>
                        <select name="source_vault_id">
                            <option value="0">Pilih Brankas Sumber</option>
                            <?php foreach ($vaults as $sourceVault): ?>
                                <option value="<?= (int) $sourceVault['id'] ?>" <?= (int) $sourceVault['id'] === (int) $vault['id'] ? 'selected' : '' ?>><?= htmlspecialchars($sourceVault['bank_name'] . (!empty($sourceVault['account_name']) ? ' - ' . $sourceVault['account_name'] : '')) ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div id="target-vault-group-<?= (int) $vault['id'] ?>">
                        <div class="small">Ke Brankas</div>
                        <select name="target_vault_id">
                            <option value="0">Pilih Brankas Tujuan</option>
                            <?php foreach ($vaults as $targetVault): ?>
                                <option value="<?= (int) $targetVault['id'] ?>" <?= (int) $targetVault['id'] === (int) $vault['id'] ? 'selected' : '' ?>><?= htmlspecialchars($targetVault['bank_name'] . (!empty($targetVault['account_name']) ? ' - ' . $targetVault['account_name'] : '')) ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div style="grid-column:1 / -1;">
                        <div class="small">Catatan</div>
                        <input name="notes" placeholder="Catatan transaksi">
                    </div>
                </div>
                <div style="margin-top:12px;"><button type="submit">Simpan Transaksi</button></div>
            </form>

            <div class="card" style="margin-top:18px;">
                <h3>History Transaksi</h3>
                <div class="vault-history-wrap">
                    <table class="vault-history-table">
                        <thead><tr><th>Tanggal</th><th>Jenis</th><th>Dari</th><th>Ke</th><th>Nominal</th><th>Catatan</th></tr></thead>
                        <tbody>
                        <?php foreach (($transactionsByVault[(int) $vault['id']] ?? []) as $transaction): ?>
                            <?php
                            $typeLabel = match ($transaction['transaction_type']) {
                                'switching_dana' => 'Switching Dana',
                                'pembelian' => 'Pembelian',
                                'dana_masuk' => 'Dana Masuk',
                                'penjualan' => 'Transaksi Penjualan',
                                default => $transaction['transaction_type'],
                            };
                            $sourceLabel = trim((string) (($transaction['source_bank_name'] ?? '') . (!empty($transaction['source_account_name']) ? ' - ' . $transaction['source_account_name'] : '')));
                            $targetLabel = trim((string) (($transaction['target_bank_name'] ?? '') . (!empty($transaction['target_account_name']) ? ' - ' . $transaction['target_account_name'] : '')));
                            ?>
                            <tr>
                                <td data-label="Tanggal"><?= htmlspecialchars((string) $transaction['transaction_date']) ?></td>
                                <td data-label="Jenis"><?= htmlspecialchars($typeLabel) ?></td>
                                <td data-label="Dari"><?= htmlspecialchars($sourceLabel !== '' ? $sourceLabel : '-') ?></td>
                                <td data-label="Ke"><?= htmlspecialchars($targetLabel !== '' ? $targetLabel : '-') ?></td>
                                <td data-label="Nominal"><?= rupiah((float) $transaction['amount']) ?></td>
                                <td data-label="Catatan"><?= htmlspecialchars((string) ($transaction['notes'] ?: '-')) ?></td>
                            </tr>
                        <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
<?php endforeach; ?>
<script>
    function toggleBrankasModal(show) {
        document.getElementById('brankas-modal').classList.toggle('active', show);
    }
    function toggleVaultTransactionModal(vaultId, show) {
        document.getElementById('vault-transaction-modal-' + vaultId).classList.toggle('active', show);
    }
    (function () {
        document.querySelectorAll('.money-input').forEach(function (input) {
            input.addEventListener('input', function () {
                const digits = this.value.replace(/[^\d]/g, '');
                this.value = digits === '' ? '' : Number(digits).toLocaleString('id-ID');
            });
        });

        document.querySelectorAll('[id^=\"transaction_type_\"]').forEach(function (transactionType) {
            const vaultId = transactionType.dataset.vaultId;
            const sourceGroup = document.getElementById('source-vault-group-' + vaultId);
            const targetGroup = document.getElementById('target-vault-group-' + vaultId);

            function syncTransactionForm() {
                if (!sourceGroup || !targetGroup) {
                    return;
                }

                const type = transactionType.value;
                sourceGroup.style.display = type === 'dana_masuk' ? 'none' : '';
                targetGroup.style.display = type === 'pembelian' ? 'none' : '';
            }

            transactionType.addEventListener('change', syncTransactionForm);
            syncTransactionForm();
        });
    }());
</script>
