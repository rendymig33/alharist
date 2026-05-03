<style>
    .service-shell {
        padding: 0;
        overflow: hidden;
    }

    .service-head-grid {
        background: #111;
        color: #fff;
        display: grid;
        grid-template-columns: repeat(5, 1fr);
        font-size: 13px;
    }

    .service-main-grid {
        display: grid;
        grid-template-columns: 1.4fr .6fr;
    }

    .service-table-wrap,
    .service-list-wrap {
        overflow-x: auto;
    }

    .service-total-box {
        border: 1px solid var(--line);
        border-radius: 16px;
        background: #fff;
        padding: 16px;
    }

    .service-pay-row {
        display: grid;
        grid-template-columns: minmax(0, 1fr) 160px;
        gap: 12px;
        margin-top: 18px;
    }

    .service-search {
        display: grid;
        grid-template-columns: minmax(0, 1fr) auto auto;
        gap: 12px;
        align-items: end;
        margin-bottom: 16px;
    }

    @media (max-width: 920px) {

        .service-head-grid,
        .service-main-grid {
            grid-template-columns: 1fr;
        }

        .service-pay-row,
        .service-search {
            grid-template-columns: 1fr;
        }
    }

    @media (max-width: 640px) {
        .service-head-grid>div {
            border-right: none !important;
            border-bottom: 1px solid #333;
        }

        .service-head-grid>div:last-child {
            border-bottom: none;
        }

        .service-main-grid>div:first-child {
            border-right: none !important;
            border-bottom: 1px solid #e2e4ea;
        }

        #ppob-total {
            font-size: 38px !important;
            text-align: left !important;
        }

        #service-receipt {
            max-width: 100% !important;
        }
    }
</style>
<div class="card service-shell">
    <div class="service-head-grid">
        <div style="padding:10px 14px; border-right:1px solid #333;">Modal Dasar</div>
        <div style="padding:10px 14px; border-right:1px solid #333;"><?= htmlspecialchars((string) (($modalVault['bank_name'] ?? 'SALDO MODAL #1'))) ?></div>
        <div style="padding:10px 14px; border-right:1px solid #333;">Pembayaran</div>
        <div style="padding:10px 14px; border-right:1px solid #333;">Tanggal: <?= date('d M Y') ?></div>
        <div style="padding:10px 14px;">Kode: <?= htmlspecialchars((string) ($nextCode ?? '-')) ?></div>
    </div>
    <div class="service-main-grid">
        <div style="padding:16px; border-right:1px solid #e2e4ea;">
            <form method="post" id="service-form">
                <div class="form-grid">
                    <div>
                        <div class="small">Jenis Layanan</div>
                        <select name="service_type" required>
                            <option value="Top Up E-Wallet">Top Up E-Wallet</option>
                            <option value="Pulsa">Pulsa</option>
                            <option value="PPOB">PPOB</option>
                            <option value="Tarik Tunai">Tarik Tunai</option>
                        </select>
                    </div>
                    <div>
                        <div class="small">Brankas Tujuan Untung</div>
                        <select name="vault_id">
                            <option value="0">Pilih Brankas Tujuan</option>
                            <?php if (isset($vaults) && is_array($vaults)): ?>
                                <?php foreach ($vaults as $vault): ?>
                                    <option value="<?= (int) $vault['id'] ?>"><?= htmlspecialchars($vault['bank_name']) ?></option>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </select>
                    </div>
                    <div>
                        <div class="small">Harga Beli</div><input type="text" class="money-input" name="buy_price" placeholder="Modal layanan" required>
                    </div>
                    <div>
                        <div class="small">Harga Jual</div><input type="text" class="money-input" name="sell_price" placeholder="Harga ke pelanggan" required>
                    </div>
                    <div>
                        <div class="small">Pembayaran</div><select name="payment_type" required>
                            <option value="Tunai">Tunai</option>
                            <option value="QRIS">QRIS</option>
                        </select>
                    </div>
                    <div>
                        <div class="small">No. Token / Kode Manual</div><input name="token_number" placeholder="Isi jika ada token atau kode manual">
                    </div>
                    <div style="grid-column:1 / -1;">
                        <div class="info-strip">
                            <div class="small">Modal akan dipotong dari <?= htmlspecialchars((string) (($modalVault['bank_name'] ?? 'SALDO MODAL #1'))) ?> sebesar Harga Beli. Keuntungan bersih akan masuk ke brankas tujuan yang dipilih.</div>
                        </div>
                    </div>
                </div>
            </form>
        </div>
        <div style="padding:16px; background:#fafafa;">
            <div class="small">TOTAL BELANJA</div>
            <div class="service-total-box">
                <div style="font-size:54px; font-weight:700; text-align:right; margin:0;" id="ppob-total">Rp 0</div>
            </div>
            <div class="service-pay-row">
                <input type="text" class="money-input" id="service-paid" placeholder="Masukan Uang">
                <button type="button" class="btn-green" id="confirm-service" style="border-radius:999px;">Bayar</button>
            </div>
        </div>
    </div>
</div>

<div class="card" style="margin-top:18px;">
    <h3>List Layanan</h3>
    <form method="get" class="service-search">
        <input type="hidden" name="route" value="layanan">
        <div>
            <div class="small">Cari Layanan</div>
            <input type="text" name="q" value="<?= htmlspecialchars((string) ($keyword ?? '')) ?>" placeholder="Cari kode, jenis, pelanggan, atau tujuan">
        </div>
        <div class="search-reset-actions">
            <button type="submit" class="btn btn-secondary">Search</button>
            <a href="index.php?route=layanan" class="btn btn-info">Reset</a>
        </div>
    </form>
    <div class="service-list-wrap">
        <table>
            <thead>
                <tr>
                    <th>Kode</th>
                    <th>Jenis</th>
                    <th>Modal</th>
                    <th>Jual</th>
                    <th>Token</th>
                    <th>Profit</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
                <?php if (isset($services) && is_array($services)): ?>
                    <?php foreach ($services as $service): ?>
                        <tr>
                            <td><?= htmlspecialchars($service['code']) ?></td>
                            <td><?= htmlspecialchars($service['service_type']) ?></td>
                            <td><?= rupiah((float) $service['buy_price']) ?></td>
                            <td><?= rupiah((float) $service['sell_price']) ?></td>
                            <td><?= htmlspecialchars((string) ($service['token_number'] ?? '-')) ?></td>
                            <td><?= rupiah((float) $service['profit']) ?></td>
                            <td>
                                <form method="post" onsubmit="event.preventDefault(); const f = this; askConfirmation('Hapus transaksi layanan ini?', () => f.submit());">
                                    <input type="hidden" name="action" value="delete_service">
                                    <input type="hidden" name="service_id" value="<?= (int) $service['id'] ?>">
                                    <button class="btn-secondary" type="submit">Hapus</button>
                                </form>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="7" style="text-align:center;">Data layanan tidak tersedia atau belum dimuat.</td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>
<?php if (!empty($serviceReceipt)): ?>
    <div class="card" style="margin-top:18px;">
        <h3>Struk Layanan</h3>
        <div id="service-receipt" style="border:1px dashed #999; padding:16px; max-width:420px;">
            <div style="font-weight:700; text-align:center;">STRUK LAYANAN</div>
            <div>Kode: <?= htmlspecialchars($serviceReceipt['code']) ?></div>
            <div>Jenis: <?= htmlspecialchars($serviceReceipt['service_type']) ?></div>
            <div>Modal: <?= rupiah((float) $serviceReceipt['buy_price']) ?></div>
            <div>Bayar: <?= rupiah((float) $serviceReceipt['sell_price']) ?></div>
            <div>Untung: <?= rupiah((float) $serviceReceipt['profit']) ?></div>
            <div>Token: <?= htmlspecialchars((string) ($serviceReceipt['token_number'] ?? '-')) ?></div>
            <div>Tanggal: <?= date('d-m-Y H:i') ?></div>
        </div>
        <div style="margin-top:12px;"><button type="button" class="btn" onclick="window.print()">Cetak Struk</button></div>
    </div>
<?php endif; ?>
<script>
    (function() {
        function formatNumber(value) {
            const digits = String(value || '').replace(/[^\d]/g, '');
            return digits === '' ? '' : Number(digits).toLocaleString('id-ID');
        }

        document.querySelectorAll('.money-input').forEach(function(input) {
            input.addEventListener('input', function() {
                this.value = formatNumber(this.value);
                if (this.name === 'sell_price') {
                    document.getElementById('ppob-total').textContent = 'Rp ' + (Number(this.value.replace(/[^\d]/g, '')) || 0).toLocaleString('id-ID');
                }
            });
        });

        const form = document.getElementById('service-form');
        const btn = document.getElementById('confirm-service');
        if (form && btn) {
            btn.addEventListener('click', function() {
                askConfirmation('Simpan transaksi layanan ini? Mohon cek kembali nominal, harga jual, dan nomor tujuan.', () => form.submit(), 'Konfirmasi Simpan', 'Simpan Transaksi', 'btn-success');
            });
        }
    }());
</script>