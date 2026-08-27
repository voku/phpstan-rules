<?php

declare(strict_types=1);

namespace voku\PHPStan\Rules\Test\fixtures;

final class InArrayLooseComparisonNamedArgumentsFixtures
{
    /**
     * @param array<int, string> $haystack
     */
    public function reordered(int $needle, array $haystack): bool
    {
        return \in_array(haystack: $haystack, needle: $needle, strict: false);
    }

    /**
     * @param array<int, string> $haystack
     */
    public function strict(int $needle, array $haystack): bool
    {
        return \in_array(haystack: $haystack, needle: $needle, strict: true);
    }
}
