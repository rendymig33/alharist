<?php
$typeLabel = match ($transaction['transaction_type']) {
    'switching_dana' => 'SWITCHING',
    'pembelian' => 'PEMBELIAN',
    'dana_masuk' => 'DANA MASUK',
    'penjualan' => 'PENJUALAN',
    'pelunasan_hutang' => 'PELUNASAN HTG',
    default => strtoupper((string) $transaction['transaction_type']),
};
$sourceLabel = trim((string) ($transaction['source_bank_name'] ?? ''));
$targetLabel = trim((string) ($transaction['target_bank_name'] ?? ''));
?>
<tr>
    <td class="date" data-label="Tanggal"><?= htmlspecialchars((string) $transaction['transaction_date']) ?></td>
    <td class="desc" data-label="Keterangan">
        <span class="desc-main"><?= htmlspecialchars($typeLabel) ?></span>
        <span class="desc-sub"><?= htmlspecialchars((string) ($transaction['notes'] ?: 'Tanpa catatan')) ?></span>
        <?php if ($sourceLabel !== '' && $targetLabel !== ''): ?>
            <span class="desc-sub"><?= htmlspecialchars($sourceLabel) ?> Ke <?= htmlspecialchars($targetLabel) ?></span>
        <?php elseif ($sourceLabel !== ''): ?>
            <span class="desc-sub">Dari: <?= htmlspecialchars($sourceLabel) ?></span>
        <?php elseif ($targetLabel !== ''): ?>
            <span class="desc-sub">Ke: <?= htmlspecialchars($targetLabel) ?></span>
        <?php endif; ?>
    </td>
    <td class="amount cr" style="text-align:right;" data-label="Kredit">
        <?= $transaction['kredit'] > 0 ? number_format($transaction['kredit'], 0, ',', '.') : '-' ?>
    </td>
    <td class="amount db" style="text-align:right;" data-label="Debet">
        <?= $transaction['debet'] > 0 ? number_format($transaction['debet'], 0, ',', '.') : '-' ?>
    </td>
    <td class="balance" style="text-align:right;" data-label="Saldo">
        <div style="font-weight: 800; <?= $transaction['ending_balance'] < 0 ? 'color: var(--red);' : '' ?>">
            <?= number_format((float) ($transaction['ending_balance'] ?? 0), 0, ',', '.') ?>
        </div>
    </td>
    <td style="text-align:right;" data-label="Aksi">
        <?php if (($transaction['source_module'] ?? '') === 'manual'): ?>
            <button type="button" class="btn btn-danger delete-transaction-btn" style="padding: 4px 8px; font-size: 11px;" data-transaction-id="<?= (int) $transaction['id'] ?>" data-vault-id="<?= (int) $vaultId ?>">Delete</button>
        <?php else: ?>
            <span class="small" style="font-size:10px;"><?= htmlspecialchars((string) $transaction['source_module']) ?></span>
        <?php endif; ?>
    </td>
</tr>
