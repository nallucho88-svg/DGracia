<?php

namespace App\Libraries;

use App\Models\DiscountModel;
use App\Models\CouponModel;

class DiscountService
{
    /**
     * Calculate final price and discount details for a single product.
     *
     * @param object|array $product
     * @param bool $isLoyalUser
     * @return array
     */
    public function calculateProductDiscount($product, bool $isLoyalUser = false): array
    {
        $originalPrice = (float) (is_object($product) ? $product->price : $product['price']);
        $productId     = is_object($product) ? $product->id : $product['id'];
        $categoryId    = is_object($product) ? $product->category_id : $product['category_id'];

        $now = date('Y-m-d H:i:s');
        $discountModel = new DiscountModel();

        $activeDiscounts = $discountModel->where('status', 1)
            ->groupStart()
                ->where('start_date IS NULL')
                ->orWhere('start_date <=', $now)
            ->groupEnd()
            ->groupStart()
                ->where('end_date IS NULL')
                ->orWhere('end_date >=', $now)
            ->groupEnd()
            ->findAll();

        $bestDiscountAmount = 0;
        $appliedDiscountTitle = null;

        foreach ($activeDiscounts as $discount) {
            $applies = false;
            if ($discount->type === 'product' && (int)$discount->product_id === (int)$productId) {
                $applies = true;
            } elseif ($discount->type === 'category' && (int)$discount->category_id === (int)$categoryId) {
                $applies = true;
            } elseif ($discount->type === 'general') {
                $applies = true;
            }

            if ($applies) {
                $amount = 0;
                if ($discount->discount_type === 'percentage') {
                    $amount = $originalPrice * ((float)$discount->value / 100);
                } else {
                    $amount = (float)$discount->value;
                }

                if ($amount > $bestDiscountAmount) {
                    $bestDiscountAmount = $amount;
                    $appliedDiscountTitle = $discount->title;
                }
            }
        }

        $currentPrice = max(0, $originalPrice - $bestDiscountAmount);

        // Cliente fiel discount
        if ($isLoyalUser) {
            $loyaltyDiscount = $discountModel->where('type', 'loyalty')
                ->where('status', 1)
                ->groupStart()
                    ->where('start_date IS NULL')
                    ->orWhere('start_date <=', $now)
                ->groupEnd()
                ->groupStart()
                    ->where('end_date IS NULL')
                    ->orWhere('end_date >=', $now)
                ->groupEnd()
                ->first();

            if ($loyaltyDiscount) {
                $loyaltyAmount = 0;
                if ($loyaltyDiscount->discount_type === 'percentage') {
                    $loyaltyAmount = $currentPrice * ((float)$loyaltyDiscount->value / 100);
                } else {
                    $loyaltyAmount = (float)$loyaltyDiscount->value;
                }
                $currentPrice = max(0, $currentPrice - $loyaltyAmount);
            }
        }

        $totalDiscountAmount = $originalPrice - $currentPrice;
        $discountPercentage  = $originalPrice > 0 ? round(($totalDiscountAmount / $originalPrice) * 100) : 0;

        return [
            'original_price'      => $originalPrice,
            'final_price'         => $currentPrice,
            'discount_amount'     => $totalDiscountAmount,
            'discount_percentage' => $discountPercentage,
            'has_discount'        => $totalDiscountAmount > 0,
            'discount_title'      => $appliedDiscountTitle,
            'formatted_original'  => '$' . number_format($originalPrice, 0, ',', '.'),
            'formatted_final'     => '$' . number_format($currentPrice, 0, ',', '.'),
        ];
    }

    /**
     * Validate a coupon code and calculate its discount amount.
     */
    public function validateCoupon(string $code, float $cartSubtotal, int $userId = 0, bool $isLoyalUser = false): array
    {
        $couponModel = new CouponModel();
        $now = date('Y-m-d H:i:s');

        $coupon = $couponModel->where('code', strtoupper(trim($code)))
            ->where('status', 1)
            ->first();

        if (!$coupon) {
            return ['valid' => false, 'message' => 'El cupón no existe o está inactivo.'];
        }

        if ($coupon->start_date && $coupon->start_date > $now) {
            return ['valid' => false, 'message' => 'El cupón aún no está vigente.'];
        }

        if ($coupon->end_date && $coupon->end_date < $now) {
            return ['valid' => false, 'message' => 'El cupón ya ha vencido.'];
        }

        if ($coupon->max_uses > 0 && $coupon->used_count >= $coupon->max_uses) {
            return ['valid' => false, 'message' => 'El cupón ya alcanzó el límite máximo de usos.'];
        }

        if ($coupon->loyal_only && !$isLoyalUser) {
            return ['valid' => false, 'message' => 'Este cupón es exclusivo para clientes fieles.'];
        }

        $discountValue = 0;
        if ($coupon->discount_type === 'percentage') {
            $discountValue = $cartSubtotal * ((float)$coupon->value / 100);
        } else {
            $discountValue = min($cartSubtotal, (float)$coupon->value);
        }

        return [
            'valid'             => true,
            'message'           => '¡Cupón aplicado exitosamente!',
            'coupon'            => $coupon,
            'discount_amount'   => $discountValue,
            'formatted_amount'  => '$' . number_format($discountValue, 0, ',', '.'),
            'code'              => $coupon->code,
        ];
    }

    /**
     * Resumen económico centralizado para Carrito, Checkout, POS y Pedidos.
     */
    public function calculateSummary(array $cartItems, ?array $appliedCoupon = null, float $deliveryCost = 0.0): array
    {
        $originalSubtotal = 0.0;
        $productDiscountTotal = 0.0;
        $subtotalAfterProductDiscount = 0.0;

        foreach ($cartItems as $item) {
            $qty = max(1, (int)($item['quantity'] ?? $item['qty'] ?? 1));
            $normPrice = (float)($item['normal_price'] ?? $item['original_price'] ?? $item['price'] ?? 0);
            $finalPrice = (float)($item['price'] ?? 0);

            $originalSubtotal += ($normPrice * $qty);
            $subtotalAfterProductDiscount += ($finalPrice * $qty);
            if (!empty($item['has_discount']) || $normPrice > $finalPrice) {
                $discPerUnit = max(0, $normPrice - $finalPrice);
                $productDiscountTotal += ($discPerUnit * $qty);
            }
        }

        $couponDiscountTotal = 0.0;
        $couponCode = null;
        if ($appliedCoupon && !empty($appliedCoupon['discount_amount'])) {
            $couponDiscountTotal = (float)$appliedCoupon['discount_amount'];
            $couponCode = $appliedCoupon['code'] ?? null;
        }

        $subtotalAfterCoupon = max(0, $subtotalAfterProductDiscount - $couponDiscountTotal);
        $finalTotal = max(0, $subtotalAfterCoupon + $deliveryCost);

        return [
            'original_price_subtotal'          => $originalSubtotal,
            'product_discount_total'           => $productDiscountTotal,
            'subtotal_after_product_discount'  => $subtotalAfterProductDiscount,
            'coupon_code'                      => $couponCode,
            'coupon_discount_total'            => $couponDiscountTotal,
            'subtotal_after_coupon'            => $subtotalAfterCoupon,
            'delivery_cost'                    => $deliveryCost,
            'final_total'                      => $finalTotal,
            'formatted' => [
                'original_price_subtotal'          => '$' . number_format($originalSubtotal, 0, ',', '.'),
                'product_discount_total'           => '-$' . number_format($productDiscountTotal, 0, ',', '.'),
                'subtotal_after_product_discount'  => '$' . number_format($subtotalAfterProductDiscount, 0, ',', '.'),
                'coupon_discount_total'            => '-$' . number_format($couponDiscountTotal, 0, ',', '.'),
                'subtotal_after_coupon'            => '$' . number_format($subtotalAfterCoupon, 0, ',', '.'),
                'delivery_cost'                    => '$' . number_format($deliveryCost, 0, ',', '.'),
                'final_total'                      => '$' . number_format($finalTotal, 0, ',', '.'),
            ]
        ];
    }
}
