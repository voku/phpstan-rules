<?php

namespace voku\PHPStan\Rules\Test\fixtures\special;

class AFoo
{
    public function __construct(
        private ?ACoalesceInterface $var
    ) 
    {
        $this->var ??= new ACoalesceClass();
    }

    public function myMethod(): void
    {
        $this->var ??= new ACoalesceClassChild();
    }
}
