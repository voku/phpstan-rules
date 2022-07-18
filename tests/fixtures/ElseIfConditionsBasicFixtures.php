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
