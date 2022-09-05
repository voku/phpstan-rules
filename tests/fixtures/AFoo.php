<?php

namespace voku\PHPStan\Rules\Test\fixtures;

class AFoo
{
    public function __construct(
        private ?ACoalesceInterface $var,
        private ?ACoalesceInterface $var2,
    ) 
    {
        $this->var ??= new ACoalesceClass();
    }

    private function myMethod()
    {
        $this->var2 ??= new ACoalesceChild();
    }
}
