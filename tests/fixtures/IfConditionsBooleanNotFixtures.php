<?php

namespace voku\PHPStan\Rules\Test\fixtures;

// Use a method to check the condition e.g. `$foo->value()` instead of `$foo`.
$a = new \stdClass();
if (!$a) {
    // ...
}

// Allow AND && OR checks on objects
$a = random_int(0, 1) ? new \SplFixedArray(2) : null;
$b = random_int(0, 1) ? new \SplFixedArray(2) : null;
$c = 1;
if ($b) {
    // ...
}
if (!$b) {
    // ...
}
if (!$b && $c) {
    // ...
}
if ($c && !$b) {
    // ...
}
if (!$c && $b) {
    // ...
}
if ($a !== null) {
    // ...
}
if ($b && $a !== null) {
    // ...
}

// check for use "count()"
$b = rand(0, 1) ? [] : [true];
if (!$b) {
    // ...
}

// Error: stdClass with BooleanNot in a non-if context
$j = new \stdClass();
$k = !$j;

// OK: string negation is fine (not a class)
$l = rand(0, 1) ? 'foo' : '';
$m = !$l;

// OK: integer negation is fine
$n = rand(0, 5);
$o = !$n;
