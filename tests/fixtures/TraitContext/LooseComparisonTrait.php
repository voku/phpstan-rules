<?php

declare(strict_types=1);

namespace voku\PHPStan\Rules\Test\fixtures\TraitContext;

trait LooseComparisonTrait
{
    public function hasValue(): bool
    {
        return $this->value != '';
    }
}
