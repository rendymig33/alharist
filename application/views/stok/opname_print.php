<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cetak Kartu Stok</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            font-size: 11px;
            color: #000;
            margin: 0;
            padding: 20px;
        }
        .no-print {
            margin-bottom: 20px;
            text-align: right;
        }
        .btn {
            padding: 8px 16px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            font-weight: bold;
        }
        .btn-print { background: #16a34a; color: white; }
        .btn-close { background: #64748b; color: white; margin-left: 10px; }
        
        .stock-card {
            border: 2px solid #000;
            margin-bottom: 20px;
            page-break-inside: avoid;
            border-radius: 4px;
            overflow: hidden;
        }
        .card-header {
            border-bottom: 2px solid #000;
            padding: 10px;
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
        }
        .header-left, .header-right {
            width: 48%;
        }
        .header-title {
            text-align: center;
            font-size: 14px;
            font-weight: bold;
            text-transform: uppercase;
            margin-bottom: 10px;
            letter-spacing: 2px;
        }
        .info-row {
            display: flex;
            margin-bottom: 4px;
        }
        .info-label {
            width: 80px;
            font-weight: bold;
        }
        .info-value {
            flex: 1;
            border-bottom: 1px dotted #000;
        }
        
        table {
            width: 100%;
            border-collapse: collapse;
        }
        th, td {
            border: 1px solid #000;
            padding: 6px;
            text-align: center;
            vertical-align: middle;
        }
        th {
            background-color: #f0f0f0;
            font-weight: bold;
            text-transform: uppercase;
            -webkit-print-color-adjust: exact;
            color-adjust: exact;
        }
        td.empty-row {
            height: 20px;
        }
        .text-left { text-align: left; }
        
        @media print {
            body {
                padding: 0;
                margin: 0;
            }
            .no-print {
                display: none !important;
            }
            @page {
                size: A4 portrait;
                margin: 1cm;
            }
        }
    </style>
</head>
<body onload="window.print()">

    <div class="no-print">
        <button onclick="window.print()" class="btn btn-print">Print Kartu Stok</button>
        <button onclick="window.close()" class="btn btn-close">Tutup</button>
    </div>

    <?php if (empty($items)): ?>
        <div style="text-align: center; padding: 50px; border: 1px solid #000;">
            Tidak ada barang yang ditemukan.
        </div>
    <?php else: ?>
        <?php foreach ($items as $item): ?>
            <div class="stock-card">
                <div class="card-header">
                    <div class="header-left">
                        <div class="info-row">
                            <div class="info-label">KODE</div>
                            <div class="info-value">: <?= htmlspecialchars((string)($item['code'] ?? '-')) ?> / <?= htmlspecialchars((string)($item['barcode'] ?? '-')) ?></div>
                        </div>
                        <div class="info-row">
                            <div class="info-label">NAMA</div>
                            <div class="info-value">: <strong><?= htmlspecialchars((string)($item['name'] ?? '-')) ?></strong></div>
                        </div>
                        <div class="info-row">
                            <div class="info-label">KATEGORI</div>
                            <div class="info-value">: <?= htmlspecialchars((string)($item['category'] ?? '-')) ?></div>
                        </div>
                    </div>
                    <div style="width: 4%;"></div>
                    <div class="header-right">
                        <div class="header-title">KARTU STOK</div>
                        <div class="info-row">
                            <div class="info-label">SATUAN</div>
                            <div class="info-value">: <?= htmlspecialchars((string)($item['unit_large'] ?? '')) ?> / <?= htmlspecialchars((string)($item['unit_small'] ?? '')) ?> (Isi <?= (int)($item['small_unit_qty'] ?? 1) ?>)</div>
                        </div>
                        <div class="info-row">
                            <div class="info-label">STOK AWAL</div>
                            <div class="info-value">: <?= htmlspecialchars((string)($item['stock_display'] ?? '0')) ?></div>
                        </div>
                    </div>
                </div>
                
                <table>
                    <thead>
                        <tr>
                            <th width="10%" rowspan="2">Tanggal</th>
                            <th width="20%" rowspan="2">Keterangan</th>
                            <th width="20%" colspan="2">Masuk</th>
                            <th width="20%" colspan="2">Keluar</th>
                            <th width="20%" colspan="2">Sisa</th>
                            <th width="10%" rowspan="2">Paraf</th>
                        </tr>
                        <tr>
                            <th width="10%"><?= htmlspecialchars((string)($item['unit_large'] ?? 'Bks')) ?></th>
                            <th width="10%"><?= htmlspecialchars((string)($item['unit_small'] ?? 'Pcs')) ?></th>
                            <th width="10%"><?= htmlspecialchars((string)($item['unit_large'] ?? 'Bks')) ?></th>
                            <th width="10%"><?= htmlspecialchars((string)($item['unit_small'] ?? 'Pcs')) ?></th>
                            <th width="10%"><?= htmlspecialchars((string)($item['unit_large'] ?? 'Bks')) ?></th>
                            <th width="10%"><?= htmlspecialchars((string)($item['unit_small'] ?? 'Pcs')) ?></th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php for ($i = 0; $i < 6; $i++): ?>
                        <tr>
                            <td class="empty-row"></td>
                            <td class="empty-row"></td>
                            <td class="empty-row"></td>
                            <td class="empty-row"></td>
                            <td class="empty-row"></td>
                            <td class="empty-row"></td>
                            <td class="empty-row"></td>
                            <td class="empty-row"></td>
                            <td class="empty-row"></td>
                        </tr>
                        <?php endfor; ?>
                    </tbody>
                </table>
            </div>
        <?php endforeach; ?>
    <?php endif; ?>

</body>
</html>
