<?php

declare(strict_types=1);

namespace voku\PHPStan\Rules\Test\fixtures;

/**
 * @param array<int, string> $values
 */
function checkArrayTruthiness(array $values): void
{
    if ($values) {
        echo 'not empty';
    }

    if (!$values) {
        echo 'empty';
    }
}

/**
 * Nullable arrays must never be rewritten to count(): count(null) is not equivalent.
 *
 * @param array<int, string>|null $maybe
 */
function checkNullableArray(?array $maybe): void
{
    if ($maybe) {
        echo 'maybe non-empty';
    }
}

/**
 * A proven non-empty array already has a different diagnostic.
 */
function checkDefinitelyNonEmptyArray(): void
{
    $values = ['non-empty'];
    if ($values) {
        echo 'always true';
    }
}
