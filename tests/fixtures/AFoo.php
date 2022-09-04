<?php

namespace voku\PHPStan\Rules\Test\fixtures;

class AFoo
{
    public function __construct(
        private ?ACoalesceInterface $var
    ) 
    {
        $this->var ??= new ACoalesceClass();
    }
}
