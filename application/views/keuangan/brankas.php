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
            <strong id="vault-total-balance" style="font-size:24px; display:block; margin-top:6px;"><?= rupiah((float) ($totalBalance ?? 0)) ?></strong>
        </div>
        <div class="detail-box">
            <div class="small">Saldo Hasil Filter</div>
            <strong id="vault-filtered-balance" style="font-size:24px; display:block; margin-top:6px;"><?= rupiah((float) ($filteredBalance ?? 0)) ?></strong>
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
                    <div id="vault-pecahan-summary-<?= (int) $vault['id'] ?>" style="font-size: 14px; font-weight: 600; color: #64748b; margin-top: 4px;"></div>
                </div>
                <div class="small" style="margin-bottom:14px;">
                    Saldo aktif pada brankas ini.
                    <div id="vault-selisih-summary-<?= (int) $vault['id'] ?>" style="margin-top: 4px; font-weight: 700; font-size: 12px;"></div>
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
                <strong id="modal-balance-<?= (int) $vault['id'] ?>" style="font-size:24px; display:block; margin-top:6px;">
                    <?= rupiah((float) $vault['balance']) ?>
                </strong>
                <span id="modal-pecahan-summary-<?= (int) $vault['id'] ?>" style="font-size: 14px; font-weight: 600; color: #64748b; margin-left: 10px;"></span>
                <span id="modal-selisih-summary-<?= (int) $vault['id'] ?>" style="font-size: 13px; font-weight: 700; margin-left: 5px;"></span>
            </div>
            <form method="post" class="vault-transaction-form" data-vault-id="<?= (int) $vault['id'] ?>">
                <input type="hidden" name="action" value="save_transaction">
                <input type="hidden" name="active_vault_id" value="<?= (int) $vault['id'] ?>">
                <div class="form-grid">
                    <div>
                        <div class="small">Jenis Transaksi</div>
                        <select name="transaction_type" class="transaction-type-select" data-vault-id="<?= (int) $vault['id'] ?>">
                            <option value="dana_masuk" selected>Dana Masuk (Lain-lain)</option>
                            <option value="pembelian">Pembelian (Uang Keluar)</option>
                            <option value="switching_dana">Switching Dana</option>
                        </select>
                    </div>
                    <div>
                        <div class="small">Tanggal Transaksi</div>
                        <input type="date" name="transaction_date" value="<?= date('Y-m-d') ?>" required>
                    </div>
                </div>

                <div class="form-grid" style="margin-top:12px;">
                    <div>
                        <div class="small">Nominal</div>
                        <input type="text" name="amount" class="money-input" placeholder="Nominal transaksi" required autocomplete="off">
                    </div>
                </div>
                <div class="vault-transaction-grid">
                    <div id="source-vault-group-<?= (int) $vault['id'] ?>" style="display:none;">
                        <div class="small">Dari Brankas (Terkunci)</div>
                        <select class="form-control" disabled>
                            <option value="<?= (int) $vault['id'] ?>" selected><?= htmlspecialchars((string) $vault['bank_name']) ?></option>
                        </select>
                        <input type="hidden" name="source_vault_id" value="<?= (int) $vault['id'] ?>">
                    </div>
                    <div id="target-vault-group-<?= (int) $vault['id'] ?>" style="display:none;">
                        <div class="small">Ke Brankas (Tujuan Switching)</div>
                        <select name="target_vault_id" class="form-control">
                            <?php foreach ($vaults as $targetVault): ?>
                                <?php if ((int)$targetVault['id'] !== (int)$vault['id']): ?>
                                    <option value="<?= (int) $targetVault['id'] ?>"><?= htmlspecialchars((string) $targetVault['bank_name']) ?></option>
                                <?php endif; ?>
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
                                <th style="text-align:right;">Kredit (Masuk)</th>
                                <th style="text-align:right;">Debet (Keluar)</th>
                                <th style="text-align:right;">Saldo</th>
                                <th style="text-align:right;">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach (($transactionsByVault[(int) $vault['id']] ?? []) as $transaction): ?>
                                <?php 
                                    $vaultId = (int) $vault['id'];
                                    include 'application/views/keuangan/brankas_history_row.php'; 
                                ?>
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
                <div class="pecahan-row" style="margin-bottom: 8px; display: flex; align-items: center; gap: 8px;">
                    <div class="uang-label" style="width: 65px; font-weight: 700; font-size: 13px; color: #475467;"><?= number_format($p, 0, ',', '.') ?></div>
                    <div style="font-weight: 600; color: #98a2b3; font-size: 10px;">x</div>
                    <input type="number" min="0" class="input-lembar input-kemarin" data-nilai="<?= $p ?>" readonly title="Jumlah uang kemarin" style="width: 55px; text-align: center; padding: 6px; border-radius: 8px; border: 1px solid #d0d5dd; font-size: 12px; font-weight: 600; background: #f9fafb; cursor: not-allowed;">
                    <div style="font-weight: 600; color: #98a2b3; font-size: 10px;">+</div>
                    <input type="number" min="0" class="input-lembar input-hari-ini" data-nilai="<?= $p ?>" title="Jumlah uang hari ini" style="width: 55px; text-align: center; padding: 6px; border-radius: 8px; border: 1px solid #d0d5dd; font-size: 12px; font-weight: 600;">
                    <div style="font-weight: 600; color: #98a2b3; font-size: 10px;">=</div>
                    <div class="subtotal" style="flex: 1; text-align: right; font-weight: 800; color: #1d2939; font-size: 13px;">0</div>
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

        function formatRupiah(value) {
            const number = Number(value) || 0;
            return 'Rp ' + Math.round(number).toLocaleString('id-ID');
        }

        function setVaultBalance(vaultId, balance) {
            const formatted = formatRupiah(balance);
            const cardBalanceEl = document.getElementById('vault-balance-display-' + vaultId);
            if (cardBalanceEl) {
                const pecahanSummary = document.getElementById('vault-pecahan-summary-' + vaultId);
                if (cardBalanceEl.firstChild) {
                    cardBalanceEl.firstChild.textContent = formatted + ' ';
                } else {
                    cardBalanceEl.insertBefore(document.createTextNode(formatted + ' '), cardBalanceEl.firstChild);
                }
                if (!pecahanSummary) {
                    const summary = document.createElement('div');
                    summary.id = 'vault-pecahan-summary-' + vaultId;
                    summary.style.cssText = 'font-size: 14px; font-weight: 600; color: #64748b; margin-top: 4px;';
                    cardBalanceEl.appendChild(summary);
                }
                cardBalanceEl.dataset.balance = balance;
            }

            const modalBalanceEl = document.getElementById('modal-balance-' + vaultId);
            if (modalBalanceEl) modalBalanceEl.textContent = formatted;

            const saldoSistemEl = document.getElementById('saldo-sistem-' + vaultId);
            if (saldoSistemEl) {
                saldoSistemEl.textContent = formatted;
                saldoSistemEl.dataset.balance = balance;
            }

            if (window.updateVaultSummary) window.updateVaultSummary(vaultId);
        }

        function applyVaultRefresh(result) {
            if (!result) return;

            if (result.balances) {
                Object.keys(result.balances).forEach(function(vaultId) {
                    setVaultBalance(vaultId, result.balances[vaultId]);
                });
            } else if (result.new_balance !== undefined && result.vault_id !== undefined) {
                setVaultBalance(result.vault_id, result.new_balance);
            }

            if (result.histories_html) {
                Object.keys(result.histories_html).forEach(function(vaultId) {
                    const historyTbody = document.querySelector(`#vault-transaction-modal-${vaultId} .bca-ledger tbody`);
                    if (historyTbody) {
                        historyTbody.innerHTML = result.histories_html[vaultId];
                    }
                });
                setupDeleteHandlers();
            } else if (result.history_html !== undefined && result.vault_id !== undefined) {
                const historyTbody = document.querySelector(`#vault-transaction-modal-${result.vault_id} .bca-ledger tbody`);
                if (historyTbody) {
                    historyTbody.innerHTML = result.history_html;
                    setupDeleteHandlers();
                }
            }

            const totalBalanceEl = document.getElementById('vault-total-balance');
            if (totalBalanceEl && result.total_balance !== undefined) {
                totalBalanceEl.textContent = formatRupiah(result.total_balance);
            }

            const filteredBalanceEl = document.getElementById('vault-filtered-balance');
            if (filteredBalanceEl && result.filtered_balance !== undefined) {
                filteredBalanceEl.textContent = formatRupiah(result.filtered_balance);
            }
        }

        // Global function to update vault summary (both card and modal)
        window.updateVaultSummary = function(vaultId) {
            const container = document.getElementById('pecahan-container-' + vaultId);
            if (!container) return;

            const inputs = container.querySelectorAll('.input-lembar');
            const totalEl = document.getElementById('total-uang-' + vaultId);
            const selisihEl = document.getElementById('selisih-uang-' + vaultId);
            const cardSelisihEl = document.getElementById('vault-selisih-summary-' + vaultId);
            const pecahanSummaryEl = document.getElementById('vault-pecahan-summary-' + vaultId);
            const modalPecahanEl = document.getElementById('modal-pecahan-summary-' + vaultId);
            const modalSelisihEl = document.getElementById('modal-selisih-summary-' + vaultId);
            const saldoEl = document.getElementById('vault-balance-display-' + vaultId);
            const saldoAwal = parseFloat(saldoEl ? saldoEl.dataset.balance : 0);

            let total = 0;
            let dataToSave = {};
            
            // Loop per baris (row) untuk menghitung subtotal dan akumulasi total
            const rows = container.querySelectorAll('.pecahan-row');
            rows.forEach(function(row) {
                const kInput = row.querySelector('.input-kemarin');
                const hInput = row.querySelector('.input-hari-ini');
                const nilai = parseInt(kInput.dataset.nilai) || 0;
                
                const kCount = parseInt(kInput.value) || 0;
                const hCount = parseInt(hInput.value) || 0;
                const totalLembar = kCount + hCount;

                // Memberi warna merah jika total lembar di baris ini masih kosong
                if (totalLembar === 0) {
                    kInput.style.backgroundColor = '#fef2f2'; // Merah muda transparan
                    kInput.style.borderColor = '#fda29b';
                    kInput.style.color = '#b42318';
                } else {
                    kInput.style.backgroundColor = '#f9fafb';
                    kInput.style.borderColor = '#d0d5dd';
                    kInput.style.color = '#475467';
                }
                
                const subtotal = totalLembar * nilai;
                const subtotalDisplay = row.querySelector('.subtotal');
                if (subtotalDisplay) subtotalDisplay.textContent = subtotal.toLocaleString('id-ID');
                
                total += subtotal;
                if (totalLembar > 0) dataToSave[nilai] = totalLembar;
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

            if (total > 0) {
                if (pecahanSummaryEl) pecahanSummaryEl.textContent = 'Fisik: Rp ' + formattedTotal;
                if (modalPecahanEl) modalPecahanEl.textContent = '| Fisik: Rp ' + formattedTotal;
                if (modalSelisihEl) {
                    modalSelisihEl.textContent = '(Selisih: ' + formattedSelisih + ')';
                    modalSelisihEl.style.color = color;
                }
                
                if (cardSelisihEl) {
                    cardSelisihEl.textContent = 'Selisih: ' + formattedSelisih;
                    cardSelisihEl.style.color = color;
                }
            } else {
                if (pecahanSummaryEl) pecahanSummaryEl.textContent = '';
                if (modalPecahanEl) modalPecahanEl.textContent = '';
                if (modalSelisihEl) modalSelisihEl.textContent = '';
                if (cardSelisihEl) cardSelisihEl.textContent = '';
            }

            return dataToSave;
        };

        function pecahanPayloadEmpty(data) {
            if (data == null) return true;
            if (Array.isArray(data)) return data.length === 0;
            if (typeof data !== 'object') return true;
            return Object.keys(data).length === 0;
        }

        function applyPecahanRows(rows, parsed) {
            if (!parsed || typeof parsed !== 'object' || Array.isArray(parsed)) return;
            rows.forEach(function(row) {
                const kInput = row.querySelector('.input-kemarin');
                const nilai = kInput.dataset.nilai;
                if (parsed[nilai] !== undefined) {
                    kInput.value = parsed[nilai];
                }
            });
        }

        function clearPecahanRows(rows) {
            rows.forEach(function(row) {
                row.querySelectorAll('input').forEach(function(input) {
                    input.value = '';
                });
            });
        }

        // Setup Pecahan Calculator logic
        document.querySelectorAll('.pecahan-container').forEach(function(container) {
            const vaultId = container.id.replace('pecahan-container-', '');
            const rows = container.querySelectorAll('.pecahan-row');
            const savedData = localStorage.getItem('pecahan_vault_' + vaultId);

            if (savedData) {
                try {
                    applyPecahanRows(rows, JSON.parse(savedData));
                } catch (e) {}
            }

            function finishPecahanSetup() {
                const hInputs = container.querySelectorAll('.input-hari-ini');
                hInputs.forEach(function(input, index) {
                    input.addEventListener('keydown', function(e) {
                        if (e.key === 'Enter') {
                            e.preventDefault();
                            const next = hInputs[index + 1];
                            if (next) {
                                next.focus();
                            } else {
                                document.querySelector('.btn-success[onclick^="simpanPecahan"]').focus();
                            }
                        }
                    });
                });

                updateVaultSummary(vaultId);

                rows.forEach(function(row) {
                    const inputs = row.querySelectorAll('input');
                    inputs.forEach(function(input) {
                        input.addEventListener('input', function() {
                            updateVaultSummary(vaultId);
                        });
                    });
                });

                window.resetPecahan = function(id) {
                    askConfirmation('Kosongkan input hari ini saja?', function() {
                        const ctr = document.getElementById('pecahan-container-' + id);
                        if (ctr) {
                            ctr.querySelectorAll('.input-hari-ini').forEach(function(inp) {
                                inp.value = '';
                            });
                            updateVaultSummary(id);
                        }
                    }, 'Reset Input', 'Ya, Kosongkan');
                };

                window.simpanPecahan = async function(id) {
                    const dataToSave = updateVaultSummary(id);

                    const fd = new FormData();
                    fd.append('action', 'save_vault_pecahan');
                    fd.append('vault_id', String(id));
                    fd.append('pecahan_json', JSON.stringify(dataToSave));

                    let res = null;
                    try {
                        const response = await fetch('index.php?route=keuangan/brankas', {
                            method: 'POST',
                            body: fd,
                            headers: { 'X-Requested-With': 'XMLHttpRequest' }
                        });
                        res = await response.json();
                    } catch (e) {
                        showToast('Gagal menyimpan ke server. Data belum disinkronkan.', 'warning');
                        return;
                    }

                    if (!res || !res.success) {
                        showToast('Gagal menyimpan pecahan ke server.', 'warning');
                        return;
                    }

                    const ctr = document.getElementById('pecahan-container-' + id);
                    if (ctr) {
                        const mergeRows = ctr.querySelectorAll('.pecahan-row');
                        mergeRows.forEach(function(row) {
                            const kInput = row.querySelector('.input-kemarin');
                            const hInput = row.querySelector('.input-hari-ini');
                            const kCount = parseInt(kInput.value) || 0;
                            const hCount = parseInt(hInput.value) || 0;
                            kInput.value = kCount + hCount;
                            hInput.value = '';
                        });
                        updateVaultSummary(id);
                    }

                    try {
                        localStorage.setItem('pecahan_vault_' + id, JSON.stringify(res.pecahan || dataToSave));
                    } catch (e) {}
                    showToast('Data pecahan berhasil disimpan!');
                };
            }

            finishPecahanSetup();

            fetch('index.php?route=keuangan/brankas&ajax_pecahan=1&vault_id=' + encodeURIComponent(vaultId), {
                headers: { 'X-Requested-With': 'XMLHttpRequest' }
            })
                .then(function(r) { return r.json(); })
                .then(function(serverData) {
                    if (!pecahanPayloadEmpty(serverData)) {
                        applyPecahanRows(rows, serverData);
                        try {
                            localStorage.setItem('pecahan_vault_' + vaultId, JSON.stringify(serverData));
                        } catch (e) {}
                        updateVaultSummary(vaultId);
                    } else {
                        clearPecahanRows(rows);
                        try {
                            localStorage.removeItem('pecahan_vault_' + vaultId);
                        } catch (e) {}
                        updateVaultSummary(vaultId);
                    }
                })
                .catch(function() {});
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
                        applyVaultRefresh(result);
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

        document.querySelectorAll('.transaction-type-select').forEach(function(transactionType) {
            const vaultId = transactionType.dataset.vaultId;
            const sourceGroup = document.getElementById('source-vault-group-' + vaultId);
            const targetGroup = document.getElementById('target-vault-group-' + vaultId);

            function syncTransactionForm() {
                if (!sourceGroup || !targetGroup) return;
                const type = transactionType.value;
                
                // Brankas Sumber selalu disembunyikan (sudah otomatis dari brankas ini)
                sourceGroup.style.display = 'none';
                
                // Ke Brankas hanya muncul untuk Switching Dana
                targetGroup.style.display = (type === 'switching_dana') ? '' : 'none';
            }


                // Refresh balance when any vault modal (transaction, pecahan, edit) is closed
                document.querySelectorAll('.modal[data-vault-id]').forEach(function(mod) {
                    mod.addEventListener('hidden.bs.modal', function () {
                        const vaultId = mod.dataset.vaultId;
                        // Fetch latest balance via AJAX endpoint
                        fetch(`${window.location.pathname}?ajax_balance=1&vault_id=${vaultId}`, { headers: { 'X-Requested-With': 'XMLHttpRequest' } })
                            .then(r => r.json())
                            .then(data => {
                                if (data && typeof data.balance !== 'undefined') {
                                    const formatted = 'Rp ' + Math.round(data.balance).toLocaleString('id-ID');
                                    const cardEl = document.getElementById('vault-balance-display-' + vaultId);
                                    if (cardEl) {
                                        cardEl.innerHTML = formatted + '<div id="vault-pecahan-summary-' + vaultId + '" style="font-size: 14px; font-weight: 600; color: #64748b; margin-top: 4px;">' + (cardEl.dataset.summary || '') + '</div>';
                                        cardEl.dataset.balance = data.balance;
                                    }
                                    const modalEl = document.getElementById('modal-balance-' + vaultId);
                                    if (modalEl) modalEl.textContent = formatted;
                                }
                            })
                            .catch(err => console.error('Error refreshing balance:', err));
                    });
                });

            // Input money formatting
            const amountInput = transactionType.closest('form').querySelector('.money-input');
            if (amountInput) {
                amountInput.addEventListener('input', function(e) {
                    let value = this.value.replace(/[^0-9]/g, '');
                    if (value !== '') {
                        this.value = parseInt(value).toLocaleString('id-ID');
                    }
                });
            }

            transactionType.addEventListener('change', syncTransactionForm);
            syncTransactionForm();
        });

        document.querySelectorAll('.vault-transaction-form').forEach(function(form) {
            form.addEventListener('submit', async function(e) {
                e.preventDefault();
                const vaultId = form.dataset.vaultId; // Ambil dari form attribute
                const button = form.querySelector('button[type="submit"]');
                const originalText = button.textContent;
                button.disabled = true;
                button.textContent = 'Menyimpan...';

                const formData = new FormData(form);
                // Pastikan active_vault_id terkirim
                formData.set('active_vault_id', vaultId);

                try {
                    const response = await fetch(window.location.href, {
                        method: 'POST',
                        body: formData,
                        headers: { 'X-Requested-With': 'XMLHttpRequest' }
                    });

                    // Cek jika response bukan JSON (misal ada PHP error)
                    const contentType = response.headers.get('content-type');
                    if (!contentType || !contentType.includes('application/json')) {
                        const text = await response.text();
                        console.error('Non-JSON response:', text);
                        showToast('Server error. Cek console.', 'warning');
                        button.disabled = false;
                        button.textContent = originalText;
                        return;
                    }

                    const result = await response.json();
                    if (result.success) {
                        showSuccessModal(result.message);
                        applyVaultRefresh(result);

                        // Reset form tapi pertahankan hidden fields
                        const activeVaultHidden = form.querySelector('input[name="active_vault_id"]');
                        const sourceVaultHidden = form.querySelector('input[name="source_vault_id"]');
                        const savedActiveVault = activeVaultHidden ? activeVaultHidden.value : vaultId;
                        const savedSourceVault = sourceVaultHidden ? sourceVaultHidden.value : vaultId;
                        
                        form.reset();
                        
                        // Pulihkan hidden fields setelah reset
                        if (activeVaultHidden) activeVaultHidden.value = savedActiveVault;
                        if (sourceVaultHidden) sourceVaultHidden.value = savedSourceVault;
                        
                        form.querySelector('select.transaction-type-select, select[name="transaction_type"]').dispatchEvent(new Event('change'));

                    } else {
                        showToast(result.message || 'Transaksi gagal disimpan.', 'warning');
                    }
                    button.disabled = false;
                    button.textContent = originalText;
                } catch (error) {
                    console.error('Submit error:', error);
                    showToast('Terjadi kesalahan koneksi. Cek console.', 'warning');
                    button.disabled = false;
                    button.textContent = originalText;
                }
            });
        });


    }());
</script>
