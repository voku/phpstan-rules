<?php

namespace voku\PHPStan\Rules\Test\fixtures;

// ---------------------------------------------------
// ERRORS: cast string to int inside a checked class
// ---------------------------------------------------

class WrongCastCheckedClass extends WrongCastBaseClass
{
    public function castLiteralToInt(): void
    {
        $value = 'hello';
        $result = (int) $value;
    }

    public function castParamToInt(string $str): int
    {
        return (int) $str;
    }
}

// ---------------------------------------------------
// OK: cast string to float (not covered by the rule)
// ---------------------------------------------------

class WrongCastCheckFloat extends WrongCastBaseClass
{
    public function castStringToFloat(string $str): float
    {
        return (float) $str;
    }
}

// ---------------------------------------------------
// OK: cast int to string inside a checked class
// ---------------------------------------------------

class WrongCastCheckIntToString extends WrongCastBaseClass
{
    public function castIntToString(int $n): string
    {
        return (string) $n;
    }
}

// ---------------------------------------------------
// OK: cast string to int outside any class
// ---------------------------------------------------

function wrongCastOutsideClass(string $str): int
{
    return (int) $str;
}
