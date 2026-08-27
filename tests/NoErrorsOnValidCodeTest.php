<?php

declare(strict_types=1);

namespace voku\PHPStan\Rules\Test;

use PHPStan\Rules\Rule;
use PHPStan\Testing\RuleTestCase;
use voku\PHPStan\Rules\DisallowedCallMethodOnNullRule;
use voku\PHPStan\Rules\ElseIfConditionBasicRule;
use voku\PHPStan\Rules\ExtendedAssignOpRule;
use voku\PHPStan\Rules\ExtendedBinaryOpRule;
use voku\PHPStan\Rules\IfConditionBasicRule;
use voku\PHPStan\Rules\IfConditionBooleanAndRule;
use voku\PHPStan\Rules\IfConditionBooleanNotRule;
use voku\PHPStan\Rules\IfConditionBooleanOrRule;
use voku\PHPStan\Rules\IfConditionMatchRule;
use voku\PHPStan\Rules\IfConditionRule;
use voku\PHPStan\Rules\IfConditionSwitchCaseRule;
use voku\PHPStan\Rules\IfConditionTernaryOperatorRule;
use voku\PHPStan\Rules\Test\fixtures\WrongCastBaseClass;
use voku\PHPStan\Rules\WrongCastRule;

/**
 * Before this test the suite only ever asserted that the rules *do* report something. A change that
 * widened a condition in `IfConditionHelper` could therefore start flagging perfectly valid code
 * without a single test turning red - which is the regression that actually hurts a static-analysis
 * extension, because users cannot work around a false positive.
 *
 * Every rule is run against one fixture of idiomatic code, with the strictest configuration this
 * package offers, and has to stay silent.
 *
 * @extends RuleTestCase<Rule>
 */
final class NoErrorsOnValidCodeTest extends RuleTestCase
{
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

    /**
     * @return array<string, array{0: string}>
     */
    public function provideRuleNames(): array
    {
        $ruleNames = [
            'DisallowedCallMethodOnNullRule',
            'ElseIfConditionBasicRule',
            'ExtendedAssignOpRule',
            'ExtendedBinaryOpRule',
            'IfConditionBasicRule',
            'IfConditionBooleanAndRule',
            'IfConditionBooleanNotRule',
            'IfConditionBooleanOrRule',
            'IfConditionMatchRule',
            'IfConditionRule',
            'IfConditionSwitchCaseRule',
            'IfConditionTernaryOperatorRule',
            'WrongCastRule',
        ];

        $data = [];
        foreach ($ruleNames as $ruleName) {
            $data[$ruleName] = [$ruleName];
        }

        return $data;
    }

    /**
     * @dataProvider provideRuleNames
     */
    public function testValidCodeProducesNoErrors(string $ruleName): void
    {
        $this->ruleUnderTest = $this->createStrictlyConfiguredRule($ruleName);

        $this->analyse(
            [
                __DIR__ . '/fixtures/ValidCodeFixtures.php',
                __DIR__ . '/fixtures/MyId.php',
            ],
            []
        );
    }

    /**
     * @dataProvider provideRuleNames
     *
     * @requires PHP 8.0
     */
    public function testValidPhp80CodeProducesNoErrors(string $ruleName): void
    {
        $this->ruleUnderTest = $this->createStrictlyConfiguredRule($ruleName);

        $this->analyse(
            [
                __DIR__ . '/fixtures/ValidCodeFixtures80.php',
            ],
            []
        );
    }

    /**
     * Every knob this package exposes is turned on, so the fixture also proves that the optional
     * checks do not fire on code that follows the recommendation.
     */
    private function createStrictlyConfiguredRule(string $ruleName): Rule
    {
        $reflectionProvider = $this->createReflectionProvider();
        $classesNotInIfConditions = [\stdClass::class];

        switch ($ruleName) {
            case 'IfConditionRule':
                return new IfConditionRule($classesNotInIfConditions, $reflectionProvider, true, true);
            case 'IfConditionBasicRule':
                return new IfConditionBasicRule($classesNotInIfConditions, $reflectionProvider, true, true);
            case 'ElseIfConditionBasicRule':
                return new ElseIfConditionBasicRule($classesNotInIfConditions, $reflectionProvider, true, true);
            case 'IfConditionBooleanAndRule':
                return new IfConditionBooleanAndRule($classesNotInIfConditions, $reflectionProvider, true, true);
            case 'IfConditionBooleanOrRule':
                return new IfConditionBooleanOrRule($classesNotInIfConditions, $reflectionProvider, true, true);
            case 'IfConditionBooleanNotRule':
                return new IfConditionBooleanNotRule($classesNotInIfConditions, $reflectionProvider, true, true);
            case 'IfConditionTernaryOperatorRule':
                return new IfConditionTernaryOperatorRule($classesNotInIfConditions, $reflectionProvider, true);
            case 'IfConditionMatchRule':
                return new IfConditionMatchRule($classesNotInIfConditions, true, $reflectionProvider);
            case 'IfConditionSwitchCaseRule':
                return new IfConditionSwitchCaseRule($classesNotInIfConditions, true, $reflectionProvider);
            case 'ExtendedBinaryOpRule':
                return new ExtendedBinaryOpRule();
            case 'ExtendedAssignOpRule':
                return new ExtendedAssignOpRule($reflectionProvider, true, true);
            case 'WrongCastRule':
                return new WrongCastRule([WrongCastBaseClass::class]);
            case 'DisallowedCallMethodOnNullRule':
                return new DisallowedCallMethodOnNullRule($reflectionProvider);
        }

        throw new \LogicException('Unknown rule: ' . $ruleName);
    }
}
