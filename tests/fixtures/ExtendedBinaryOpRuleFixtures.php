<?php

// ---------------------------------------------------
// ERRORS
// ---------------------------------------------------

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

// OK: null-coalesce is not handled by this rule (ExtendedBinaryOpRule skips Coalesce nodes)
$nonNull = 'always-set';
$coalesced = $nonNull ?? 'default';

// Error: comparison between string and bool
function lall_bool_string_error(bool $a): string
{
    if ($a == '') {
        // ...
    }
}

// OK: comparison between string and string
function lall_string_string(string $a, string $b): string
{
    if ($a == $b) {
        // ...
    }
}

// OK: comparison between int and int
function lall_int_int(int $a, int $b): bool
{
    return $a == $b;
}
