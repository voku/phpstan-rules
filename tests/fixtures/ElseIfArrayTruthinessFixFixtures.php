<?php

declare(strict_types=1);

namespace voku\PHPStan\Rules\Test\fixtures;

/**
 * @param array<int, string> $values
 */
function checkElseIfArrayTruthiness(array $values, bool $check): void
{
    if ($check) {
        echo 'first';
    } elseif ($values) {
        echo 'not empty';
    }

    if ($check) {
        echo 'first';
    } elseif (!$values) {
        echo 'empty';
    }
}
