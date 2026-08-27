<?php

declare(strict_types=1);

namespace voku\PHPStan\Rules\Test;

use PHPStan\Rules\Rule;
use PHPStan\Testing\RuleTestCase;
use voku\PHPStan\Rules\ExtendedAssignOpRule;
use voku\PHPStan\Rules\ExtendedBinaryOpRule;

/**
 * VPR-2 regression coverage for the array/non-array branch shared by both extended operator rules.
 * Definite non-arrays are reported; arrays, including non-empty-array accessory types, stay valid.
 *
 * @extends RuleTestCase<Rule>
 */
final class ExtendedOpRuleArrayCheckTest extends RuleTestCase
{
    private const FIXTURE = __DIR__ . '/fixtures/ArrayInCombinationWithNonArrayFixtures.php';

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

    public function testExtendedBinaryOpRuleReportsDefiniteNonArrayOperands(): void
    {
        $this->ruleUnderTest = new ExtendedBinaryOpRule();

        $this->analyse(
            [self::FIXTURE],
            [
                [
                    'Equal: array (array<int, string>) in combination with non-array (int) is not allowed.',
                    18,
                ],
                [
                    'Equal: array (array<int, string>) in combination with non-array (string) is not allowed.',
                    26,
                ],
            ]
        );
    }

    public function testExtendedAssignOpRuleReportsDefiniteNonArrayOperands(): void
    {
        $this->ruleUnderTest = new ExtendedAssignOpRule($this->createReflectionProvider());

        $this->analyse(
            [self::FIXTURE],
            [
                [
                    'Plus: array (array<int, string>) in combination with non-array (int) is not allowed.',
                    45,
                ],
            ]
        );
    }
}
