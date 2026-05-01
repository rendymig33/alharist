<?php $flashData = flash(); ?>

<style>
    .esaldo-hero {
        background: linear-gradient(135deg, #ffffff 0%, #fff9eb 50%, #fff4d0 100%);
        border: 1px solid var(--line);
        border-radius: 20px;
        padding: 28px 24px;
        margin-top: 18px;
        box-shadow: 0 14px 30px rgba(28, 39, 60, .06);
        position: relative;
        overflow: hidden;
    }

    .esaldo-hero::before {
        content: '';
        position: absolute;
        top: -40px;
        right: -40px;
        width: 160px;
        height: 160px;
        background: radial-gradient(circle, rgba(255, 213, 74, .3) 0%, transparent 70%);
        border-radius: 50%;
        pointer-events: none;
    }

    .esaldo-hero::after {
        content: '';
        position: absolute;
        bottom: -20px;
        left: 30%;
        width: 100px;
        height: 100px;
        background: radial-gradient(circle, rgba(215, 25, 32, .06) 0%, transparent 70%);
        border-radius: 50%;
        pointer-events: none;
    }

    .esaldo-hero .hero-label {
        font-size: 11px;
        font-weight: 800;
        letter-spacing: .1em;
        text-transform: uppercase;
        color: #98a2b3;
        margin-bottom: 8px;
    }

    .esaldo-hero .hero-title {
        font-size: 15px;
        font-weight: 600;
        color: #344054;
        margin: 0 0 18px;
    }

    .esaldo-form-card {
        background: var(--white);
        border: 1px solid var(--line);
        border-radius: 20px;
        padding: 24px;
        box-shadow: 0 12px 24px rgba(28, 39, 60, .05);
        position: relative;
        z-index: 1;
    }

    .esaldo-form-header {
        display: flex;
        align-items: center;
        gap: 12px;
        margin-bottom: 20px;
    }

    .esaldo-form-icon {
        width: 48px;
        height: 48px;
        border-radius: 14px;
        background: linear-gradient(135deg, var(--red), #b31419);
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 22px;
        color: #fff;
        box-shadow: 0 8px 16px rgba(215, 25, 32, .2);
        flex-shrink: 0;
    }

    .esaldo-form-header h3 {
        margin: 0 0 4px;
        font-size: 17px;
        font-weight: 700;
    }

    .esaldo-input-group {
        position: relative;
    }

    .esaldo-input-group label {
        display: block;
        font-size: 12px;
        font-weight: 700;
        letter-spacing: .04em;
        text-transform: uppercase;
        color: #667085;
        margin-bottom: 8px;
    }

    .esaldo-input-group .input-wrap {
        position: relative;
        display: flex;
        align-items: center;
    }

    .esaldo-input-group .currency-prefix {
        position: absolute;
        left: 14px;
        font-size: 15px;
        font-weight: 700;
        color: #98a2b3;
        pointer-events: none;
        z-index: 2;
    }

    .esaldo-input-group input[name="balance"] {
        padding-left: 42px;
        font-size: 20px;
        font-weight: 700;
        height: 56px;
        border-radius: 14px;
        border: 2px solid #e2e4ea;
        transition: all .2s ease;
        color: var(--ink);
    }

    .esaldo-input-group input[name="balance"]:focus {
        border-color: var(--red);
        box-shadow: 0 0 0 4px rgba(215, 25, 32, .1);
    }

    .esaldo-form-actions {
        display: flex;
        gap: 10px;
        margin-top: 18px;
    }

    .esaldo-form-actions button,
    .esaldo-form-actions .btn {
        padding: 13px 24px;
        border-radius: 14px;
        font-weight: 700;
        font-size: 15px;
        min-width: 140px;
        transition: all .2s ease;
    }

    .esaldo-form-actions button[type="submit"] {
        background: linear-gradient(135deg, var(--red), #b31419);
        box-shadow: 0 8px 20px rgba(215, 25, 32, .18);
    }

    .esaldo-form-actions button[type="submit"]:hover {
        box-shadow: 0 12px 28px rgba(215, 25, 32, .28);
        transform: translateY(-2px);
    }

    /* Table section */
    .esaldo-list-card {
        background: var(--white);
        border: 1px solid var(--line);
        border-radius: 20px;
        margin-top: 18px;
        overflow: hidden;
        box-shadow: 0 12px 24px rgba(28, 39, 60, .05);
    }

    .esaldo-list-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        gap: 14px;
        padding: 20px 24px;
        border-bottom: 1px solid var(--line);
        background: linear-gradient(180deg, #fcfcfd, #f8fafc);
    }

    .esaldo-list-header h3 {
        margin: 0 0 4px;
        font-size: 17px;
    }

    .esaldo-list-count {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        background: linear-gradient(135deg, var(--red), #b31419);
        color: #fff;
        font-size: 12px;
        font-weight: 800;
        min-width: 28px;
        height: 28px;
        border-radius: 999px;
        padding: 0 8px;
    }

    .esaldo-table {
        width: 100%;
        border-collapse: separate;
        border-spacing: 0;
    }

    .esaldo-table thead th {
        background: var(--soft-yellow);
        color: #5a4700;
        font-size: 11px;
        font-weight: 800;
        letter-spacing: .06em;
        text-transform: uppercase;
        padding: 12px 16px;
        border-bottom: 1px solid var(--line);
    }

    .esaldo-table thead th:first-child {
        border-top-left-radius: 0;
    }

    .esaldo-table thead th:last-child {
        border-top-right-radius: 0;
    }

    .esaldo-table tbody tr {
        transition: background .15s ease;
    }

    .esaldo-table tbody tr:hover {
        background: #fafbfc;
    }

    .esaldo-table tbody tr:nth-child(even) {
        background: #fcfcfd;
    }

    .esaldo-table tbody tr:nth-child(even):hover {
        background: #f6f8fa;
    }

    .esaldo-table td {
        padding: 14px 16px;
        border-bottom: 1px solid #f2f4f7;
        vertical-align: middle;
        font-size: 14px;
    }

    .esaldo-table .td-no {
        font-weight: 700;
        color: #98a2b3;
        width: 50px;
        text-align: center;
    }

    .esaldo-table .td-saldo {
        font-size: 16px;
        font-weight: 700;
        color: var(--ink);
    }

    .esaldo-table .td-date {
        color: #667085;
        font-size: 13px;
        white-space: nowrap;
    }

    .esaldo-table .td-aksi {
        width: 180px;
    }

    .esaldo-empty {
        text-align: center;
        padding: 40px 20px;
        color: #98a2b3;
    }

    .esaldo-empty-icon {
        font-size: 40px;
        margin-bottom: 12px;
        opacity: .5;
    }

    .esaldo-empty p {
        margin: 0;
        font-size: 14px;
    }

    @media (max-width: 640px) {
        .esaldo-hero {
            padding: 20px 16px;
            border-radius: 16px;
        }

        .esaldo-form-card {
            padding: 18px 16px;
            border-radius: 16px;
        }

        .esaldo-form-header {
            gap: 10px;
        }

        .esaldo-form-icon {
            width: 40px;
            height: 40px;
            border-radius: 12px;
            font-size: 18px;
        }

        .esaldo-form-header h3 {
            font-size: 15px;
        }

        .esaldo-input-group input[name="balance"] {
            font-size: 18px;
            height: 50px;
        }

        .esaldo-form-actions {
            flex-direction: column;
        }

        .esaldo-form-actions button,
        .esaldo-form-actions .btn {
            width: 100%;
            min-width: 0;
        }

        .esaldo-list-card {
            border-radius: 16px;
        }

        .esaldo-list-header {
            padding: 16px;
            flex-direction: column;
            align-items: flex-start;
        }

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

        .esaldo-table tbody tr {
            padding: 14px 16px;
            border-bottom: 1px solid var(--line);
        }

        .esaldo-table tbody tr:last-child {
            border-bottom: none;
        }

        .esaldo-table td {
            border-bottom: none;
            padding: 6px 0;
        }

        .esaldo-table td::before {
            content: attr(data-label);
            display: block;
            margin-bottom: 2px;
            font-size: 11px;
            font-weight: 800;
            letter-spacing: .05em;
            text-transform: uppercase;
            color: #98a2b3;
        }

        .esaldo-table .td-no {
            text-align: left;
            width: auto;
        }

        .esaldo-table .td-aksi {
            width: auto;
            margin-top: 6px;
        }
    }
</style>

<?php if (!empty($flashData)): ?>
    <div class="flash <?= htmlspecialchars((string) ($flashData['type'] ?? 'success')) ?>">
        <?= htmlspecialchars((string) ($flashData['message'] ?? '')) ?>
    </div>
<?php endif; ?>

<div class="esaldo-hero">
    <div class="hero-label">Master E-Saldo</div>
    <p class="hero-title">Masukkan dan kelola saldo E-Saldo Anda di sini.</p>

    <div class="esaldo-form-card">
        <div class="esaldo-form-header">
            <div class="esaldo-form-icon">$</div>
            <div>
                <h3><?= !empty($editEsaldo) ? 'Edit Saldo' : 'Masukkan Saldo Baru' ?></h3>
                <div class="small">Masukkan nominal saldo yang ingin disimpan.</div>
            </div>
        </div>

        <form method="post">
            <input type="hidden" name="action" value="save">
            <input type="hidden" name="id" value="<?= htmlspecialchars((string) ($editEsaldo['id'] ?? '')) ?>">

            <div class="esaldo-input-group" style="margin-bottom: 16px;">
                <label for="esaldo-name">Nama / Keterangan</label>
                <div class="input-wrap">
                    <input
                        type="text"
                        id="esaldo-name"
                        name="name"
                        placeholder="Contoh: Saldo BCA / Provider XYZ"
                        required
                        autocomplete="off"
                        style="padding-left: 14px;"
                        value="<?= htmlspecialchars((string) ($editEsaldo['name'] ?? '')) ?>">
                </div>
            </div>

            <div class="esaldo-input-group">
                <label for="esaldo-balance">Nominal Saldo</label>
                <div class="input-wrap">
                    <span class="currency-prefix">Rp</span>
                    <input
                        id="esaldo-balance"
                        name="balance"
                        inputmode="numeric"
                        placeholder="0"
                        required
                        autocomplete="off"
                        value="<?= htmlspecialchars(number_format((float) ($editEsaldo['balance'] ?? 0), 0, ',', '.')) ?>">
                </div>
            </div>

            <div class="esaldo-form-actions">
                <button type="submit">💾 Simpan Saldo</button>
                <?php if (!empty($editEsaldo)): ?>
                    <a class="btn btn-secondary" href="index.php?route=esaldo">Batal</a>
                <?php endif; ?>
            </div>
        </form>
    </div>
</div>

<div class="esaldo-list-card">
    <div class="esaldo-list-header">
        <div>
            <h3>Daftar Saldo</h3>
            <div class="small">Riwayat saldo yang sudah dimasukkan.</div>
        </div>
        <?php if (!empty($esaldos)): ?>
            <span class="esaldo-list-count"><?= count($esaldos) ?></span>
        <?php endif; ?>
    </div>

    <table class="esaldo-table">
        <thead>
            <tr>
                <th style="text-align:center;">No</th>
                <th>Keterangan</th>
                <th>Saldo</th>
                <th>Tanggal</th>
                <th>Aksi</th>
            </tr>
        </thead>
        <tbody>
            <?php if (!empty($esaldos)): ?>
                <?php $no = 1;
                foreach ($esaldos as $item): ?>
                    <tr>
                        <td class="td-no" data-label="No"><?= $no++ ?></td>
                        <td class="td-name" data-label="Keterangan"><strong><?= htmlspecialchars((string) ($item['name'] ?? 'E-Saldo')) ?></strong></td>
                        <td class="td-saldo" data-label="Saldo"><?= rupiah((float) $item['balance']) ?></td>
                        <td class="td-date" data-label="Tanggal"><?= htmlspecialchars((string) ($item['created_at'] ?? '-')) ?></td>
                        <td class="td-aksi" data-label="Aksi">
                            <div class="action-row">
                                <a class="btn btn-info" href="index.php?route=esaldo&edit=<?= (int) $item['id'] ?>">Edit</a>
                                <form method="post" onsubmit="return confirm('Hapus saldo ini?');" style="margin:0;">
                                    <input type="hidden" name="action" value="delete">
                                    <input type="hidden" name="id" value="<?= (int) $item['id'] ?>">
                                    <button type="submit" class="btn btn-danger">Hapus</button>
                                </form>
                            </div>
                        </td>
                    </tr>
                <?php endforeach; ?>
            <?php else: ?>
                <tr>
                    <td colspan="4">
                        <div class="esaldo-empty">
                            <div class="esaldo-empty-icon">📭</div>
                            <p>Belum ada data saldo. Tambahkan saldo pertama Anda!</p>
                        </div>
                    </td>
                </tr>
            <?php endif; ?>
        </tbody>
    </table>
</div>

<script>
    (function() {
        const balanceInput = document.getElementById('esaldo-balance');
        if (balanceInput) {
            balanceInput.addEventListener('input', function() {
                const digits = this.value.replace(/[^\d]/g, '');
                this.value = digits === '' ? '' : Number(digits).toLocaleString('id-ID');
            });
        }
    }());
</script>