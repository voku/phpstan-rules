<?php

// ---------------------------------------------------
// ERRORS
// ---------------------------------------------------

use voku\PHPStan\Rules\Test\fixtures\MyId;

$a = "Hello World!";
$a += 6;

$a = "Hello World!";
$a += [];

$a = "Hello World!";
$a .= (new stdClass());

$a = [0 => 'foo'];
$a += [];

/**
 * @param array{lall: int} $a
 */
function lall_non_empty_error(array $a): string
{
    $a['foo'] = 1;
    
    $a += 'foo';
}

function lall_error(int $a): string
{
    $a += 'foo';
}

// check for "__toString()" usage
function doStuff_v2(): string {
    $id = new MyId('donut');

    $return = 'My favorite identifier is ';
    $return .= $id;

    return $return;
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

/**
 * @param array{lall: int} $a
 */
function lall_non_empty(array $a): string
{
    $a['foo'] = 1;

    $a += ['bar' => 3];
}

/**
 * @param array<array-key, mixed> $a
 * @param array<array-key, mixed> $b
 */
function lall_non_empty_v3(array $a, array $b): array
{
    $c = $a + $b;
    
    $a += $c;
    
    return $a;
}

function lall_non_empty_v2(): string
{
    $a = [];
    $a['foo'] = 1;

    $a += ['bar' => 3];
}
