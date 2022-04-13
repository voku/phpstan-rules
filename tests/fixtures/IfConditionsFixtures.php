<?php

namespace voku\PHPStan\Rules\Test\fixtures;

// double negative integer conditions
$a = rand(0, 1);
if ($a != 0) {
    // ...
}

// Non-empty string is never empty
$a = 'test';
if ($a != '') {
    // ...
}

// Do not compare objects directly
$a = new \stdClass();
if ($a != '') {
    // ...
}

// Do not compare boolean and int
$a = rand(0, 1) ? true : false;
if ($a == 0) {
    // ...
}

// Do not compare boolean and string
$a = rand(0, 1) ? true : false;
if ($a != '') {
    // ...
}

// Non-empty string is always empty
$a = 'test';
if ($a == '') {
    // ...
}
