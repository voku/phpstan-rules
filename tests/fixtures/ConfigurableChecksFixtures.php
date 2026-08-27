<?php

declare(strict_types=1);

namespace voku\PHPStan\Rules\Test\fixtures;

use ArrayObject;
use stdClass;

/**
 * One construct per optional check, isolated so that a "the flag is off" assertion can be an empty
 * error list instead of a filtered one.
 */
final class ConfigurableChecksFixtures
{
    public function assignmentInsideCondition(): bool
    {
        if (($value = \random_int(0, 10)) > 5) {
            return true;
        }

        return $value === 0;
    }

    public function yodaCondition(int $value): bool
    {
        if (4 > $value) {
            return true;
        }

        return false;
    }

    public function objectInCondition(stdClass $object): bool
    {
        if ($object) {
            return true;
        }

        return false;
    }

    /**
     * @param ArrayObject<int, string> $object
     */
    public function subclassInCondition(ArrayObject $object): bool
    {
        if ($object) {
            return true;
        }

        return false;
    }
}
