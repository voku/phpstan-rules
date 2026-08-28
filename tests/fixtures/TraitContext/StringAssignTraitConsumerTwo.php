<?php

declare(strict_types=1);

namespace voku\PHPStan\Rules\Test\fixtures\TraitContext;

final class StringAssignTraitConsumerTwo
{
    use AssignOpTrait;

    /** @var string */
    private $value = 'other';
}
