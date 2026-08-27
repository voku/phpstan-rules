<?php

declare(strict_types=1);

namespace voku\PHPStan\Rules\Test\fixtures;

/**
 * The reason this package exists: comparisons whose result changed between PHP 7 and PHP 8, and the
 * "double negative" comparisons the README asks people to write as a plain truthiness check.
 *
 * Each case is one method, so a failure names the exact rule that regressed instead of pointing at
 * a line number inside a 500-line fixture.
 */
final class Php8ComparisonSemanticsFixtures
{
    /**
     * PHP 7: `0 == ''` is true. PHP 8: false.
     *
     * @see https://3v4l.org/lBFHI
     */
    public function intComparedToEmptyString(int $value): bool
    {
        return $value == '';
    }

    /**
     * PHP 7: `0 != ''` is false. PHP 8: true.
     */
    public function intNotComparedToEmptyString(int $value): bool
    {
        return $value != '';
    }

    /**
     * PHP 7: `0 == 'foo'` is true. PHP 8: false.
     *
     * @see https://3v4l.org/BJ6b8
     */
    public function intComparedToNonNumericString(int $value): bool
    {
        return $value == 'foo';
    }

    /**
     * PHP 7: `0 == '0foo'` is true. PHP 8: false.
     */
    public function intComparedToLeadingNumericString(int $value): bool
    {
        return $value == '0foo';
    }

    public function floatComparedToEmptyString(float $value): bool
    {
        return $value == '';
    }

    /**
     * `(string) $foo != ''` is the same as `(string) $foo`.
     */
    public function doubleNegativeString(string $value): bool
    {
        return $value != '';
    }

    /**
     * `(int) $foo != 0` is the same as `(int) $foo`.
     *
     * @see https://3v4l.org/OWhrc
     */
    public function doubleNegativeInteger(int $value): bool
    {
        return $value != 0;
    }

    /**
     * `(bool) $foo != false` is the same as `(bool) $foo`.
     *
     * @see https://3v4l.org/SHoQP
     */
    public function doubleNegativeBoolean(bool $value): bool
    {
        return $value != false;
    }

    /**
     * NULL checks are surprising with a loose operator, "!==" is the honest form.
     *
     * @see https://3v4l.org/a4VdC
     */
    public function doubleNegativeNull(int $value): bool
    {
        return $value != null;
    }
}
