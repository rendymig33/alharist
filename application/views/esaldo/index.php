<?php $flashData = flash(); ?>

<div class="toolbar">
    <div class="small">Master E-Saldo sebagai pondasi saldo. Tambah, edit, dan update saldo E-Saldo di sini.</div>
</div>

<?php if (!empty($flashData)): ?>
    <div class="flash <?= htmlspecialchars((string) ($flashData['type'] ?? 'success')) ?>">
        <?= htmlspecialchars((string) ($flashData['message'] ?? '')) ?>
    </div>
<?php endif; ?>

<div class="two-col" style="margin-top:18px; gap:18px;">
    <div class="card">
        <h3>Saldo Modal</h3>
        <div class="metric" style="margin: 16px 0;"><?= rupiah((float) ($modalBalance ?? 0)) ?></div>
        <p class="small">Saldo modal ini berkurang sesuai dengan transaksi E-Saldo yang dilakukan. Perbarui master E-Saldo untuk menyesuaikan nilai item saldo.</p>
    </div>

    <div class="card">
        <div class="section-title"><?= !empty($editEsaldo) ? 'Edit Master E-Saldo' : 'Tambah Master E-Saldo' ?></div>
        <p class="small" style="margin-bottom:16px;">Gunakan form ini untuk menambah atau mengubah data E-Saldo sebagai pondasi saldo.</p>

        <form method="post" class="form-grid">
            <input type="hidden" name="action" value="save">
            <input type="hidden" name="id" value="<?= htmlspecialchars((string) ($editEsaldo['id'] ?? '')) ?>">
            <input type="hidden" name="code" value="<?= htmlspecialchars((string) ($editEsaldo['code'] ?? $nextCode)) ?>">

            <div>
                <div class="small">Kode E-Saldo</div>
                <input readonly value="<?= htmlspecialchars((string) ($editEsaldo['code'] ?? $nextCode)) ?>">
            </div>
            <div>
                <div class="small">Nama E-Saldo</div>
                <input name="name" placeholder="Nama E-Saldo" required value="<?= htmlspecialchars((string) ($editEsaldo['name'] ?? '')) ?>">
            </div>
            <div>
                <div class="small">Harga Modal</div>
                <input name="purchase_price" inputmode="numeric" placeholder="0" value="<?= htmlspecialchars(number_format((float) ($editEsaldo['purchase_price'] ?? 0), 0, ',', '.')) ?>">
            </div>
            <div>
                <div class="small">Harga Jual</div>
                <input name="selling_price" inputmode="numeric" placeholder="0" value="<?= htmlspecialchars(number_format((float) ($editEsaldo['selling_price'] ?? 0), 0, ',', '.')) ?>">
            </div>
            <div>
                <div class="small">Saldo / Stok</div>
                <input name="stock" type="number" min="0" placeholder="0" value="<?= htmlspecialchars((string) ($editEsaldo['stock'] ?? 0)) ?>">
            </div>
            <div>
                <div class="small">Keterangan</div>
                <input name="description" placeholder="Deskripsi singkat" value="<?= htmlspecialchars((string) ($editEsaldo['description'] ?? '')) ?>">
            </div>

            <div style="grid-column:1 / -1; display:flex; gap:10px; flex-wrap:wrap; align-items:center; margin-top:8px;">
                <button type="submit" class="btn">Simpan</button>
                <?php if (!empty($editEsaldo)): ?>
                    <a class="btn btn-secondary" href="index.php?route=esaldo<?= !empty($keyword) ? '&q=' . urlencode((string) $keyword) : '' ?>">Batal</a>
                <?php endif; ?>
            </div>
        </form>
    </div>
</div>

<div class="card" style="margin-top:18px;">
    <div style="display:flex; justify-content:space-between; align-items:center; gap:12px; flex-wrap:wrap; margin-bottom:12px;">
        <div>
            <h3 style="margin:0;">Daftar Master E-Saldo</h3>
            <div class="small">Cari, edit, atau hapus master E-Saldo yang sudah dibuat.</div>
        </div>
        <form method="get" class="form-grid" style="max-width:420px; width:100%; grid-template-columns:1fr auto; gap:10px;">
            <input name="q" placeholder="Cari kode atau nama" value="<?= htmlspecialchars((string) $keyword) ?>">
            <button type="submit" class="btn btn-secondary">Cari</button>
        </form>
    </div>

    <div class="esaldo-table-wrap">
        <table class="esaldo-table">
            <thead>
                <tr>
                    <th>Kode</th>
                    <th>Nama</th>
                    <th>Harga Modal</th>
                    <th>Harga Jual</th>
                    <th>Saldo</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($esaldos as $item): ?>
                    <tr>
                        <td data-label="Kode"><strong><?= htmlspecialchars($item['code']) ?></strong></td>
                        <td data-label="Nama"><?= htmlspecialchars($item['name']) ?></td>
                        <td data-label="Harga Modal"><?= rupiah((float) $item['purchase_price']) ?></td>
                        <td data-label="Harga Jual"><?= rupiah((float) $item['selling_price']) ?></td>
                        <td data-label="Saldo"><?= htmlspecialchars((string) ($item['stock'] ?? 0)) ?></td>
                        <td data-label="Aksi">
                            <div class="action-row" style="display:flex; gap:8px; flex-wrap:wrap;">
                                <a class="btn btn-info" href="index.php?route=esaldo&edit=<?= (int) $item['id'] ?><?= !empty($keyword) ? '&q=' . urlencode((string) $keyword) : '' ?>">Edit</a>
                                <form method="post" onsubmit="return confirm('Hapus master E-Saldo ini?');" style="margin:0;">
                                    <input type="hidden" name="action" value="delete">
                                    <input type="hidden" name="id" value="<?= (int) $item['id'] ?>">
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