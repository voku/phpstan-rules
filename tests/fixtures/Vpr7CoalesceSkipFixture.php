<?php

declare(strict_types=1);

namespace voku\PHPStan\Rules\Test\fixtures;

final class Vpr7CoalesceSkipFixture
{
    public function nullableObject(?\stdClass $value): \stdClass
    {
        return $value ?? new \stdClass();
    }

    /**
     * @param array{value?: \stdClass} $values
     */
    public function optionalArrayOffset(array $values): \stdClass
    {
        return $values['value'] ?? new \stdClass();
    }
}
