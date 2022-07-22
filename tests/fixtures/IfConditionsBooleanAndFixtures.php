<?php

namespace voku\PHPStan\Rules\Test\fixtures;

// Do not compare objects directly
$a = new \stdClass();
$b = rand(1, 2);
$b = $a && $b ? $a->lall : 'foo';

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

// Check for conditions without "if"
function foooooo($a)
{
    // ...
}
foooooo($b && $a !== null);
foooooo(0 && 1);
$c = new \stdClass();
foooooo($c && $a);
foooooo($c);
