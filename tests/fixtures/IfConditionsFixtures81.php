<?php

namespace voku\PHPStan\Rules\Test\fixtures;

// This `if` is necessary to test the \PHPStan\Type\UnionType
if (in_array($isBulbOn, [BulbOn::ON, BulbOn::OFF], true)) {
    // Allow strict comparisons for ENUMs
    if (BulbOn::ON === $isBulbOn) {
        //
    }
    // Do not compare objects directly
    if (BulbOn::ON > $isBulbOn) {
        //
    }
}
