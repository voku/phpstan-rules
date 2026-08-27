<?php

declare(strict_types=1);

namespace voku\PHPStan\Rules\Test\fixtures;

final class InArrayLooseComparisonFixtures
{
    /**
     * @param array<int, string> $haystack
     */
    public function intNeedleInStringHaystack(int $needle, array $haystack): bool
    {
        return \in_array($needle, $haystack);
    }

    /**
     * @param array<int, int> $haystack
     */
    public function stringNeedleInIntHaystack(string $needle, array $haystack): bool
    {
        return \in_array($needle, $haystack);
    }

    /**
     * @param array<int, string> $haystack
     */
    public function explicitFalse(int $needle, array $haystack): bool
    {
        return \in_array($needle, $haystack, false);
    }

    /**
     * @param array<int, string> $haystack
     *
     * @return int|false
     */
    public function arraySearch(int $needle, array $haystack)
    {
        return \array_search($needle, $haystack);
    }

    /**
     * @param array<int, string> $haystack
     */
    public function floatNeedle(float $needle, array $haystack): bool
    {
        return \in_array($needle, $haystack);
    }
}
