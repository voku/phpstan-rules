<?php

namespace voku\PHPStan\Rules\Test\fixtures;

// This class DOES extend WrongCastBaseClass through inheritance chain
class WrongCastSubCheckedClass extends WrongCastCheckedClass
{
    public function castInSubClass(string $s): int
    {
        return (int) $s;
    }
}
