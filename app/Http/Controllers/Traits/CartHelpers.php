<?php

namespace App\Http\Controllers\Traits;

use App\Models\Product;
use App\Models\StoreSetting;

trait CartHelpers
{
    protected function getCart(): array
    {
        $cart = session('cart', []);
        $normalizedCart = $this->normalizeCart($cart);
        $this->saveCart($normalizedCart);
        return $normalizedCart;
    }

    protected function saveCart(array $cart): void
    {
        session()->put('cart', $cart);
    }

    protected function normalizeCart(array $cart): array
    {
        $productIds = collect($cart)
            ->pluck('product_id')
            ->filter()
            ->unique()
            ->values()
            ->all();

        $products = Product::whereIn('id', $productIds)
            ->get()
            ->keyBy('id');

        return collect($cart)
            ->map(function ($item) use ($products) {
                $product = $products->get($item['product_id']);
                if (! $product) {
                    return $item;
                }

                $item['unit'] = $item['unit'] ?? $product->sellingUnit();
                $item['sale_type'] = $item['sale_type'] ?? $product->saleType();
                
                    // purchase_type: 'pcs'|'gram'|'grosir' — default based on sale_type
                    $defaultPurchase = 'pcs';
                    if (str_contains($item['sale_type'], 'gram')) {
                        $defaultPurchase = 'gram';
                    }
                    $item['purchase_type'] = $item['purchase_type'] ?? $defaultPurchase;

                    // input_method: 'berat'|'nominal'|null — default for gram products is 'berat'
                    $item['input_method'] = $item['input_method'] ?? (
                        str_contains($item['sale_type'], 'gram') ? 'berat' : null
                    );
                $item['is_tembakau'] = $this->isTembakau($product);

                $item['price'] = $item['price'] ?? (
                    ($item['purchase_type'] === 'grosir' && (float) ($product->wholesale_price ?? 0) > 0)
                        ? (float) $product->wholesale_price
                        : (float) $product->sell_price
                );

                return $item;
            })
            ->toArray();
    }

    protected function calculateCartSummary(array $cart, ?array $items = null): array
    {
        $subtotal = $items !== null
            ? (float) collect($items)->sum('subtotal')
            : (float) collect($cart)->sum(function ($item) {
                $price = (float) ($item['price'] ?? 0);
                $qty = (float) ($item['qty'] ?? 0);

                if ($item['is_tembakau'] ?? false) {
                    return $price * ($qty / 100);
                }

                return $price * $qty;
            });

        $storeSettings = $this->getStoreSettings();
        $discount = 0;
        $taxPercentage = (float) ($storeSettings->tax_percentage ?? 0);
        $taxAmount = $taxPercentage > 0
            ? round($subtotal * ($taxPercentage / 100), 2)
            : 0;

        $rounding = (int) ($storeSettings->rounding ?? 0);
        $grandTotal = $subtotal + $taxAmount - $discount;

        if ($rounding > 0) {
            $grandTotal = round($grandTotal / $rounding) * $rounding;
        }

        return [
            'subtotal' => $subtotal,
            'taxAmount' => $taxAmount,
            'discount' => $discount,
            'grandTotal' => $grandTotal,
        ];
    }

    protected function getStoreSettings(): StoreSetting
    {
        $storeSettings = StoreSetting::first();

        if (! $storeSettings) {
            $storeSettings = new StoreSetting();
            $storeSettings->tax_percentage = 0;
            $storeSettings->rounding = 0;
            $storeSettings->transaction_number_format = null;
        }

        return $storeSettings;
    }

    protected function resolveStockReduction($product, float $qty): int
    {
        return (int) round($qty);
    }

    protected function isValidCartQuantity($product, $qty): bool
    {
        if ($product->saleType() === 'gram') {
            return is_numeric($qty) && $qty > 0;
        }

        return is_int($qty + 0) || floor($qty) == $qty;
    }

    protected function isTembakau($product): bool
    {
        return $product->saleType() === 'gram' || $product->sellingUnit() === 'gram' || $product->stockUnit() === 'gram';
    }
}
