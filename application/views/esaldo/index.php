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
    <button type="button" class="btn" onclick="toggleEsaldoModal(true)">Add E-Saldo</button>
</div>

<div class="card">
    <h3>Daftar Master E-Saldo</h3>
    <form method="get" style="display:grid; grid-template-columns:minmax(0,1fr) auto; gap:12px; align-items:end; margin:16px 0;">
        <input type="hidden" name="route" value="esaldo">
        <div>
            <div class="small">Cari E-Saldo</div>
            <input type="text" name="q" value="<?= htmlspecialchars((string) ($keyword ?? '')) ?>" placeholder="Cari kode, nama, atau provider">
        </div>
        <div class="search-reset-actions">
            <button type="submit" class="btn btn-secondary">Search</button>
            <a href="index.php?route=esaldo" class="btn btn-info">Reset</a>
        </div>
    </form>
    <div class="esaldo-table-wrap">
        <table class="esaldo-table">
            <thead><tr><th>Kode</th><th>Nama</th><th>Provider</th><th>Modal Default</th><th>Jual Default</th><th>Aksi</th></tr></thead>
            <tbody>
            <?php foreach (($esaldos ?? []) as $esaldo): ?>
                <tr>
                    <td data-label="Kode"><?= htmlspecialchars((string) $esaldo['code']) ?></td>
                    <td data-label="Nama"><?= htmlspecialchars((string) $esaldo['name']) ?></td>
                    <td data-label="Provider"><?= htmlspecialchars((string) ($esaldo['description'] ?? '-')) ?></td>
                    <td data-label="Modal Default"><?= rupiah((float) ($esaldo['purchase_price'] ?? 0)) ?></td>
                    <td data-label="Jual Default"><?= rupiah((float) ($esaldo['selling_price'] ?? 0)) ?></td>
                    <td data-label="Aksi">
                        <div class="action-row">
                            <a class="btn btn-secondary" href="index.php?route=esaldo&edit=<?= (int) $esaldo['id'] ?>">Edit</a>
                            <form method="post" onsubmit="return confirm('Hapus master E-Saldo ini?');" style="margin:0;">
                                <input type="hidden" name="action" value="delete">
                                <input type="hidden" name="id" value="<?= (int) $esaldo['id'] ?>">
                                <button type="submit" class="btn btn-danger">Hapus</button>
                            </form>
                        </div>
                    </td>
                </tr>
            <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>

<div class="modal-backdrop <?= !empty($editEsaldo) ? 'active' : '' ?>" id="esaldo-modal">
    <div class="modal">
        <div class="modal-head">
            <h3 style="margin:0;"><?= !empty($editEsaldo) ? 'Edit E-Saldo' : 'Add E-Saldo' ?></h3>
            <button type="button" class="modal-close" onclick="toggleEsaldoModal(false)">Tutup</button>
        </div>
        <form method="post">
            <input type="hidden" name="id" value="<?= htmlspecialchars((string) ($editEsaldo['id'] ?? '')) ?>">
            <input type="hidden" name="code" value="<?= htmlspecialchars((string) $nextCode) ?>">
            <div class="form-grid">
                <div><div class="small">Kode</div><input value="<?= htmlspecialchars((string) $nextCode) ?>" readonly></div>
                <div><div class="small">Nama Produk</div><input name="name" value="<?= htmlspecialchars((string) ($editEsaldo['name'] ?? '')) ?>" placeholder="Contoh: Pulsa Telkomsel 10K" required></div>
                <div><div class="small">Provider / Keterangan</div><input name="description" value="<?= htmlspecialchars((string) ($editEsaldo['description'] ?? '')) ?>" placeholder="Contoh: Telkomsel"></div>
                <div><div class="small">Modal Default</div><input type="text" class="money-input" name="purchase_price" value="<?= htmlspecialchars(number_format((float) ($editEsaldo['purchase_price'] ?? 0), 0, ',', '.')) ?>" placeholder="Modal default"></div>
                <div><div class="small">Harga Jual Default</div><input type="text" class="money-input" name="selling_price" value="<?= htmlspecialchars(number_format((float) ($editEsaldo['selling_price'] ?? 0), 0, ',', '.')) ?>" placeholder="Jual default"></div>
            </div>
            <div style="margin-top:12px;"><button type="submit">Simpan E-Saldo</button></div>
        </form>
    </div>
</div>
<script>
    function toggleEsaldoModal(show) {
        document.getElementById('esaldo-modal').classList.toggle('active', show);
    }

    (function () {
        document.querySelectorAll('.money-input').forEach(function (input) {
            input.addEventListener('input', function () {
                const digits = this.value.replace(/[^\d]/g, '');
                this.value = digits === '' ? '' : Number(digits).toLocaleString('id-ID');
            });
        });
    }());
</script>
