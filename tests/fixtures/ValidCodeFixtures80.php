<?php

declare(strict_types=1);

namespace voku\PHPStan\Rules\Test\fixtures;

/**
 * The PHP 8.0+ half of ValidCodeFixtures: constructs that cannot live in a file which the PHP 7.4
 * CI job may parse.
 *
 * Nothing in this file may produce an error for any of the shipped rules.
 */
final class ValidCodeFixtures80
{
    public function matchUsesStrictlyTypedArms(string $value): int
    {
        return match ($value) {
            'a' => 1,
            'b' => 2,
            default => 0,
        };
    }

    public function matchOnTrueUsesRealConditions(int $value): string
    {
        return match (true) {
            $value > 10 => 'big',
            $value > 0 => 'small',
            default => 'none',
        };
    }

    public function nullsafeCallIsAllowed(?\DateTimeImmutable $createdAt): ?int
    {
        return $createdAt?->getTimestamp();
    }
}
