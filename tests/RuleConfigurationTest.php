<?php

declare(strict_types=1);

namespace voku\PHPStan\Rules\Test;

use PHPStan\Rules\Rule;
use PHPStan\Testing\RuleTestCase;
use voku\PHPStan\Rules\IfConditionBasicRule;
use voku\PHPStan\Rules\IfConditionRule;
use voku\PHPStan\Rules\Test\fixtures\WrongCastBaseClass;
use voku\PHPStan\Rules\WrongCastRule;

/**
 * `rules.neon` ships `checkForAssignments`, `checkYodaConditions`, `classesNotInIfConditions` and
 * `classesForCheckStringToIntCast` disabled/empty, but the whole suite only ever constructed the
 * rules with the optional checks turned *on*. Nothing proved that a project which does not opt in
 * stays quiet - which is the regression that would hit every user of the default configuration at
 * once.
 *
 * Each test asserts both directions on the same fixture: silent when the flag is off, exactly one
 * specific error when it is on.
 *
 * @extends RuleTestCase<Rule>
 */
final class RuleConfigurationTest extends RuleTestCase
{
    private const FIXTURE = __DIR__ . '/fixtures/ConfigurableChecksFixtures.php';

    /**
     * @var Rule|null
     */
    private $ruleUnderTest;

    protected function getRule(): Rule
    {
        if ($this->ruleUnderTest === null) {
            throw new \LogicException('The rule under test has to be selected by the test method.');
        }

        return $this->ruleUnderTest;
    }

    public function testAssignmentsAreNotReportedByDefault(): void
    {
        $this->ruleUnderTest = new IfConditionRule([], $this->createReflectionProvider(), false, false);

        $this->analyse([self::FIXTURE], []);
    }

    public function testAssignmentsAreReportedWhenCheckForAssignmentsIsEnabled(): void
    {
        $this->ruleUnderTest = new IfConditionRule([], $this->createReflectionProvider(), true, false);

        $this->analyse(
            [self::FIXTURE],
            [
                [
                    'Greater: Assignment is not allowed here.',
                    18,
                ],
            ]
        );
    }

    public function testYodaConditionsAreNotReportedByDefault(): void
    {
        $this->ruleUnderTest = new IfConditionRule([], $this->createReflectionProvider(), false, false);

        $this->analyse([self::FIXTURE], []);
    }

    public function testYodaConditionsAreReportedWhenCheckYodaConditionsIsEnabled(): void
    {
        $this->ruleUnderTest = new IfConditionRule([], $this->createReflectionProvider(), false, true);

        $this->analyse(
            [self::FIXTURE],
            [
                [
                    'Greater: Yoda condition is not allowed here.',
                    27,
                ],
            ]
        );
    }

    /**
     * Both optional checks are independent: enabling both has to report both, not one of them.
     */
    public function testBothOptionalChecksCanBeEnabledTogether(): void
    {
        $this->ruleUnderTest = new IfConditionRule([], $this->createReflectionProvider(), true, true);

        $this->analyse(
            [self::FIXTURE],
            [
                [
                    'Greater: Assignment is not allowed here.',
                    18,
                ],
                [
                    'Greater: Yoda condition is not allowed here.',
                    27,
                ],
            ]
        );
    }

    public function testClassesNotInIfConditionsIsSilentWhenEmpty(): void
    {
        $this->ruleUnderTest = new IfConditionBasicRule([], $this->createReflectionProvider(), false, false);

        $this->analyse([self::FIXTURE], []);
    }

    public function testClassesNotInIfConditionsReportsAConfiguredClass(): void
    {
        $this->ruleUnderTest = new IfConditionBasicRule([\stdClass::class], $this->createReflectionProvider(), false, false);

        $this->analyse(
            [self::FIXTURE],
            [
                [
                    'If_: Use a method to check the condition e.g. `$foo->value()` instead of `$foo`.',
                    36,
                ],
            ]
        );
    }

    /**
     * `is_a($class, $configured, true)` is used, so an implementation of a configured interface has
     * to be reported as well - otherwise configuring a base type would be useless.
     */
    public function testClassesNotInIfConditionsMatchesSubtypes(): void
    {
        $this->ruleUnderTest = new IfConditionBasicRule([\Traversable::class], $this->createReflectionProvider(), false, false);

        $this->analyse(
            [self::FIXTURE],
            [
                [
                    'If_: Use a method to check the condition e.g. `$foo->value()` instead of `$foo`.',
                    48,
                ],
            ]
        );
    }

    /**
     * A class that is unrelated to the configured one must stay silent.
     */
    public function testClassesNotInIfConditionsIgnoresUnrelatedClasses(): void
    {
        $this->ruleUnderTest = new IfConditionBasicRule([\DateTimeInterface::class], $this->createReflectionProvider(), false, false);

        $this->analyse([self::FIXTURE], []);
    }

    public function testClassesForCheckStringToIntCastIsSilentWhenEmpty(): void
    {
        $this->ruleUnderTest = new WrongCastRule([]);

        $this->analyse(
            [
                __DIR__ . '/fixtures/WrongCastBaseClass.php',
                __DIR__ . '/fixtures/WrongCastCheckedClass.php',
            ],
            []
        );
    }

    public function testClassesForCheckStringToIntCastReportsAConfiguredClass(): void
    {
        $this->ruleUnderTest = new WrongCastRule([WrongCastBaseClass::class]);

        $this->analyse(
            [
                __DIR__ . '/fixtures/WrongCastBaseClass.php',
                __DIR__ . '/fixtures/WrongCastCheckedClass.php',
            ],
            [
                [
                    "Casting to int something that's string.",
                    14,
                ],
                [
                    "Casting to int something that's string.",
                    19,
                ],
            ]
        );
    }
}
