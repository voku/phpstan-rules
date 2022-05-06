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

// Do not compare objects directly v1
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

// Use a method to check the condition e.g. `$foo->value()` instead of `$foo`.
$a = new \stdClass();
$b = rand(0, 1) ? true : false;
if ($a && !$b) {
    // ...
}

// Do not compare objects directly v2
if ('2032-03-04' <= new \DateTimeImmutable()) {
  // ...
}
if ('2032-03-04' >= new \DateTimeImmutable()) {
  // ...
}
if ('2032-03-04' == new \DateTimeImmutable()) {
  // ...
}
