<?php $flashData = flash(); ?>

<div class="toolbar">
    <div class="small">Master E-Saldo — Masukkan dan kelola saldo di sini.</div>
</div>

<?php if (!empty($flashData)): ?>
    <div class="flash <?= htmlspecialchars((string) ($flashData['type'] ?? 'success')) ?>">
        <?= htmlspecialchars((string) ($flashData['message'] ?? '')) ?>
    </div>
<?php endif; ?>

<div class="card" style="margin-top:18px;">
    <div class="section-title"><?= !empty($editEsaldo) ? 'Edit Saldo' : 'Masukkan Saldo' ?></div>
    <p class="small" style="margin-bottom:16px;">Gunakan form ini untuk menambah atau mengubah saldo E-Saldo.</p>

    <form method="post" class="form-grid">
        <input type="hidden" name="action" value="save">
        <input type="hidden" name="id" value="<?= htmlspecialchars((string) ($editEsaldo['id'] ?? '')) ?>">

        <div>
            <div class="small">Saldo</div>
            <input name="balance" inputmode="numeric" placeholder="0" required value="<?= htmlspecialchars(number_format((float) ($editEsaldo['balance'] ?? 0), 0, ',', '.')) ?>">
        </div>

        <div style="display:flex; gap:10px; flex-wrap:wrap; align-items:center; margin-top:8px;">
            <button type="submit" class="btn">Simpan</button>
            <?php if (!empty($editEsaldo)): ?>
                <a class="btn btn-secondary" href="index.php?route=esaldo">Batal</a>
            <?php endif; ?>
        </div>
    </form>
</div>

<div class="card" style="margin-top:18px;">
    <div style="display:flex; justify-content:space-between; align-items:center; gap:12px; flex-wrap:wrap; margin-bottom:12px;">
        <div>
            <h3 style="margin:0;">Daftar Saldo</h3>
            <div class="small">Riwayat saldo yang sudah dimasukkan.</div>
        </div>
    </div>

    <div class="esaldo-table-wrap">
        <table class="esaldo-table">
            <thead>
                <tr>
                    <th>No</th>
                    <th>Saldo</th>
                    <th>Tanggal</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
                <?php if (!empty($esaldos)): ?>
                    <?php $no = 1; foreach ($esaldos as $item): ?>
                        <tr>
                            <td data-label="No"><?= $no++ ?></td>
                            <td data-label="Saldo"><strong><?= rupiah((float) $item['balance']) ?></strong></td>
                            <td data-label="Tanggal"><?= htmlspecialchars((string) ($item['created_at'] ?? '-')) ?></td>
                            <td data-label="Aksi">
                                <div class="action-row" style="display:flex; gap:8px; flex-wrap:wrap;">
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
                        <td colspan="4" style="text-align:center; padding:20px; color:#667085;">Belum ada data saldo.</td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>