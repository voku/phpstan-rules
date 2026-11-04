<?php

namespace voku\PHPStan\Rules\Test\fixtures\special;

class AFoo
{
    /** @var ACoalesceInterface|null */
    private $var;

    public function __construct(
        ?ACoalesceInterface $var
    )
    {
        $this->var = $var;
        /* @phpstan-ignore-next-line -  Coalesce: Do not compare objects directly */
        $this->var ??= new ACoalesceClass();
    }
    
    public function myMethod(): void
    {
        /* @phpstan-ignore-next-line -  Coalesce: Do not compare objects directly */
        $this->var ??= new ACoalesceClassChild();
    }
}
