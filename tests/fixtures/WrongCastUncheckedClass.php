<?php

namespace voku\PHPStan\Rules\Test\fixtures;

// This class does NOT extend WrongCastBaseClass, so string-to-int casts are OK
class WrongCastUncheckedClass
{
    public function castStringToInt(string $str): int
    {
        return (int) $str;
    }
}
