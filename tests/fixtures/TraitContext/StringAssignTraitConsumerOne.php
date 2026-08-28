<?php

declare(strict_types=1);

namespace voku\PHPStan\Rules\Test\fixtures\TraitContext;

final class StringAssignTraitConsumerOne
{
    use AssignOpTrait;

    /** @var string */
    private $value = 'value';
}
