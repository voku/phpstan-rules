<?php

declare(strict_types=1);

namespace voku\PHPStan\Rules\Test\fixtures\TraitContext;

final class IntTraitConsumerOne
{
    use LooseComparisonTrait;

    /** @var int */
    private $value = 1;
}
