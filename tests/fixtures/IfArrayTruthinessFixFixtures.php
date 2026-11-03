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
