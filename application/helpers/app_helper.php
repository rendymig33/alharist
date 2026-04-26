<?php
declare(strict_types=1);

function rupiah(float $amount): string
{
    return 'Rp ' . number_format($amount, 0, ',', '.');
}

function post(string $key, mixed $default = ''): mixed
{
    return $_POST[$key] ?? $default;
}

function flash(?string $message = null, string $type = 'success'): ?array
{
    if ($message !== null) {
        $_SESSION['flash'] = ['message' => $message, 'type' => $type];
        return null;
    }

    $data = $_SESSION['flash'] ?? null;
    unset($_SESSION['flash']);
    return $data;
}

function active_menu(string $route, string $currentRoute): string
{
    return str_starts_with($currentRoute, $route) ? 'active' : '';
}

function format_qty(float $value): string
{
    if ((float) (int) $value === $value) {
        return (string) (int) $value;
    }

    return rtrim(rtrim(number_format($value, 2, '.', ''), '0'), '.');
}

function unformat_number(string $value): float
{
    $clean = preg_replace('/[^\d]/', '', $value);
    return (float) ($clean === '' ? 0 : $clean);
}

function stock_to_smallest_units(int|float $largeStock, int|float $smallStock, int $smallUnitQty): int
{
    $smallUnitQty = max(1, $smallUnitQty);
    $largeStock = max(0, (int) $largeStock);
    $smallStock = max(0, (int) $smallStock);

    return ($largeStock * $smallUnitQty) + $smallStock;
}

function split_stock_units(int|float $stock, int $smallUnitQty): array
{
    $smallUnitQty = max(1, $smallUnitQty);
    $stock = max(0, (int) round($stock));

    return [
        'large' => intdiv($stock, $smallUnitQty),
        'small' => $stock % $smallUnitQty,
    ];
}

function format_stock_breakdown(int|float $stock, string $unitLarge, string $unitSmall, int $smallUnitQty): string
{
    $parts = split_stock_units($stock, $smallUnitQty);

    if ($parts['large'] > 0 && $parts['small'] > 0) {
        return $parts['large'] . ' ' . $unitLarge . ' ' . $parts['small'] . ' ' . $unitSmall;
    }

    if ($parts['large'] > 0) {
        return $parts['large'] . ' ' . $unitLarge;
    }

    return $parts['small'] . ' ' . $unitSmall;
}
