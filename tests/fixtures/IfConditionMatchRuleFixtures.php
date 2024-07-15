<?php

namespace voku\PHPStan\Rules\Test\fixtures;

// double negative integer conditions
$a = rand(0, 1);
$result = match ($a) {
    1 => 'Not zero',
    default => null,
};

// Non-empty string is never empty
$a = 'test';
$result = match ($a) {
    '' => null,
    default => 'Not empty',
};

// Do not compare objects directly v1
$a = new \stdClass();
$result = match (true) {
    $a != '' => 'Not empty',
    default => null,
};

// Do not compare boolean and int
$a = rand(0, 1) ? true : false;
$result = match ($a) {
    true => 'Not zero',
    default => null,
};

// Do not compare boolean and string
$a = rand(0, 1) ? true : false;
$result = match (true) {
    $a != '' => 'Not empty',
    default => null,
};

// Non-empty string is always empty
$a = 'test';
$result = match ($a) {
    '' => null,
    default => 'Not empty',
};

// Use a method to check the condition e.g. `$foo->value()` instead of `$foo`.
$a = new \stdClass();
$b = rand(0, 1) ? true : false;
$result = match (true) {
    $a && !$b => 'Met condition',
    default => null,
};

// Do not compare objects directly v2
$dateComparison1 = match (true) {
    '2032-03-04' <= new \DateTimeImmutable() => 'Condition met',
    default => null,
};
$dateComparison2 = match (true) {
    '2032-03-04' >= new \DateTimeImmutable() => 'Condition met',
    default => null,
};
$dateComparison3 = match (true) {
    '2032-03-04' == new \DateTimeImmutable() => 'Condition met',
    default => null,
};

class A1Match
{
    public function foo(self $bar)
    {
        // Allow this and static comparison
        $result = match (true) {
            $bar === $this => 'Condition met',
            default => null,
        };
    }
}

// Intercept binary op wherever they are
$var = fn(): bool => '2032-03-04' <= new \DateTimeImmutable();

// Allow NULL checks on objects
$a = [new \DateTime(), new \DateTime()];
$rand = random_int(0, 5);
$b = isset($a[$rand]) ? $a[$rand] : null;
$b = $a[$rand] ?? null;

if ($date instanceof \DateTimeImmutable || $date === null) {
    // Do not compare nullable objects on non-strict binary operators
    $result1 = match (true) {
        $date < '2013-04-05' => 'Condition met',
        default => null,
    };

    // Allow strict comparison
    $result2 = match (true) {
        $date === null => 'Condition met',
        default => null,
    };

    // Do not compare nullable DateTime
    $result3 = match (true) {
        $date < new \DateTimeImmutable() => 'Condition met',
        default => null,
    };

    if ($date !== null) {
        // Allow DateTime comparison when type is certain
        $result4 = match (true) {
            $date < new \DateTimeImmutable() => 'Condition met',
            default => null,
        };
    }
}

// Allow AND && OR checks on objects
$a = random_int(0, 1) ? new \SplFixedArray(2) : null;
$b = random_int(0, 1) ? new \SplFixedArray(2) : null;
$c = 1;

$result1 = match (true) {
    (bool)$b => 'Condition met',
    default => null,
};

$result2 = match (true) {
    !$b => 'Condition met',
    default => null,
};

$result3 = match (true) {
    !$b && $c => 'Condition met',
    default => null,
};

$result4 = match (true) {
    $c && !$b => 'Condition met',
    default => null,
};

$result5 = match (true) {
    !$c && $b => 'Condition met',
    default => null,
};

$result6 = match (true) {
    $a !== null => 'Condition met',
    default => null,
};

$result7 = match (true) {
    $b && $a !== null => 'Condition met',
    default => null,
};

// do not report this
$postTmp = $_POST ?? [];
$getTmp = $_GET ?? [];
$result = match (true) {
    isset($_REQUEST) && ($getTmp + $postTmp) != $_REQUEST => 'Condition met',
    default => null,
};

/** @var numeric $a */
$a = '1';
$result1 = match (true) {
    (bool)$a && '0' => 'Condition met',
    default => null,
};

$result2 = match (true) {
    (bool)$a && '' => 'Condition met',
    default => null,
};

$result3 = match (true) {
    (bool)$a && false => 'Condition met',
    default => null,
};

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
function hasPermissionsNatch($permission): bool
{
    return match ($permission) {
        FooConst::USER_PERMISSION_NONE => false,
        default => true,
    };
}

// check assignment in condition
$result = match (true) {
    1 === 1 && $b = rand(0, 1) => 'Condition met',
    default => null,
};

while (1 === 1 && $a = rand(0, 1)) {
    // ...
}

// check yoda - ok
$foo = 'generated';
$result = match (true) {
    !is_dir("/tmp/" . $foo) && !mkdir("/tmp/" . $foo) && !is_dir("/tmp/" . $foo) => 'Condition met',
    default => null,
};

// check yoda - error
$i = 5;
$result = match (true) {
    4 > $i => 'Condition met',
    default => null,
};

$storage_id = match (true) {
    rand(0, 10) > 5 => "NULL",
    default => rand(0, 10) > 5 ? rand() : null,
};

$result = match ($storage_id) {
    "NULL" => 'Condition met',
    default => null,
};

/** @var array<array-key,mixed> $customer */
$customer = [];
$result = match (true) {
    $customer == false => 'Condition met',
    default => null,
};

/** @var array<array-key,mixed> $customer */
$customer = [];
$result = match (true) {
    $customer === true => 'Condition met',
    default => 'Condition not met',
};

/** @var array<array-key,mixed> $customer */
$customer = [];
$result = match (true) {
    $customer == false => 'Condition met',
    default => null,
};

/** @phpstan-var non-empty-list<1> $customer */
$customer = [1, 1, 1];
$result = match (true) {
    $customer == false => 'Condition met',
    default => null,
};
