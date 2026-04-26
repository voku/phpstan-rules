<?php

namespace voku\PHPStan\Rules\Test\fixtures;

// Use a method to check the condition e.g. `$foo->value()` instead of `$foo`.
$a = new \stdClass();
$b = $a ? $a->lall : 'foo';
$b = $a ?: 'foo';
$b = $a ?? 'foo';

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

// do not report this
function doStuff(?\stdClass $maybeThing): \stdClass {
    $aThing = $maybeThing ?? new \stdClass();

    return $aThing;
}

// Error: stdClass in ternary without method call
$p = new \stdClass();
$q = rand(0, 5);
$r = $p ? $q : 0;

// OK: explicitly check for null using null coalesce
$s = random_int(0, 1) ? new \stdClass() : null;
$t = $s ?? new \stdClass();

// Error: disguised impossible comparison inside ternary condition
$u = count([]) !== 0 ? 'never' : 'always';

// Error: disguised impossible comparison inside shorthand ternary condition
$v = (count([]) !== 0) ?: 'fallback';

// Error: disguised tautology inside ternary condition
$w = count([]) == 0 ? 'always' : 'never';

// Error: disguised tautology inside shorthand ternary condition
$x = (count([]) == 0) ?: 'fallback';

// Error: disguised strict tautology inside ternary condition
$y = count([]) === 0 ? 'always' : 'never';

// Error: disguised strict tautology inside shorthand ternary condition
$z = (count([]) === 0) ?: 'fallback';

// OK: non-constant count comparison should not be treated as an impossible condition
$items = rand(0, 1) ? [] : [1];
$maybeEmpty = count($items) === 0 ? 'empty' : 'not-empty';
$maybeFallback = (count($items) == 0) ?: 'fallback';
