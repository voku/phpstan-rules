<?php

declare(strict_types=1);

namespace voku\PHPStan\Rules\Test\fixtures\TraitContext;

final class IntAssignTraitConsumer
{
    use AssignOpTrait;

    /** @var int */
    private $value = 1;
}
