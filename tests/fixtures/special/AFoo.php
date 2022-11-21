<?php

namespace voku\PHPStan\Rules\Test\fixtures\special;

class AFoo
{
    public function __construct(
        private ?ACoalesceInterface $var
    ) 
    {
        /* @phpstan-ignore-next-line -  Coalesce: Do not compare objects directly */
        $this->var ??= new ACoalesceClass();
    }
    
    public function myMethod(): void
    {
        /* @phpstan-ignore-next-line -  Coalesce: Do not compare objects directly */
        $this->var ??= new ACoalesceClassChild();
    }
}
