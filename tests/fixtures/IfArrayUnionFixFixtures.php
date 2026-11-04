<?php

declare(strict_types=1);

namespace voku\PHPStan\Rules\Test\fixtures;

/**
 * Both variants are arrays, but must not receive a fix: union diagnostics stay advisory.
 *
 * @param array{foo?: string}|array{bar?: int} $values
 */
function checkArrayOnlyUnion(array $values): void
{
    if ($values) {
        echo 'maybe non-empty';
    }
}
