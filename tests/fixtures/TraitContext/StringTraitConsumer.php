<?php

declare(strict_types=1);

namespace voku\PHPStan\Rules\Test\fixtures\TraitContext;

final class StringTraitConsumer
{
    use LooseComparisonTrait;

    /** @var string */
    private $value = 'value';
}
