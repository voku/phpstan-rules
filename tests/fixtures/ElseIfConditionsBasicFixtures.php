<?php

namespace voku\PHPStan\Rules\Test\fixtures;

// check for "please use count()"
$a = [];
if ($a) {
    // ...
}

$b = rand(0, 1) ? true : false;
if ($b) {
    // ...
} elseif ($a) {
    // ...
}

$c = new \stdClass();
if ($b) {
    // ...
} elseif ($c) {
    // ...
}

// check for use "count()"
$b = rand(0, 1) ? [] : [true];
if ($a) {
    // ...
} elseif (!$b) {
    // ...
}

// OK: boolean in elseif is fine
$d = rand(0, 1) ? true : false;
$e = rand(0, 1) ? true : false;
if ($d) {
    // ...
} elseif ($e) {
    // ...
}

// Error: non-empty array in elseif is flagged
$f = ['x'];
$g = rand(0, 1) ? true : false;
if ($g) {
    // ...
} elseif ($f) {
    // ...
}
