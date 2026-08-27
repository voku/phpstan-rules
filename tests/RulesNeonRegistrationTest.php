<?php

declare(strict_types=1);

namespace voku\PHPStan\Rules\Test;

use PHPStan\Rules\Rule;
use PHPStan\Testing\PHPStanTestCase;
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
use voku\PHPStan\Rules\InArrayLooseComparisonRule;
use voku\PHPStan\Rules\WrongCastRule;

/**
 * `rules.neon` is the only thing a consumer of this package actually loads, and nothing else in the
 * test-suite touches it. A renamed constructor parameter, a class that was added to `src/` but
 * forgotten in the neon file, or a silently changed default therefore used to ship green.
 *
 * These tests build a real PHPStan container from `rules.neon`, which forces every declared service
 * to be compiled and instantiated with exactly the arguments the neon file passes.
 */
final class RulesNeonRegistrationTest extends PHPStanTestCase
{
    private const PHPSTAN_RULE_TAG = 'phpstan.rules.rule';

    /**
     * The rules a consumer gets by only including `rules.neon`.
     *
     * @var array<int, class-string<Rule>>
     */
    private const SHIPPED_RULES = [
        ElseIfConditionBasicRule::class,
        ExtendedAssignOpRule::class,
        ExtendedBinaryOpRule::class,
        IfConditionBasicRule::class,
        IfConditionBooleanAndRule::class,
        IfConditionBooleanNotRule::class,
        IfConditionBooleanOrRule::class,
        IfConditionMatchRule::class,
        IfConditionRule::class,
        IfConditionSwitchCaseRule::class,
        IfConditionTernaryOperatorRule::class,
        WrongCastRule::class,
    ];

    /**
     * Rules that exist in `src/` but are deliberately not part of `rules.neon`, because the README
     * documents them as something the user has to opt into.
     *
     * @var array<int, class-string<Rule>>
     */
    private const OPT_IN_RULES = [
        DisallowedCallMethodOnNullRule::class,
        InArrayLooseComparisonRule::class,
    ];

    /**
     * @return array<int, string>
     */
    public static function getAdditionalConfigFiles(): array
    {
        return [
            \dirname(__DIR__) . '/rules.neon',
        ];
    }

    public function testRulesNeonRegistersExactlyTheShippedRules(): void
    {
        $expected = self::SHIPPED_RULES;
        \sort($expected);

        $actual = $this->getRegisteredVokuRuleClasses();

        static::assertSame($expected, $actual);
    }

    public function testEveryRuleClassInSrcIsEitherShippedOrExplicitlyOptIn(): void
    {
        $known = \array_merge(self::SHIPPED_RULES, self::OPT_IN_RULES);
        \sort($known);

        $actual = self::findRuleClassesInSrc();

        static::assertSame(
            $known,
            $actual,
            'A rule class was added to or removed from src/ without updating rules.neon and this test.'
        );
    }

    public function testOptInRulesAreNotRegisteredByRulesNeon(): void
    {
        $registered = $this->getRegisteredVokuRuleClasses();

        foreach (self::OPT_IN_RULES as $optInRule) {
            static::assertNotContains(
                $optInRule,
                $registered,
                $optInRule . ' is documented as opt-in but rules.neon registers it.'
            );
        }
    }

    /**
     * The defaults are part of the public contract: switching one of them to `true` would start
     * reporting new errors in every project that just includes `rules.neon`.
     */
    public function testDefaultParameterValues(): void
    {
        /** @var array<string, mixed> $parameters */
        $parameters = self::getContainer()->getParameter('voku');

        static::assertSame(
            [
                'checkForAssignments' => false,
                'checkYodaConditions' => false,
                'classesForCheckStringToIntCast' => [],
                'classesNotInIfConditions' => [],
            ],
            self::sortByKey($parameters)
        );
    }

    /**
     * Every rule registered by `rules.neon` has to be tagged so that PHPStan actually runs it.
     * A service without the tag is dead configuration that no test would otherwise notice.
     */
    public function testEveryRegisteredRuleIsAnInstanceOfPhpStanRule(): void
    {
        $services = self::getContainer()->getServicesByTag(self::PHPSTAN_RULE_TAG);

        $vokuServices = \array_filter(
            $services,
            static function ($service): bool {
                return \strpos(\get_class($service), 'voku\\PHPStan\\Rules\\') === 0;
            }
        );

        static::assertCount(\count(self::SHIPPED_RULES), $vokuServices);

        foreach ($vokuServices as $service) {
            static::assertInstanceOf(Rule::class, $service);
            static::assertNotSame('', $service->getNodeType());
        }
    }

    /**
     * @return array<int, class-string<Rule>>
     */
    private function getRegisteredVokuRuleClasses(): array
    {
        $classNames = [];

        foreach (self::getContainer()->getServicesByTag(self::PHPSTAN_RULE_TAG) as $service) {
            $className = \get_class($service);
            if (\strpos($className, 'voku\\PHPStan\\Rules\\') !== 0) {
                continue;
            }

            $classNames[] = $className;
        }

        $classNames = \array_values(\array_unique($classNames));
        \sort($classNames);

        /** @var array<int, class-string<Rule>> $classNames */
        return $classNames;
    }

    /**
     * @return array<int, class-string<Rule>>
     */
    private static function findRuleClassesInSrc(): array
    {
        $directory = \dirname(__DIR__) . '/src/voku/PHPStan/Rules';
        $files = \glob($directory . '/*.php');
        static::assertIsArray($files);
        static::assertNotSame([], $files);

        $classNames = [];
        foreach ($files as $file) {
            $className = 'voku\\PHPStan\\Rules\\' . \basename($file, '.php');
            if (!\class_exists($className)) {
                continue;
            }

            if (!\in_array(Rule::class, (array) \class_implements($className), true)) {
                continue;
            }

            /** @var class-string<Rule> $className */
            $classNames[] = $className;
        }

        \sort($classNames);

        return $classNames;
    }

    /**
     * @param array<string, mixed> $values
     *
     * @return array<string, mixed>
     */
    private static function sortByKey(array $values): array
    {
        \ksort($values);

        return $values;
    }
}
