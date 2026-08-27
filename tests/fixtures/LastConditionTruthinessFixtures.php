<?php

declare(strict_types=1);

namespace voku\PHPStan\Rules\Test\fixtures;

final class LastConditionTruthinessFixtures
{
    /**
     * @param non-empty-array<int, int> $items
     */
    public function lastAlwaysTrue(bool $gate, array $items): bool
    {
        if ($gate) {
            return false;
        } elseif ($items) {
            return true;
        }

        return false;
    }

    /**
     * @param non-empty-array<int, int> $items
     */
    public function lastAlwaysFalse(bool $gate, array $items): bool
    {
        if ($gate) {
            return false;
        } elseif (!$items) {
            return true;
        }

        return false;
    }
}
