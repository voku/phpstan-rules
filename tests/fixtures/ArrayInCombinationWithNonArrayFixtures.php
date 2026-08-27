<?php

declare(strict_types=1);

namespace voku\PHPStan\Rules\Test\fixtures;

/**
 * Operands for the "array (...) in combination with non-array (...) is not allowed." check of
 * ExtendedBinaryOpRule and ExtendedAssignOpRule.
 *
 * Every line here mixes an array with something that is not an array, which is exactly what the
 * check claims to report.
 */
final class ArrayInCombinationWithNonArrayFixtures
{
    /**
     * @param array<int, string> $left
     */
    public function arrayComparedToInt(array $left, int $right): bool
    {
        return $left == $right;
    }

    /**
     * @param array<int, string> $left
     */
    public function arrayComparedToString(array $left, string $right): bool
    {
        return $left == $right;
    }

    /**
     * @param array<int, string> $left
     * @param non-empty-array<int, string> $right
     */
    public function arrayComparedToNonEmptyArray(array $left, array $right): bool
    {
        return $left == $right;
    }

    /**
     * @param array<int, string> $left
     *
     * @return array<int, string>
     */
    public function arrayAddedToArray(array $left): array
    {
        $left += ['x'];

        return $left;
    }
}
