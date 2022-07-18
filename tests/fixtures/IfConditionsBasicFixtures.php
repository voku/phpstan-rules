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
