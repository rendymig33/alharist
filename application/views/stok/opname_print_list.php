<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Laporan List Stok Opname</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            font-size: 12px;
            color: #000;
            margin: 0;
            padding: 20px;
        }
        .header {
            text-align: center;
            margin-bottom: 20px;
            border-bottom: 2px solid #000;
            padding-bottom: 10px;
        }
        .header h2 {
            margin: 0 0 5px 0;
            font-size: 18px;
            text-transform: uppercase;
        }
        .header p {
            margin: 0;
            font-size: 12px;
            color: #333;
        }
        .info {
            margin-bottom: 15px;
            font-size: 12px;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }
        th, td {
            border: 1px solid #000;
            padding: 6px 8px;
            text-align: left;
            vertical-align: middle;
        }
        th {
            background-color: #f0f0f0;
            font-weight: bold;
            text-transform: uppercase;
            font-size: 11px;
            -webkit-print-color-adjust: exact;
            color-adjust: exact;
        }
        .text-right {
            text-align: right;
        }
        .text-center {
            text-align: center;
        }
        .stock-box {
            display: inline-block;
            width: 45px;
            height: 20px;
            border: 1px solid #999;
            margin-left: 5px;
        }
        
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

    <div class="no-print" style="margin-bottom: 20px; text-align: right;">
        <button onclick="window.print()" style="padding: 8px 16px; background: #16a34a; color: white; border: none; border-radius: 4px; cursor: pointer; font-weight:bold;">Print Laporan</button>
        <button onclick="window.close()" style="padding: 8px 16px; background: #64748b; color: white; border: none; border-radius: 4px; cursor: pointer; margin-left: 10px; font-weight:bold;">Tutup</button>
    </div>

    <div class="header">
        <h2>LEMBAR KERJA STOK OPNAME</h2>
        <p>Tanggal Cetak: <?= date('d M Y H:i:s') ?></p>
        <?php if (!empty($category)): ?>
            <p><strong>Kategori: <?= htmlspecialchars($category) ?></strong></p>
        <?php endif; ?>
        <?php if (!empty($keyword)): ?>
            <p><strong>Pencarian: <?= htmlspecialchars($keyword) ?></strong></p>
        <?php endif; ?>
    </div>

    <div class="info">
        <p>Total Barang: <strong><?= count($items ?? []) ?></strong></p>
    </div>

    <table>
        <thead>
            <tr>
                <th width="5%" class="text-center">No</th>
                <th width="20%">Kode / Barcode</th>
                <th width="45%">Nama Barang</th>
                <th width="15%" class="text-center">Stok Sistem</th>
                <th width="15%" class="text-center">Cek Fisik</th>
            </tr>
        </thead>
        <tbody>
            <?php if (empty($items)): ?>
                <tr>
                    <td colspan="5" class="text-center" style="padding: 20px;">Tidak ada barang yang ditemukan.</td>
                </tr>
            <?php else: ?>
                <?php $no = 1; foreach ($items as $item): ?>
                    <tr>
                        <td class="text-center"><?= $no++ ?></td>
                        <td>
                            <strong><?= htmlspecialchars((string)($item['code'] ?? '-')) ?></strong>
                            <?php if (!empty($item['barcode'])): ?>
                                <br><span style="font-size: 10px; color: #555;"><?= htmlspecialchars((string)$item['barcode']) ?></span>
                            <?php endif; ?>
                        </td>
                        <td>
                            <strong><?= htmlspecialchars((string)($item['name'] ?? '-')) ?></strong>
                            <?php if (!empty($item['category'])): ?>
                                <br><span style="font-size: 10px; color: #555;"><?= htmlspecialchars((string)$item['category']) ?></span>
                            <?php endif; ?>
                        </td>
                        <td class="text-center">
                            <strong><?= htmlspecialchars((string)($item['stock_display'] ?? '0')) ?></strong>
                        </td>
                        <td></td>
                    </tr>
                <?php endforeach; ?>
            <?php endif; ?>
        </tbody>
    </table>

</body>
</html>
