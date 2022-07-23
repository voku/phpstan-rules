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

class A1
{
    public function foo(self $bar)
    {
        // Allow this and static comparison
        if ($bar === $this) {
            //
        }
    }
}

// Intercept binary op wherever they are
$var = function(): bool {
    return '2032-03-04' <= new \DateTimeImmutable();
};

// Allow NULL checks on objects
$a = [new \DateTime(), new \DateTime()];
$rand = random_int(0, 5);
if (isset($a[$rand])) {
    $b = $a[$rand];
} else {
    $b = null;
}
$b = $a[$rand] ?? null;

if ($date instanceof \DateTimeImmutable || $date === null) {
    // Do not compare nullable objects on non-strict binary operators
    if ($date < '2013-04-05') {
        //
    }

    // Allow strict comparison
    if ($date === null) {
        //
    }

    // Do not compare nullable DateTime
    if ($date < new \DateTimeImmutable()) {
        //
    }

    if ($date !== null) {
        // Allow DateTime comparison when type is certain
        if ($date < new \DateTimeImmutable()) {
            //
        }
    }
}

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

