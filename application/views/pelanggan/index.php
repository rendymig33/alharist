<style>
    .customer-table-wrap {
        overflow-x: auto;
    }

    .customer-table {
        width: 100%;
    }

    @media (max-width: 640px) {
        .customer-toolbar {
            flex-direction: column;
            align-items: stretch;
        }

        .customer-toolbar .btn {
            width: 100%;
        }

        .customer-table thead {
            display: none;
        }

        .customer-table,
        .customer-table tbody,
        .customer-table tr,
        .customer-table td {
            display: block;
            width: 100%;
        }

        .customer-table tr {
            padding: 10px 0;
            border-bottom: 1px solid var(--line);
        }

        .customer-table tr:last-child {
            border-bottom: none;
        }

        .customer-table td {
            border-bottom: none;
            padding: 8px 0;
        }

        .customer-table td::before {
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
<div class="toolbar customer-toolbar">
    <div class="small">List pelanggan yang sudah tersimpan.</div>
    <button type="button" class="btn" onclick="togglePelangganModal(true)">Add Pelanggan</button>
</div>

<div class="card">
    <h3>Daftar Pelanggan</h3>
    <div class="customer-table-wrap">
        <table class="customer-table">
            <thead><tr><th>Kode</th><th>Nama</th><th>Telepon</th><th>Alamat</th><th>Aksi</th></tr></thead>
            <tbody>
            <?php foreach ($customers as $customer): ?>
                <tr>
                    <td data-label="Kode"><?= htmlspecialchars($customer['code']) ?></td>
                    <td data-label="Nama"><?= htmlspecialchars($customer['name']) ?></td>
                    <td data-label="Telepon"><?= htmlspecialchars((string) $customer['phone']) ?></td>
                    <td data-label="Alamat"><?= htmlspecialchars((string) $customer['address']) ?></td>
                    <td data-label="Aksi"><a class="btn btn-secondary" href="index.php?route=pelanggan&edit=<?= (int) $customer['id'] ?>">Edit</a></td>
                </tr>
            <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>

<div class="modal-backdrop <?= !empty($editCustomer) ? 'active' : '' ?>" id="pelanggan-modal">
    <div class="modal">
        <div class="modal-head">
            <h3 style="margin:0;"><?= !empty($editCustomer) ? 'Edit Pelanggan' : 'Add Pelanggan' ?></h3>
            <button type="button" class="modal-close" onclick="togglePelangganModal(false)">Tutup</button>
        </div>
        <form method="post">
            <input type="hidden" name="id" value="<?= htmlspecialchars((string) ($editCustomer['id'] ?? '')) ?>">
            <input type="hidden" name="code" value="<?= htmlspecialchars((string) $nextCode) ?>">
            <div class="form-grid">
                <div><div class="small">Kode Pelanggan</div><input value="<?= htmlspecialchars((string) $nextCode) ?>" readonly></div>
                <div><div class="small">Nama Pelanggan</div><input name="name" placeholder="Nama pelanggan" value="<?= htmlspecialchars((string) ($editCustomer['name'] ?? '')) ?>" required></div>
                <div><div class="small">No. Telepon</div><input name="phone" placeholder="Nomor HP pelanggan" value="<?= htmlspecialchars((string) ($editCustomer['phone'] ?? '')) ?>"></div>
                <div><div class="small">Alamat</div><input name="address" placeholder="Alamat pelanggan" value="<?= htmlspecialchars((string) ($editCustomer['address'] ?? '')) ?>"></div>
            </div>
            <div style="margin-top:12px;"><button type="submit">Simpan Pelanggan</button></div>
        </form>
    </div>
</div>
<script>
    function togglePelangganModal(show) {
        document.getElementById('pelanggan-modal').classList.toggle('active', show);
    }
</script>
