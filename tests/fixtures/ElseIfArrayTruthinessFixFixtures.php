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

/** @param array<int, string> $input */
function checkElseIfUnsafeFixes(bool $flag, bool $other, array $input): void
{
    if ($flag) {
        $positive = $input;
        $negative = $input;
    }
    if ($other) {
        echo 'first';
    } elseif ($positive) {
        echo 'positive';
    }
    if ($other) {
        echo 'first';
    } elseif (!$negative) {
        echo 'negative';
    }
    if ($other) {
        echo 'first';
    } elseif (! /* keep this comment */ $input) {
        echo 'comment';
    }
}
