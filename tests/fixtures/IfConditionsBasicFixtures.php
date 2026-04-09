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
}

$c = new \stdClass();
if ($c) {
    // ...
}

// check for "non-empty"
$a = ['a'];
if ($a) {
    // ...
}

// do not report this
$postTmp = $_POST ?? [];
$getTmp = $_GET ?? [];
if (
    isset($_REQUEST)
    &&
    ($getTmp + $postTmp) != $_REQUEST
) {
    // ...
}

// check for use "count()"
$b = rand(0, 1) ? [] : [true];
if (!$b) {
    // ...
}

// OK: integer comparison is fine
$d = rand(0, 1);
if ($d > 0) {
    // ...
}

// Error: non-empty array is always non-empty in if condition  
$e = ['x', 'y'];
if (!$e) {
    // ...
}

// OK: string comparison with operator
$f = rand(0, 1) ? 'foo' : 'bar';
if ($f === 'foo') {
    // ...
}
