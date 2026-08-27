<?php

declare(strict_types=1);

namespace voku\PHPStan\Rules\Test\fixtures;

final class InArrayLooseComparisonValidFixtures
{
    /**
     * @param array<int, string> $haystack
     */
    public function strictMode(int $needle, array $haystack): bool
    {
        return \in_array($needle, $haystack, true);
    }

    /**
     * @param array<int, string> $haystack
     */
    public function stringInStrings(string $needle, array $haystack): bool
    {
        return \in_array($needle, $haystack);
    }

    /**
     * @param array<int, int> $haystack
     */
    public function intInInts(int $needle, array $haystack): bool
    {
        return \in_array($needle, $haystack);
    }

    /**
     * @param numeric-string $needle
     * @param array<int, int> $haystack
     */
    public function numericStringInInts(string $needle, array $haystack): bool
    {
        return \in_array($needle, $haystack);
    }

    /**
     * @param array<int, numeric-string> $haystack
     */
    public function intInNumericStrings(int $needle, array $haystack): bool
    {
        return \in_array($needle, $haystack);
    }

    /**
     * @param array<int, string> $haystack
     */
    public function unknownStrict(int $needle, array $haystack, bool $strict): bool
    {
        return \in_array($needle, $haystack, $strict);
    }

    /**
     * @param array<int, mixed> $haystack
     */
    public function mixedHaystack(int $needle, array $haystack): bool
    {
        return \in_array($needle, $haystack);
    }

    /**
     * @param int|string $needle
     * @param array<int, string> $haystack
     */
    public function unionNeedle($needle, array $haystack): bool
    {
        return \in_array($needle, $haystack);
    }

    public function constantHaystack(int $needle): bool
    {
        return \in_array($needle, ['foo', 'bar']);
    }

    /**
     * An unqualified call in a namespace may resolve to a local function, so the rule stays silent.
     *
     * @param array<int, string> $haystack
     */
    public function namespacedUnqualifiedCall(int $needle, array $haystack): bool
    {
        return in_array($needle, $haystack);
    }
}
