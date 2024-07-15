<?php

namespace voku\PHPStan\Rules\Test\fixtures;

// double negative integer conditions
$a = rand(0, 1);
switch ($a) {
    case 1:
        // ...
        break;
}

// Non-empty string is never empty
$a = 'test';
switch ($a) {
    case '':
        // ...
        break;
    default:
        // ...
        break;
}

// Do not compare objects directly v1
$a = new \stdClass();
switch (true) {
    case $a != '':
        // ...
        break;
}

// Do not compare boolean and int
$a = rand(0, 1) ? true : false;
switch ($a) {
    case 1:
        // ...
        break;
}

// Do not compare boolean and string
$a = rand(0, 1) ? true : false;
switch ($a) {
    case '':
        // ...
        break;
    default:
        // ...
        break;
}

// Non-empty string is always empty
$a = 'test';
switch ($a) {
    case '':
        // ...
        break;
    default:
        // ...
        break;
}

// Use a method to check the condition e.g. `$foo->value()` instead of `$foo`.
$a = new \stdClass();
$b = rand(0, 1) ? true : false;
switch (true) {
    case $a && !$b:
        // ...
        break;
}

// Do not compare objects directly v2
switch (true) {
    case '2032-03-04' <= new \DateTimeImmutable():
        // ...
        break;
    case '2032-03-04' >= new \DateTimeImmutable():
        // ...
        break;
    case '2032-03-04' == new \DateTimeImmutable():
        // ...
        break;
}

class A1Switch
{
    public function foo(self $bar)
    {
        // Allow this and static comparison
        switch (true) {
            case $bar === $this:
                //
                break;
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
$b = isset($a[$rand]) ? $a[$rand] : null;
$b = $a[$rand] ?? null;

if ($date instanceof \DateTimeImmutable || $date === null) {
    // Do not compare nullable objects on non-strict binary operators
    switch (true) {
        case $date < '2013-04-05':
            //
            break;
    }

    // Allow strict comparison
    switch (true) {
        case $date === null:
            //
            break;
    }

    // Do not compare nullable DateTime
    switch (true) {
        case $date < new \DateTimeImmutable():
            //
            break;
    }

    if ($date !== null) {
        // Allow DateTime comparison when type is certain
        switch (true) {
            case $date < new \DateTimeImmutable():
                //
                break;
        }
    }
}

// Allow AND && OR checks on objects
$a = random_int(0, 1) ? new \SplFixedArray(2) : null;
$b = random_int(0, 1) ? new \SplFixedArray(2) : null;
$c = 1;

switch (true) {
    case (bool)$b:
        // ...
        break;
}

switch (true) {
    case !$b:
        // ...
        break;
}

switch (true) {
    case !$b && $c:
        // ...
        break;
}

switch (true) {
    case $c && !$b:
        // ...
        break;
}

switch (true) {
    case !$c && $b:
        // ...
        break;
}

switch (true) {
    case $a !== null:
        // ...
        break;
}

switch (true) {
    case $b && $a !== null:
        // ...
        break;
}

// do not report this
$postTmp = $_POST ?? [];
$getTmp = $_GET ?? [];
switch (true) {
    case isset($_REQUEST) && ($getTmp + $postTmp) != $_REQUEST:
        // ...
        break;
}

/** @var numeric $a */
$a = '1';
switch (true) {
    case (bool)$a && '0':
        // ...
        break;
}

switch (true) {
    case (bool)$a && '':
        // ...
        break;
}

switch (true) {
    case (bool)$a && false:
        // ...
        break;
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
function hasPermissionsSwitch($permission): bool
{
    switch ($permission) {
        case FooConst::USER_PERMISSION_NONE:
            return false;
        default:
            return true;
    }
}

// check assignment in condition
switch (true) {
    case 1 === 1 && $b = rand(0, 1):
        // ...
        break;
}

while (1 === 1 && $a = rand(0, 1)) {
    // ...
}

// check yoda - ok
$foo = 'generated';
switch (true) {
    case !is_dir("/tmp/" . $foo) && !mkdir("/tmp/" . $foo) && !is_dir("/tmp/" . $foo):
        // ...
        break;
}

// check yoda - error
$i = 5;
switch (true) {
    case 4 > $i:
        // ...
        break;
}

switch (true) {
    case rand(0, 10) > 5:
        $storage_id = "NULL";
        break;
    default:
        $storage_id = rand(0, 10) > 5 ? rand() : null;
        break;
}

switch ($storage_id) {
    case "NULL":
        // ...
        break;
}

/** @var array<array-key,mixed> $customer */
$customer = [];
switch (true) {
    case $customer == false:
        // ...
        break;
}

/** @var array<array-key,mixed> $customer */
$customer = [];
switch ($customer) {
    case true:
        // ...
        break;
    default:
        // ...
        break;
}

/** @var array<array-key,mixed> $customer */
$customer = [];
switch (true) {
    case $customer == false:
        // ...
        break;
}

/** @phpstan-var non-empty-list<1> $customer */
$customer = [1, 1, 1];
switch (true) {
    case $customer == false:
        // ...
        break;
}
