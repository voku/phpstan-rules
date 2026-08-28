<?php

declare(strict_types=1);

namespace voku\PHPStan\Rules\Test\fixtures\TraitContext;

trait AssignOpTrait
{
    public function mutateValue(): void
    {
        $this->value += 1;
    }
}
