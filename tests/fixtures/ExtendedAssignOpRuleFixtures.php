<?php

// ---------------------------------------------------
// ERRORS
// ---------------------------------------------------

use voku\PHPStan\Rules\Test\fixtures\MyId;

echo "Hello World!" + 6;

echo "Hello World!" + [];

echo "Hello World!" * (new stdClass());

function lall_error(int $a): string
{
    if ($a == '') {
        // ...
    }
}


// ----------------------------------------------------
// OK
// ----------------------------------------------------

echo "Hello World!" . "foo";

echo "Hello World!" . 6;

echo "Hello World!" . 6.0;

$a = rand(0, 1) ? null : 'foo';
if ('Hello World!' === $a) {
    // ...
}

$a = rand(0, 1) ? null : chr(random_int(10, 20));
if (
    !empty($a) 
    &&
    $a !== 'Hello World!'
) {
    // ...
}

$a = 'a';
$b = 'b';
$c = rand(0, 1) ? true : false;
if ($a && $b && $c) {
    // ...
}
if ($a && $b) {
    // ...
}

function lall(string $a, string $b, string $v, string $z = 'foo'): string
{
    if ($a && $b && $v && $z) {
        // ...
    }
}

/**
 * @param mixed $b
 */
function lall2(string $a, $b): string
{
    if ($a == $b) {
        // ...
    }
}

function lall3(string $a, int $b): string
{
    if ($a == $b) {
        // ...
    }
}

// check for "__toString()" usage
function doStuff_v2(): string {
    $id = new MyId('donut');

    $return = 'My favorite identifier is ';
    $return .= $id;

    return $return;
}
