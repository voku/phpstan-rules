<?php

declare(strict_types=1);

namespace voku\PHPStan\Rules\Test;

use PHPStan\Rules\Rule;
use PHPStan\Testing\RuleTestCase;
use voku\PHPStan\Rules\IfConditionMatchRule;
use voku\PHPStan\Rules\IfConditionSwitchCaseRule;
use voku\PHPStan\Rules\IfConditionTernaryOperatorRule;

/**
 * Regression coverage for VPR-8: checkYodaConditions has to reach ternary, match and switch rules
 * without reordering the legacy positional constructor arguments of Match/Switch.
 *
 * @extends RuleTestCase<Rule>
 */
final class Vpr8YodaConfigurationTest extends RuleTestCase
{
    private const TERNARY_FIXTURE = __DIR__ . '/fixtures/Vpr8TernaryFixture.php';

    private const MATCH_FIXTURE = __DIR__ . '/fixtures/Vpr8MatchFixture.php';

    private const SWITCH_FIXTURE = __DIR__ . '/fixtures/Vpr8SwitchFixture.php';

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

    public function testTernaryStaysSilentByDefault(): void
    {
        $this->ruleUnderTest = new IfConditionTernaryOperatorRule([], $this->createReflectionProvider());

        $this->analyse([self::TERNARY_FIXTURE], []);
    }

    public function testTernaryHonoursCheckYodaConditions(): void
    {
        $this->ruleUnderTest = new IfConditionTernaryOperatorRule([], $this->createReflectionProvider(), false, true);

        $this->analyse([self::TERNARY_FIXTURE], [['Ternary: Yoda condition is not allowed here.', 11]]);
    }

    /**
     * @requires PHP 8.0
     */
    public function testMatchStaysSilentByDefault(): void
    {
        $this->ruleUnderTest = new IfConditionMatchRule([], false, $this->createReflectionProvider());

        $this->analyse([self::MATCH_FIXTURE], []);
    }

    /**
     * @requires PHP 8.0
     */
    public function testMatchHonoursCheckYodaConditions(): void
    {
        $this->ruleUnderTest = new IfConditionMatchRule([], false, $this->createReflectionProvider(), true);

        $this->analyse([self::MATCH_FIXTURE], [['Match_: Yoda condition is not allowed here.', 12]]);
    }

    public function testSwitchStaysSilentByDefault(): void
    {
        $this->ruleUnderTest = new IfConditionSwitchCaseRule([], false, $this->createReflectionProvider());

        $this->analyse([self::SWITCH_FIXTURE], []);
    }

    public function testSwitchHonoursCheckYodaConditions(): void
    {
        $this->ruleUnderTest = new IfConditionSwitchCaseRule([], false, $this->createReflectionProvider(), true);

        $this->analyse([self::SWITCH_FIXTURE], [['Switch_: Yoda condition is not allowed here.', 12]]);
    }

    public function testLegacyConstructorPositionsAreNotReordered(): void
    {
        foreach ([IfConditionMatchRule::class, IfConditionSwitchCaseRule::class] as $ruleClass) {
            $constructor = (new \ReflectionClass($ruleClass))->getConstructor();
            static::assertNotNull($constructor);

            $names = \array_map(
                static function (\ReflectionParameter $parameter): string {
                    return $parameter->getName();
                },
                $constructor->getParameters()
            );

            static::assertSame(
                ['classesNotInIfConditions', 'checkForAssignments', 'reflectionProvider', 'checkYodaConditions'],
                $names
            );
        }
    }
}
