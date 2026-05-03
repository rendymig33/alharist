<style>
    /* Pagination */
    .pagination-wrap {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-top: 24px;
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

    .btn-pagination {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        padding: 8px 16px;
        border-radius: 10px;
        font-weight: 600;
        transition: all 0.2s;
        text-decoration: none;
        border: 1px solid var(--line);
        background: #fff;
        color: #344054;
    }

    .btn-pagination:hover:not(:disabled) {
        background: #f9fafb;
        transform: translateY(-1px);
        box-shadow: 0 4px 12px rgba(0,0,0,0.05);
    }

    .vault-history-wrap {
        overflow-x: auto;
    }

    .vault-summary-grid {
        display: grid;
        grid-template-columns: repeat(2, minmax(0, 1fr));
        gap: 14px;
        margin: 18px 0;
    }

    .vault-card-grid {
        display: grid;
        grid-template-columns: repeat(3, minmax(0, 1fr));
        gap: 14px;
    }

    .vault-history-table th,
    .vault-history-table td {
        font-size: 13px;
    }

    .vault-history-table tbody tr:nth-child(even) {
        background: #fcfcfd;
    }

    .vault-card {
        background: linear-gradient(135deg, #ffffff, #fffaf0);
        border: 1px solid var(--line);
        border-radius: 18px;
        padding: 16px;
        box-shadow: 0 12px 24px rgba(28, 39, 60, .05);
    }

    .vault-card-head {
        display: flex;
        justify-content: space-between;
        gap: 10px;
        align-items: flex-start;
        margin-bottom: 14px;
    }

    .vault-card-balance {
        font-size: 28px;
        font-weight: 800;
        line-height: 1.15;
        margin-bottom: 12px;
    }

    .vault-card .action-row .btn,
    .vault-card .action-row button {
        flex: 1 1 120px;
    }

    .vault-search {
        display: grid;
        grid-template-columns: minmax(0, 1fr) auto;
        gap: 12px;
        align-items: end;
        margin: 16px 0;
    }

    .vault-table,
    .vault-history-table {
        width: 100%;
    }

    .flow-badge {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        min-width: 82px;
        padding: 6px 10px;
        border-radius: 999px;
        font-size: 12px;
        font-weight: 800;
    }

    .flow-badge.debet {
        background: #ecfdf3;
        color: #027a48;
    }

    .flow-badge.kredit {
        background: #fff1f1;
        color: #b42318;
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

        .vault-card-grid {
            grid-template-columns: repeat(2, minmax(0, 1fr));
        }
    }

    @media (max-width: 720px) {
        .vault-transaction-grid {
            grid-template-columns: 1fr;
        }

        .vault-card-grid {
            grid-template-columns: 1fr;
        }

        .vault-search {
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
<?php 
$vaults = $vaults ?? []; 
$currentPage = $currentPage ?? 1;
$totalPages = $totalPages ?? 1;
?>
<div class="toolbar">
    <div class="small">Daftar rekening / bank / dompet digital.</div>
    <button type="button" class="btn" onclick="toggleBrankasModal(true)">Add Brankas</button>
</div>

<div class="card">
    <h3>Daftar Brankas</h3>
    <div class="vault-summary-grid">
        <div class="detail-box">
            <div class="small">Saldo Keseluruhan</div>
            <strong style="font-size:24px; display:block; margin-top:6px;"><?= rupiah((float) ($totalBalance ?? 0)) ?></strong>
        </div>
        <div class="detail-box">
            <div class="small">Saldo Hasil Filter</div>
            <strong style="font-size:24px; display:block; margin-top:6px;"><?= rupiah((float) ($filteredBalance ?? 0)) ?></strong>
        </div>
    </div>
    <form method="get" class="vault-search">
        <input type="hidden" name="route" value="keuangan/brankas">
        <div>
            <div class="small">Cari Brankas</div>
            <input type="text" name="q" value="<?= htmlspecialchars((string) ($keyword ?? '')) ?>" placeholder="Cari bank atau keterangan">
        </div>
        <div class="search-reset-actions">
            <button type="submit" class="btn btn-secondary">Search</button>
            <a href="index.php?route=keuangan/brankas" class="btn btn-info">Reset</a>
        </div>
    </form>
    <div class="vault-card-grid">
        <?php foreach ($vaults as $vault): ?>
            <div class="vault-card">
                <div class="vault-card-head">
                    <div>
                        <div class="section-title" style="margin-bottom:6px;"><?= htmlspecialchars((string) $vault['bank_name']) ?></div>
                    </div>
                </div>
                <div class="vault-card-balance" id="vault-balance-display-<?= (int) $vault['id'] ?>" data-balance="<?= (float) $vault['balance'] ?>">
                    <?= rupiah((float) $vault['balance']) ?>
                </div>
                <div class="small" style="margin-bottom:14px;">
                    Saldo aktif pada brankas ini.
                    <div id="vault-selisih-summary-<?= (int) $vault['id'] ?>" style="margin-top: 6px; font-weight: 800;"></div>
                </div>
                <div class="action-row">
                    <button type="button" class="btn btn-secondary" onclick="togglePecahanModal(<?= (int) $vault['id'] ?>, true)">Pecahan</button>
                    <button type="button" class="btn btn-secondary" onclick="toggleVaultTransactionModal(<?= (int) $vault['id'] ?>, true)">Transaksi</button>
                    <a class="btn btn-secondary" href="index.php?route=keuangan/brankas&edit=<?= (int) $vault['id'] ?>">Edit</a>
                </div>
            </div>
        <?php endforeach; ?>
    </div>

    <?php if ($totalPages > 1): ?>
        <div class="pagination-wrap">
            <div class="pagination-info">
                Halaman <?= $currentPage ?> dari <?= $totalPages ?>
            </div>
            <div class="pagination-btns" style="display:flex; gap:8px;">
                <?php
                $prevParams = ['route' => 'keuangan/brankas', 'p' => $currentPage - 1];
                if (!empty($keyword)) $prevParams['q'] = $keyword;
                $nextParams = ['route' => 'keuangan/brankas', 'p' => $currentPage + 1];
                if (!empty($keyword)) $nextParams['q'] = $keyword;
                ?>

                <?php if ($currentPage > 1): ?>
                    <a href="index.php?<?= http_build_query($prevParams) ?>" class="btn-pagination">
                        Prev
                    </a>
                <?php endif; ?>

                <?php if ($currentPage < $totalPages): ?>
                    <a href="index.php?<?= http_build_query($nextParams) ?>" class="btn-pagination">
                        Next
                    </a>
                <?php endif; ?>
            </div>
        </div>
    <?php endif; ?>
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
                <div>
                    <div class="small">Nama Bank / Wallet</div><input name="bank_name" placeholder="Contoh: Mandiri / QRIS / DANA" value="<?= htmlspecialchars((string) ($editVault['bank_name'] ?? '')) ?>" required>
                </div>
                <div>
                    <div class="small">Saldo</div><input class="money-input" name="balance" type="text" placeholder="Saldo awal" value="<?= htmlspecialchars(number_format((float) ($editVault['balance'] ?? 0), 0, ',', '.')) ?>">
                </div>
            </div>
            <div style="margin-top:12px;"><button type="submit" class="btn btn-success">Simpan Brankas</button></div>
        </form>
    </div>
</div>
<?php foreach ($vaults as $vault): ?>
    <div class="modal-backdrop <?= (int) ($activeTransactionVaultId ?? 0) === (int) $vault['id'] ? 'active' : '' ?>" id="vault-transaction-modal-<?= (int) $vault['id'] ?>">
        <div class="modal vault-transaction-modal">
            <div class="modal-head">
                <h3 style="margin:0;">Transaksi <?= htmlspecialchars((string) $vault['bank_name']) ?></h3>
                <button type="button" class="modal-close" onclick="toggleVaultTransactionModal(<?= (int) $vault['id'] ?>, false)">Tutup</button>
            </div>
            <div class="detail-box" style="margin-bottom:18px;">
                <div class="small">Saldo Brankas Saat Ini</div>
                <strong style="font-size:24px; display:block; margin-top:6px;"><?= rupiah((float) $vault['balance']) ?></strong>
            </div>
            <form method="post" class="vault-transaction-form" data-vault-id="<?= (int) $vault['id'] ?>">
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
                                <option value="<?= (int) $sourceVault['id'] ?>" <?= (int) $sourceVault['id'] === (int) $vault['id'] ? 'selected' : '' ?>><?= htmlspecialchars((string) $sourceVault['bank_name']) ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div id="target-vault-group-<?= (int) $vault['id'] ?>">
                        <div class="small">Ke Brankas</div>
                        <select name="target_vault_id">
                            <option value="0">Pilih Brankas Tujuan</option>
                            <?php foreach ($vaults as $targetVault): ?>
                                <option value="<?= (int) $targetVault['id'] ?>" <?= (int) $targetVault['id'] === (int) $vault['id'] ? 'selected' : '' ?>><?= htmlspecialchars((string) $targetVault['bank_name']) ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div style="grid-column:1 / -1;">
                        <div class="small">Catatan</div>
                        <input name="notes" placeholder="Catatan transaksi">
                    </div>
                </div>
                <div style="margin-top:12px;"><button type="submit" class="btn btn-success">Simpan Transaksi</button></div>
            </form>

            <div class="card" style="margin-top:18px;">
                <h3>History Transaksi</h3>
                <div class="bca-ledger-wrap">
                    <table class="bca-ledger">
                        <thead>
                            <tr>
                                <th>Tanggal</th>
                                <th>Keterangan</th>
                                <th style="text-align:right;">Mutasi</th>
                                <th style="text-align:right;">Saldo</th>
                                <th style="text-align:right;">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach (($transactionsByVault[(int) $vault['id']] ?? []) as $transaction): ?>
                                <?php
                                $typeLabel = match ($transaction['transaction_type']) {
                                    'switching_dana' => 'SWITCHING',
                                    'pembelian' => 'PEMBELIAN',
                                    'dana_masuk' => 'DANA MASUK',
                                    'penjualan' => 'PENJUALAN',
                                    'pelunasan_hutang' => 'PELUNASAN HTG',
                                    default => strtoupper((string) $transaction['transaction_type']),
                                };
                                $sourceLabel = trim((string) ($transaction['source_bank_name'] ?? ''));
                                $targetLabel = trim((string) ($transaction['target_bank_name'] ?? ''));
                                
                                $isDebit = (float)($transaction['debet'] ?? 0) > 0;
                                $amount = $isDebit ? (float)$transaction['debet'] : (float)($transaction['kredit'] ?? 0);
                                ?>
                                <tr>
                                    <td class="date" data-label="Tanggal"><?= htmlspecialchars((string) $transaction['transaction_date']) ?></td>
                                    <td class="desc" data-label="Keterangan">
                                        <span class="desc-main"><?= htmlspecialchars($typeLabel) ?></span>
                                        <span class="desc-sub"><?= htmlspecialchars((string) ($transaction['notes'] ?: 'Tanpa catatan')) ?></span>
                                        <?php if ($sourceLabel !== '' || $targetLabel !== ''): ?>
                                            <span class="desc-sub"><?= htmlspecialchars($sourceLabel) ?> Ke <?= htmlspecialchars($targetLabel) ?></span>
                                        <?php endif; ?>
                                    </td>
                                    <td class="amount <?= $isDebit ? 'db' : 'cr' ?>" data-label="Mutasi">
                                        <?= number_format($amount, 0, ',', '.') ?>
                                        <span class="type-label <?= $isDebit ? 'type-db' : 'type-cr' ?>"><?= $isDebit ? 'DB' : 'CR' ?></span>
                                    </td>
                                    <td class="balance" data-label="Saldo">
                                        <?= number_format((float) ($transaction['ending_balance'] ?? 0), 0, ',', '.') ?>
                                    </td>
                                    <td style="text-align:right;" data-label="Aksi">
                                        <?php if (($transaction['source_module'] ?? '') === 'manual'): ?>
                                            <button type="button" class="btn btn-danger delete-transaction-btn" style="padding: 4px 8px; font-size: 11px;" data-transaction-id="<?= (int) $transaction['id'] ?>" data-vault-id="<?= (int) $vault['id'] ?>">Delete</button>
                                        <?php else: ?>
                                            <span class="small" style="font-size:10px;"><?= htmlspecialchars((string) $transaction['source_module']) ?></span>
                                        <?php endif; ?>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
    <div class="modal-backdrop" id="pecahan-modal-<?= (int) $vault['id'] ?>">
        <div class="modal" style="width: min(400px, 100%);">
            <div class="modal-head">
                <h3 style="margin:0;">Hitung Pecahan Uang</h3>
                <button type="button" class="modal-close" onclick="togglePecahanModal(<?= (int) $vault['id'] ?>, false)">Tutup</button>
            </div>
            <div class="small" style="margin-bottom: 15px; padding-bottom: 10px; border-bottom: 1px solid var(--line);">Saldo Sistem: <strong id="saldo-sistem-<?= (int) $vault['id'] ?>" data-balance="<?= (float) $vault['balance'] ?>"><?= rupiah((float) $vault['balance']) ?></strong></div>
            
            <div class="pecahan-container" id="pecahan-container-<?= (int) $vault['id'] ?>">
                <?php 
                $pecahanList = [100000, 50000, 20000, 10000, 5000, 2000, 1000, 500, 200, 100];
                foreach($pecahanList as $p):
                ?>
                <div class="pecahan-row" style="margin-bottom: 8px; display: flex; align-items: center; gap: 10px;">
                    <div class="uang-label" style="width: 70px; font-weight: 600;"><?= number_format($p, 0, ',', '.') ?></div>
                    <div style="font-weight: 600; color: #98a2b3;">x</div>
                    <input type="number" min="0" class="input-lembar" data-nilai="<?= $p ?>" placeholder="0" style="width: 70px; text-align: center; padding: 6px;">
                    <div style="font-weight: 600; color: #98a2b3;">=</div>
                    <div class="subtotal" style="flex: 1; text-align: right; font-weight: 600; color: #344054;">0</div>
                </div>
                <?php endforeach; ?>
            </div>

            <div style="font-size: 18px; font-weight: 800; text-align: right; margin-top: 15px; padding-top: 15px; border-top: 1px dashed var(--line);">
                Total: <span class="total-uang" id="total-uang-<?= (int) $vault['id'] ?>">0</span>
            </div>
            <div style="font-size: 14px; text-align: right; margin-top: 8px; font-weight: bold;">
                Selisih: <span class="selisih-uang" id="selisih-uang-<?= (int) $vault['id'] ?>">0</span>
            </div>
            <div style="margin-top:20px; display: flex; gap: 10px;">
                <button type="button" class="btn btn-secondary" style="flex: 1;" onclick="resetPecahan(<?= (int) $vault['id'] ?>)">Reset</button>
                <button type="button" class="btn btn-success" style="flex: 1;" onclick="simpanPecahan(<?= (int) $vault['id'] ?>)">Simpan</button>
            </div>
        </div>
    </div>
<?php endforeach; ?>
<script>
    function toggleBrankasModal(show) {
        document.getElementById('brankas-modal').classList.toggle('active', show);
    }

    function togglePecahanModal(vaultId, show) {
        document.getElementById('pecahan-modal-' + vaultId).classList.toggle('active', show);
    }

    function toggleVaultTransactionModal(vaultId, show) {
        document.getElementById('vault-transaction-modal-' + vaultId).classList.toggle('active', show);
    }

    function openTambahSaldo(vaultId) {
        toggleVaultTransactionModal(vaultId, true);
        const typeField = document.getElementById('transaction_type_' + vaultId);
        if (typeField) {
            typeField.value = 'dana_masuk';
            typeField.dispatchEvent(new Event('change'));
        }
    }
    (function() {
        document.querySelectorAll('.money-input').forEach(function(input) {
            input.addEventListener('input', function() {
                const digits = this.value.replace(/[^\d]/g, '');
                this.value = digits === '' ? '' : Number(digits).toLocaleString('id-ID');
            });
        });

        // Global function to update vault summary (both card and modal)
        window.updateVaultSummary = function(vaultId) {
            const container = document.getElementById('pecahan-container-' + vaultId);
            if (!container) return;

            const inputs = container.querySelectorAll('.input-lembar');
            const totalEl = document.getElementById('total-uang-' + vaultId);
            const selisihEl = document.getElementById('selisih-uang-' + vaultId);
            const cardSelisihEl = document.getElementById('vault-selisih-summary-' + vaultId);
            const saldoEl = document.getElementById('vault-balance-display-' + vaultId);
            const saldoAwal = parseFloat(saldoEl ? saldoEl.dataset.balance : 0);

            let total = 0;
            let dataToSave = {};
            inputs.forEach(function(inp) {
                const count = parseInt(inp.value) || 0;
                const nilai = parseInt(inp.dataset.nilai) || 0;
                const subtotal = count * nilai;
                const subtotalDisplay = inp.closest('.pecahan-row').querySelector('.subtotal');
                if (subtotalDisplay) subtotalDisplay.textContent = subtotal.toLocaleString('id-ID');
                total += subtotal;
                if (count > 0) dataToSave[nilai] = count;
            });

            const selisih = total - saldoAwal;
            const formattedTotal = total.toLocaleString('id-ID');
            const formattedSelisih = (selisih > 0 ? '+' : '') + selisih.toLocaleString('id-ID');
            const color = selisih > 0 ? '#027a48' : (selisih < 0 ? '#b42318' : '#344054');

            if (totalEl) totalEl.textContent = formattedTotal;
            if (selisihEl) {
                selisihEl.textContent = formattedSelisih;
                selisihEl.style.color = color;
            }

            if (cardSelisihEl) {
                if (total > 0) {
                    cardSelisihEl.textContent = 'Selisih Pecahan: ' + formattedSelisih;
                    cardSelisihEl.style.color = color;
                } else {
                    cardSelisihEl.textContent = '';
                }
            }

            return dataToSave;
        };

        // Setup Pecahan Calculator logic
        document.querySelectorAll('.pecahan-container').forEach(function(container) {
            const vaultId = container.id.replace('pecahan-container-', '');
            const inputs = container.querySelectorAll('.input-lembar');

            // Load saved data from localStorage
            const savedData = localStorage.getItem('pecahan_vault_' + vaultId);
            if (savedData) {
                try {
                    const parsed = JSON.parse(savedData);
                    inputs.forEach(function(inp) {
                        const nilai = inp.dataset.nilai;
                        if (parsed[nilai] !== undefined) {
                            inp.value = parsed[nilai];
                        }
                    });
                } catch (e) {}
            }

            // Initial hitung & update UI
            updateVaultSummary(vaultId);

            inputs.forEach(function(input) {
                input.addEventListener('input', function() {
                    const dataToSave = updateVaultSummary(vaultId);
                    localStorage.setItem('pecahan_vault_' + vaultId, JSON.stringify(dataToSave));
                });
            });
            
            window.resetPecahan = function(id) {
                askConfirmation('Kosongkan hitungan pecahan ini?', function() {
                    const ctr = document.getElementById('pecahan-container-' + id);
                    if (ctr) {
                        ctr.querySelectorAll('.input-lembar').forEach(function(inp) {
                            inp.value = '';
                        });
                        localStorage.removeItem('pecahan_vault_' + id);
                        updateVaultSummary(id);
                    }
                }, 'Reset Pecahan', 'Ya, Kosongkan');
            };

            window.simpanPecahan = function(id) {
                togglePecahanModal(id, false);
                updateVaultSummary(id); // Final sync
                showToast('Data pecahan berhasil disimpan!');
            };
        });

        function setupDeleteHandlers() {
            document.querySelectorAll('.delete-transaction-btn').forEach(function(btn) {
                btn.removeEventListener('click', deleteHandler);
                btn.addEventListener('click', deleteHandler);
            });
        }

        async function deleteHandler(e) {
            const button = this;
            askConfirmation('Hapus transaksi brankas ini?', async function() {
                const transactionId = button.dataset.transactionId;
                const vaultId = button.dataset.vaultId;
                const originalText = button.textContent;
                button.disabled = true;
                button.textContent = 'Menghapus...';

                try {
                    const formData = new FormData();
                    formData.append('action', 'delete_transaction');
                    formData.append('transaction_id', transactionId);
                    formData.append('vault_id', vaultId);

                    const response = await fetch('index.php?route=keuangan/brankas', {
                        method: 'POST',
                        headers: { 'X-Requested-With': 'XMLHttpRequest' },
                        body: formData
                    });

                    const result = await response.json();
                    if (result.success) {
                        showSuccessModal(result.message);
                        button.closest('tr').remove();
                        // Update UI balance
                        const balanceEl = document.querySelector(`.vault-balance[data-id="${vaultId}"]`);
                        if (balanceEl) {
                            const newBalance = result.new_balance;
                            balanceEl.textContent = 'Rp ' + Math.round(newBalance).toLocaleString('id-ID');
                        }
                    } else {
                        showToast(result.message, 'warning');
                        button.disabled = false;
                        button.textContent = originalText;
                    }
                } catch (error) {
                    showToast('Terjadi kesalahan saat menghapus data.', 'warning');
                    button.disabled = false;
                    button.textContent = originalText;
                }
            });
        }

        setupDeleteHandlers();

        document.querySelectorAll('[id^=\"transaction_type_\"]').forEach(function(transactionType) {
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
