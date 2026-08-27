<?php

declare(strict_types=1);

namespace voku\PHPStan\Rules\Test\fixtures;

final class LastConditionTruthinessFixtures
{
    public function lastAlwaysTrue(bool $gate): bool
    {
        $items = [1];

        if ($gate) {
            return false;
        } elseif ($items) {
            return true;
        }

        return false;
    }

    public function lastAlwaysFalse(bool $gate): bool
    {
        $items = [1];

        if ($gate) {
            return false;
        } elseif (!$items) {
            return true;
        }

        return false;
    }
}
