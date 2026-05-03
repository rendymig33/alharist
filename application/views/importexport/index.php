<style>
    .import-export-actions {
        display: grid;
        gap: 12px;
    }

    @media (max-width: 640px) {
        .import-export-actions .btn {
            width: 100%;
        }
    }
</style>
<div class="two-col">
    <div class="card">
        <h3>Export Master</h3>
        <div class="import-export-actions">
            <a class="btn" href="index.php?route=importexport&download=barang">Export Master Barang Excel</a>
            <a class="btn-secondary btn" href="index.php?route=importexport&download=pelanggan">Export Master Pelanggan Excel</a>
        </div>
        <p class="small">File export memakai format `.xls` yang bisa langsung dibuka di Microsoft Excel.</p>
    </div>
    <div class="card">
        <h3>Import Data Excel</h3>
        <form method="post" enctype="multipart/form-data">
            <select name="type" required>
                <option value="barang">Import Barang</option>
                <option value="pelanggan">Import Pelanggan</option>
            </select>
            <input style="margin-top:12px;" type="file" name="file" accept=".csv" required>
            <div class="small" style="margin:10px 0;">Gunakan file CSV dari Excel agar import stabil di desktop lokal.</div>
            <button type="submit" class="btn btn-success">Upload dan Import</button>
        </form>
    </div>
</div>
