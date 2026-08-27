<?php

declare(strict_types=1);

namespace voku\PHPStan\Rules\Test;

use PhpParser\Node;
use PHPStan\Analyser\Scope;
use PHPStan\Rules\Rule;
use PHPStan\Testing\RuleTestCase;
use PHPStan\Type\VerbosityLevel;
use voku\PHPStan\Rules\ExtendedAssignOpRule;
use voku\PHPStan\Rules\ExtendedBinaryOpRule;

/**
 * ExtendedBinaryOpRule and ExtendedAssignOpRule both contain a branch that is supposed to report
 * "array (...) in combination with non-array (...) is not allowed." - the array counterpart of the
 * string check next to it. No test in this repository ever asserted that message, and it turns out
 * the branch cannot fire.
 *
 * Its last condition is:
 *
 *     \strpos($type_2->describe(VerbosityLevel::typeOnly()), 'non-empty-array') !== false
 *
 * `VerbosityLevel::typeOnly()` strips accessory types, so a `non-empty-array<int, string>` is
 * described as `array<int, string>` and the needle is never present. The comparison looks like the
 * `!== 'string'` hack directly above it, where `typeOnly()` *does* produce the needle, so the
 * inversion is easy to miss by reading.
 *
 * These tests pin both halves of that: the observable behaviour today (no error is reported) and
 * the type-description fact the branch depends on. Fixing the branch - tracked as VPR-2 on the
 * board - has to make the first test fail, which is the point of writing it down.
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

    /**
     * Known defect, see VPR-2: mixing an array with an int or with another array reports nothing.
     * The only message the fixture produces comes from the *string* branch, for `array == string`,
     * and it describes the string operand rather than the array one.
     */
    public function testExtendedBinaryOpRuleDoesNotReportTheArrayCombinationYet(): void
    {
        $this->ruleUnderTest = new ExtendedBinaryOpRule();

        $messages = $this->gatherMessages([self::FIXTURE]);

        static::assertSame(
            [
                '29: Equal: string (string) in combination with non-string (array<int, string>) is not allowed.',
            ],
            $messages
        );
        self::assertNoArrayCombinationMessage($messages);
    }

    /**
     * Known defect, see VPR-2.
     */
    public function testExtendedAssignOpRuleDoesNotReportTheArrayCombinationYet(): void
    {
        $this->ruleUnderTest = new ExtendedAssignOpRule($this->createReflectionProvider());

        $messages = $this->gatherMessages([self::FIXTURE]);

        static::assertSame([], $messages);
        self::assertNoArrayCombinationMessage($messages);
    }

    /**
     * @param array<int, string> $files
     *
     * @return array<int, string>
     */
    private function gatherMessages(array $files): array
    {
        $messages = [];
        foreach ($this->gatherAnalyserErrors($files) as $error) {
            $messages[] = $error->getLine() . ': ' . $error->getMessage();
        }

        \sort($messages);

        return $messages;
    }

    /**
     * @param array<int, string> $messages
     */
    private static function assertNoArrayCombinationMessage(array $messages): void
    {
        foreach ($messages as $message) {
            static::assertStringNotContainsString('in combination with non-array', $message);
        }
    }

    /**
     * The reason the branch is unreachable, asserted directly against PHPStan instead of against
     * prose: `typeOnly()` never yields the "non-empty-array" needle the branch looks for, while
     * `value()` does. If a future PHPStan release changes that, this test turns red and the dead
     * branch suddenly becomes live - which is something the maintainer wants to hear about.
     */
    public function testTypeOnlyVerbosityNeverContainsTheNonEmptyArrayNeedle(): void
    {
        $descriptions = [];

        $this->ruleUnderTest = new class($descriptions) implements Rule {
            /**
             * @var array<int, array{typeOnly: string, value: string}>
             */
            private $descriptions;

            /**
             * @param array<int, array{typeOnly: string, value: string}> $descriptions
             */
            public function __construct(array &$descriptions)
            {
                $this->descriptions = &$descriptions;
            }

            public function getNodeType(): string
            {
                return Node\Expr\BinaryOp::class;
            }

            /**
             * @param Node\Expr\BinaryOp $node
             *
             * @return array<int, \PHPStan\Rules\RuleError>
             */
            public function processNode(Node $node, Scope $scope): array
            {
                foreach ([$node->left, $node->right] as $side) {
                    $type = $scope->getType($side);
                    if (!$type->isArray()->yes()) {
                        continue;
                    }

                    $this->descriptions[] = [
                        'typeOnly' => $type->describe(VerbosityLevel::typeOnly()),
                        'value' => $type->describe(VerbosityLevel::value()),
                    ];
                }

                return [];
            }
        };

        $this->gatherAnalyserErrors([self::FIXTURE]);

        static::assertNotSame([], $descriptions, 'The fixture no longer contains an array operand.');

        $sawNonEmptyArrayAtValueVerbosity = false;
        foreach ($descriptions as $description) {
            static::assertStringNotContainsString(
                'non-empty-array',
                $description['typeOnly'],
                'typeOnly() started to expose accessory array types; the dead branch in the Extended*OpRule classes may have become reachable.'
            );

            if (\strpos($description['value'], 'non-empty-array') !== false) {
                $sawNonEmptyArrayAtValueVerbosity = true;
            }
        }

        static::assertTrue(
            $sawNonEmptyArrayAtValueVerbosity,
            'The fixture no longer contains a non-empty-array operand, so this test proves nothing.'
        );
    }
}
