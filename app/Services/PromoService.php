<?php

namespace App\Services;

class PromoService
{
    /**
     * Central catalog of valid promo codes and their discount percentage.
     * Used by both the cashier (kasir) and the member self-order flow so the
     * same codes work everywhere.
     *
     * @var array<string, int>
     */
    public const CODES = [
        'MISSYOU20' => 20,
        'SWEET20' => 20,
        'STRONG15' => 15,
        'PREMIUM15' => 15,
        'COFFEEWELCOME' => 10,
        'COFFEE10' => 10,
    ];

    /**
     * Normalize a raw user input into a comparable promo code.
     */
    public static function normalize(string $code): string
    {
        return strtoupper(trim($code));
    }

    /**
     * Whether the given code is a valid, known promo code.
     */
    public static function isValid(string $code): bool
    {
        return array_key_exists(self::normalize($code), self::CODES);
    }

    /**
     * Get the discount percentage for a code (0 if invalid).
     */
    public static function discountPercentFor(string $code): int
    {
        return self::CODES[self::normalize($code)] ?? 0;
    }
}
