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
 * `RuleTestCase::analyse()` only compares message, line and tip, so the error identifiers were
 * completely untested even though they are the stable API users write into `ignoreErrors` and into
 * their PHPStan baseline. Renaming one silently un-ignores errors in every downstream project.
 *
 * `IfConditionHelper::buildErrorMessage()` derives the identifier from the PhpParser node class
 * name with underscores stripped, so reserved-word node names like `If_`, `ElseIf_`, `Match_` and
 * `Switch_` produce `voku.If`, `voku.ElseIf`, `voku.Match` and `voku.Switch`. That transformation is
 * pinned here.
 *
 * @extends RuleTestCase<Rule>
 */
final class RuleErrorIdentifierTest extends RuleTestCase
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
     * @return array<string, array{0: string, 1: array<int, string>, 2: array<int, string>}>
     */
    public function provideRuleIdentifiers(): array
    {
        $fixtures = __DIR__ . '/fixtures/';

        return [
            'IfConditionRule' => [
                'IfConditionRule',
                [$fixtures . 'IfConditionsFixtures.php'],
                [
                    'voku.BooleanAnd',
                    'voku.Concat',
                    'voku.Equal',
                    'voku.Greater',
                    'voku.GreaterOrEqual',
                    'voku.Identical',
                    'voku.NotEqual',
                    'voku.Smaller',
                    'voku.SmallerOrEqual',
                ],
            ],
            'IfConditionBasicRule' => [
                'IfConditionBasicRule',
                [$fixtures . 'IfConditionsBasicFixtures.php'],
                ['voku.If'],
            ],
            'ElseIfConditionBasicRule' => [
                'ElseIfConditionBasicRule',
                [$fixtures . 'ElseIfConditionsBasicFixtures.php'],
                ['voku.ElseIf'],
            ],
            'IfConditionBooleanAndRule' => [
                'IfConditionBooleanAndRule',
                [$fixtures . 'IfConditionsBooleanAndFixtures.php'],
                ['voku.BooleanAndNode'],
            ],
            'IfConditionBooleanOrRule' => [
                'IfConditionBooleanOrRule',
                [$fixtures . 'IfConditionsBooleanOrFixtures.php'],
                ['voku.BooleanOrNode'],
            ],
            'IfConditionBooleanNotRule' => [
                'IfConditionBooleanNotRule',
                [$fixtures . 'IfConditionsBooleanNotFixtures.php'],
                ['voku.BooleanNot'],
            ],
            'IfConditionTernaryOperatorRule' => [
                'IfConditionTernaryOperatorRule',
                [$fixtures . 'IfConditionsTernaryOperatorRuleFixtures.php'],
                ['voku.Ternary'],
            ],
            'IfConditionMatchRule' => [
                'IfConditionMatchRule',
                [$fixtures . 'IfConditionMatchRuleFixtures.php'],
                ['voku.Match'],
            ],
            'IfConditionSwitchCaseRule' => [
                'IfConditionSwitchCaseRule',
                [$fixtures . 'IfConditionSwitchCaseRuleFixtures.php'],
                ['voku.Switch'],
            ],
            'ExtendedBinaryOpRule' => [
                'ExtendedBinaryOpRule',
                [$fixtures . 'ExtendedBinaryOpRuleFixtures.php'],
                ['voku.Equal', 'voku.Mul', 'voku.Plus'],
            ],
            'ExtendedAssignOpRule' => [
                'ExtendedAssignOpRule',
                [$fixtures . 'ExtendedAssignOpRuleFixtures.php'],
                ['voku.Concat', 'voku.Plus'],
            ],
            'WrongCastRule' => [
                'WrongCastRule',
                [$fixtures . 'WrongCastBaseClass.php', $fixtures . 'WrongCastCheckedClass.php'],
                ['voku.wrongCast'],
            ],
            'DisallowedCallMethodOnNullRule' => [
                'DisallowedCallMethodOnNullRule',
                [$fixtures . 'DisallowedCallMethodOnNullFixtures.php'],
                ['voku.callMethodOnNull'],
            ],
        ];
    }

    /**
     * @dataProvider provideRuleIdentifiers
     *
     * @param array<int, string> $files
     * @param array<int, string> $expectedIdentifiers
     */
    public function testErrorIdentifiers(string $ruleName, array $files, array $expectedIdentifiers): void
    {
        $this->ruleUnderTest = $this->createRule($ruleName);

        $errors = $this->gatherAnalyserErrors($files);
        static::assertNotSame([], $errors, $ruleName . ' produced no errors at all, the fixture no longer covers it.');

        $identifiers = [];
        foreach ($errors as $error) {
            $identifier = $error->getIdentifier();

            static::assertNotNull(
                $identifier,
                'PHPStan requires an identifier for every error: ' . $error->getMessage()
            );
            static::assertMatchesRegularExpression(
                '/^voku\.[A-Za-z]+$/',
                $identifier,
                'Identifiers have to stay in the "voku." namespace and must not contain an underscore.'
            );

            $identifiers[$identifier] = true;
        }

        $identifiers = \array_keys($identifiers);
        \sort($identifiers);

        static::assertSame($expectedIdentifiers, $identifiers);
    }

    private function createRule(string $ruleName): Rule
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
