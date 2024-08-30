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

/** @var numeric $a */
$a = '1';
if ($a && '0') {
    // ...
}
if ($a && '') {
    // ...
}
if ($a && false) {
    // ...
}

// check for "__toString()" usage
function doStuff(): string {
    $id = new MyId('donut');

    return 'My favorite identifier is ' . $id;
}

/**
 * @param int $permission
 *
 * @phpstan-param FooConst::USER_PERMISSION_* $permission
 */
function hasPermissions($permission): bool
{
    if ($permission != FooConst::USER_PERMISSION_NONE) {
        return true;
    }

    return false;
}

// check assignment in condition
if (1 === 1 && $b = rand(0, 1)) {
    // ...
}
while (1 === 1 && $a = rand(0,1)) {
    // ...
}

// check yoda - ok
$foo = 'generated';
if (
    !is_dir("/tmp/" . $foo)
    &&
    !mkdir("/tmp/" . $foo)
    &&
    !is_dir("/tmp/" . $foo)
) {
    // ...
}
// check yoda - error
$i = 5;
if (4 > $i) {
    // ...
}

if (rand(0, 10) > 5) {
    $storage_id = "NULL";
} else {
    $storage_id = rand(0, 10) > 5 ? rand() : null;
}
if ($storage_id === "NULL") {
    // ...
}

/** @var array<array-key,mixed> $customer */
$customer = [];
if ($customer == false) {
    // ...
}

/** @var array<array-key,mixed> $customer */
$customer = [];
if ($customer === true) {
    // ...
}

/** @var array<array-key,mixed> $customer */
$customer = [];
if ($customer == false) {
    // ...
}

/** @phpstan-var non-empty-list<1> $customer */
$customer = [1, 1, 1];
if ($customer == false) {
    // ...
}

// do not report this
function doStuff2(?\stdClass $maybeThing): \stdClass {
    $aThing = $maybeThing ?? new \stdClass();

    return $aThing;
}

/** @var int<0,10> $a */
$a = 0;
if ($a == '-1') {
    // ...
}
if ($a == -1) {
    // ...
}

// Comparing array with null
$a = [1, 2, 3];
if ($a == null) {
    // ...
}
if ($a === null) {
    // ...
}

// Comparing DateTime object with integer
$date = new \DateTime();
if ($date == 123456) {
    // ...
}
if ($date === 123456) {
    // ...
}

// Comparing float with boolean and array
$a = 5.7;
if ($a == true) {
    // ...
}
if ($a == []) {
    // ...
}

// Comparing array with boolean
$a = [1, 2, 3];
if ($a == true) {
    // ...
}
if ($a === false) {
    // ...
}

// Comparing Closure with string
$a = function() { return 1; };
if ($a == 'closure') {
    // ...
}
if ($a === 'closure') {
    // ...
}

// Comparing object with null string
$a = new \stdClass();
if ($a == 'null') {
    // ...
}
if ($a === 'null') {
    // ...
}

// Comparing resource with integer
$handle = fopen('php://memory', 'r');
if ($handle == 1) {
    // ...
}
if ($handle === 1) {
    // ...
}

// Comparing boolean true with numeric string
$a = '1';
if (true == $a) {
    // ...
}
if (true === $a) {
    // ...
}

// Comparing boolean false with empty array
$a = [];
if (false == $a) {
    // ...
}
if (false === $a) {
    // ...
}

// Comparing Infinity with boolean
$a = INF;
if ($a == true) {
    // ...
}
if ($a === false) {
    // ...
}

// Comparing NaN with string
$a = NAN;
if ($a == 'NaN') {
    // ...
}
if ($a === 'NaN') {
    // ...
}

// Comparing bool with a very large number
$a = true;
$b = 999999999999999999999999999;
if ($a == $b) {
    // ...
}
if ($a === $b) {
    // ...
}

// Comparing callable with a string function name
function foo() { return 'bar'; }
$a = 'foo';
if (is_callable($a) == 'function') {
    // ...
}
if (is_callable($a) === 'function') {
    // ...
}

// Comparing a class name string with an instance
$a = new \stdClass();
if (get_class($a) == new \stdClass()) {
    // ...
}
if (get_class($a) === new \stdClass()) {
    // ...
}

// Comparing float with date string
$a = 3.14;
if ($a == '2023-10-01') {
    // ...
}
if ($a === '2023-10-01') {
    // ...
}

// Comparing boolean with JSON string
$a = true;
if ($a == '{"key": "value"}') {
    // ...
}
if ($a === '{"key": "value"}') {
    // ...
}

// Comparing numeric string with an encoded array
$a = '123';
$b = json_encode([1, 2, 3]);
if ($a == $b) {
    // ...
}
if ($a === $b) {
    // ...
}

// Comparing boolean with regular expression
$a = true;
if ($a == '/^pattern/') {
    // ...
}
if ($a === '/^pattern/') {
    // ...
}

// Comparing timestamp with file resource
$a = time();
$handle = fopen('php://memory', 'r');
if ($a == $handle) {
    // ...
}
if ($a === $handle) {
    // ...
}

// Comparing object with array
$a = new \stdClass();
$b = [1, 2, 3];
if ($a == $b) {
    // ...
}
if ($a === $b) {
    // ...
}
