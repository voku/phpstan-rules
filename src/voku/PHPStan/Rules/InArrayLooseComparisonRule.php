<?php

declare(strict_types=1);

namespace voku\PHPStan\Rules;

use PhpParser\Node;
use PHPStan\Analyser\Scope;
use PHPStan\Rules\Rule;
use PHPStan\Type\Type;
use PHPStan\Type\VerbosityLevel;
use function sprintf;

/**
 * Reports the PHP 7 -> PHP 8 loose-comparison hazard for in_array()/array_search().
 *
 * The rule is deliberately opt-in and fail-open:
 * - only global built-ins are considered;
 * - constant haystacks stay with PHPStan's native array rules;
 * - unknown, union, mixed and unknown-strict cases stay silent;
 * - numeric-string comparisons stay silent because their loose result did not change in PHP 8.
 *
 * @implements Rule<\PhpParser\Node\Expr\FuncCall>
 */
final class InArrayLooseComparisonRule implements Rule
{
    /**
     * @var array<string, true>
     */
    private const FUNCTIONS = [
        'array_search' => true,
        'in_array' => true,
    ];

    public function getNodeType(): string
    {
        return \PhpParser\Node\Expr\FuncCall::class;
    }

    /**
     * @param \PhpParser\Node\Expr\FuncCall $node
     *
     * @return array<int, \PHPStan\Rules\RuleError>
     */
    public function processNode(Node $node, Scope $scope): array
    {
        if (!$node->name instanceof Node\Name) {
            return [];
        }

        if (!$node->name->isFullyQualified() && $scope->getNamespace() !== null) {
            return [];
        }

        $functionName = \strtolower($node->name->toString());
        if (!isset(self::FUNCTIONS[$functionName])) {
            return [];
        }

        $arguments = $this->resolveArguments($node->getArgs());
        if ($arguments === null) {
            return [];
        }

        if ($arguments['strict'] !== null && !$scope->getType($arguments['strict']->value)->isFalse()->yes()) {
            return [];
        }

        $needleType = $scope->getType($arguments['needle']->value);
        $haystackType = $scope->getType($arguments['haystack']->value);

        if (!$haystackType->isArray()->yes()) {
            return [];
        }

        if ($haystackType->getConstantArrays() !== []) {
            return [];
        }

        $valueType = $haystackType->getIterableValueType();
        if (!$this->isPhp8ComparisonHazard($needleType, $valueType)) {
            return [];
        }

        return [
            IfConditionHelper::buildErrorMessage(
                $node,
                sprintf(
                    'Possible insane comparison between %s and %s. Use strict mode e.g. `%s($needle, $haystack, true)`.',
                    $needleType->describe(VerbosityLevel::value()),
                    $valueType->describe(VerbosityLevel::value()),
                    $functionName
                ),
                $node->getStartLine()
            ),
        ];
    }

    /**
     * @param array<int, \PhpParser\Node\Arg> $args
     *
     * @return array{needle: \PhpParser\Node\Arg, haystack: \PhpParser\Node\Arg, strict: \PhpParser\Node\Arg|null}|null
     */
    private function resolveArguments(array $args): ?array
    {
        if (\count($args) < 2 || \count($args) > 3) {
            return null;
        }

        /** @var array{needle: \PhpParser\Node\Arg|null, haystack: \PhpParser\Node\Arg|null, strict: \PhpParser\Node\Arg|null} $resolved */
        $resolved = [
            'needle' => null,
            'haystack' => null,
            'strict' => null,
        ];
        $positionNames = ['needle', 'haystack', 'strict'];
        $position = 0;

        foreach ($args as $arg) {
            if ($arg->unpack) {
                return null;
            }

            if ($arg->name !== null) {
                $name = $arg->name->toString();
                if (!\array_key_exists($name, $resolved) || $resolved[$name] !== null) {
                    return null;
                }

                $resolved[$name] = $arg;

                continue;
            }

            while ($position < 3 && $resolved[$positionNames[$position]] !== null) {
                ++$position;
            }

            if ($position >= 3) {
                return null;
            }

            $resolved[$positionNames[$position]] = $arg;
            ++$position;
        }

        if (!$resolved['needle'] instanceof Node\Arg || !$resolved['haystack'] instanceof Node\Arg) {
            return null;
        }

        return [
            'needle' => $resolved['needle'],
            'haystack' => $resolved['haystack'],
            'strict' => $resolved['strict'],
        ];
    }

    private function isPhp8ComparisonHazard(Type $needleType, Type $valueType): bool
    {
        if ($this->isNonNumericString($needleType) && $this->isIntegerOrFloat($valueType)) {
            return true;
        }

        return $this->isIntegerOrFloat($needleType) && $this->isNonNumericString($valueType);
    }

    private function isIntegerOrFloat(Type $type): bool
    {
        return $type->isInteger()->yes() || $type->isFloat()->yes();
    }

    private function isNonNumericString(Type $type): bool
    {
        return $type->isString()->yes() && !$type->isNumericString()->yes();
    }
}
