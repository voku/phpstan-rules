<?php

declare(strict_types=1);

namespace voku\PHPStan\Rules\Test\fixtures;

use DateTimeImmutable;

/**
 * Idiomatic code that follows every recommendation the rules in this package make.
 *
 * Nothing in this file may produce an error for any of the shipped rules, not even with
 * "checkForAssignments" and "checkYodaConditions" enabled. Every false positive introduced by a
 * change to IfConditionHelper shows up here.
 *
 * Each check lives in its own method with its own parameters on purpose: as soon as two conditions
 * share a variable, PHPStan narrows the type between them and a later comparison legitimately
 * becomes constant, which would make this fixture assert the opposite of what it is here for.
 */
final class ValidCodeFixtures
{
    public function stringIsEqualToLiteral(string $value): bool
    {
        return $value === 'foo';
    }

    public function stringIsNotEqualToLiteral(string $value): bool
    {
        return $value !== 'bar';
    }

    public function stringIsEmpty(string $value): bool
    {
        return $value === '';
    }

    public function stringIsNotEmpty(string $value): bool
    {
        return $value !== '';
    }

    public function integerIsZero(int $value): bool
    {
        return $value === 0;
    }

    public function integerIsNotZero(int $value): bool
    {
        return $value !== 0;
    }

    public function integerIsInRange(int $value): bool
    {
        return $value > 0 && $value <= 10;
    }

    /**
     * The rules ask for `count()` instead of using an array in a boolean context.
     *
     * @param array<int, string> $items
     */
    public function arrayIsEmpty(array $items): bool
    {
        return \count($items) === 0;
    }

    /**
     * @param array<int, string> $items
     */
    public function arrayIsNotEmpty(array $items): bool
    {
        return \count($items) > 0;
    }

    /**
     * @param array<int, string> $items
     */
    public function arrayHasADifferentSize(array $items): bool
    {
        return \count($items) !== 0;
    }

    public function objectIsNull(?DateTimeImmutable $createdAt): bool
    {
        return $createdAt === null;
    }

    public function objectIsNotNull(?DateTimeImmutable $createdAt): bool
    {
        return $createdAt !== null;
    }

    /**
     * Objects are compared through a scalar accessor instead of directly.
     */
    public function objectsAreEqual(DateTimeImmutable $left, DateTimeImmutable $right): bool
    {
        return $left->getTimestamp() === $right->getTimestamp();
    }

    public function objectIsOlder(DateTimeImmutable $left, DateTimeImmutable $right): bool
    {
        return $left->getTimestamp() < $right->getTimestamp();
    }

    /**
     * Objects with "__toString()" are cast explicitly instead of magically.
     */
    public function objectIsCastExplicitly(MyId $id): string
    {
        return 'id: ' . $id->__toString();
    }

    public function booleanIsTrue(bool $flag): bool
    {
        return $flag === true;
    }

    public function booleanIsFalse(bool $flag): bool
    {
        return $flag === false;
    }

    public function booleanIsNegated(bool $flag): bool
    {
        if (!$flag) {
            return false;
        }

        return $flag;
    }

    public function coalesceIsAllowed(?string $value): string
    {
        return $value ?? 'fallback';
    }

    public function ternaryUsesAnExplicitCondition(int $value): string
    {
        return $value > 0 ? 'positive' : 'other';
    }

    public function switchUsesStrictlyTypedCases(string $value): int
    {
        switch ($value) {
            case 'a':
                return 1;
            case 'b':
                return 2;
            default:
                return 0;
        }
    }

    /**
     * Neither operand of a concatenation is an object or an empty string constant.
     */
    public function concatenationStaysWithinStrings(string $left, string $right): string
    {
        $result = $left . $right;
        $result .= $right;

        return $result;
    }

    public function arithmeticStaysWithinNumbers(int $left, float $right): float
    {
        $sum = $left + $right;
        $sum *= 2;

        return $sum;
    }

    public function methodIsCalledAfterNarrowing(?DateTimeImmutable $createdAt): int
    {
        if ($createdAt === null) {
            return 0;
        }

        return $createdAt->getTimestamp();
    }

    public function elseIfUsesStrictComparisons(int $value): string
    {
        if ($value === 1) {
            return 'one';
        } elseif ($value === 2) {
            return 'two';
        }

        return 'many';
    }

    public function conditionsAreCombinedWithAnd(string $value, int $length): bool
    {
        return $value !== '' && $length > 0;
    }

    public function conditionsAreCombinedWithOr(string $value, int $length): bool
    {
        return $value === '' || $length === 0;
    }
}
