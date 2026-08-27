<?php

declare(strict_types=1);

namespace voku\PHPStan\Rules\Test;

use PHPStan\Rules\Rule;
use PHPStan\Testing\RuleTestCase;
use voku\PHPStan\Rules\IfConditionRule;

/**
 * Regression coverage for VPR-7 / issue #18.
 *
 * Coalesce is not a comparison or truthiness test. A class configured through
 * classesNotInIfConditions must therefore remain usable on the left side of ??, including an
 * optional array offset. Removing the Coalesce guards in IfConditionHelper would reintroduce the
 * false positive reported in #18.
 *
 * @extends RuleTestCase<IfConditionRule>
 */
final class Vpr7CoalesceSkipTest extends RuleTestCase
{
    protected function getRule(): Rule
    {
        return new IfConditionRule([\stdClass::class], $this->createReflectionProvider(), false, false);
    }

    public function testConfiguredObjectsRemainValidWithNullCoalesce(): void
    {
        $this->analyse(
            [__DIR__ . '/fixtures/Vpr7CoalesceSkipFixture.php'],
            []
        );
    }
}
