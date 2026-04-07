<?php

namespace voku\PHPStan\Rules\Test\fixtures;

// Use a method to check the condition e.g. `$foo->value()` instead of `$foo`.
$a = new \stdClass();
$b = rand(1, 2);
if ($a || $b) {
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

// Use a method to check the condition: stdClass on right side of OR
$d = new \stdClass();
$e = rand(0, 1) ? true : false;
if ($e || $d) {
    // ...
}

// OK: boolean || integer
$f = rand(0, 1) ? true : false;
$g = rand(0, 5);
if ($f || $g) {
    // ...
}

// OK: nullable SplFixedArray in OR with null-check
$h = random_int(0, 1) ? new \SplFixedArray(2) : null;
$i = random_int(0, 1) ? new \SplFixedArray(2) : null;
if ($h !== null || $i !== null) {
    // ...
}
