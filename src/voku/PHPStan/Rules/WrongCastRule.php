<?php

declare(strict_types=1);

namespace voku\PHPStan\Rules;

use PhpParser\Node;
use PhpParser\Node\Expr\Cast;
use PHPStan\Analyser\Scope;
use PHPStan\Rules\Rule;
use PHPStan\Rules\RuleError;
use PHPStan\Rules\RuleErrorBuilder;
use PHPStan\Type\ErrorType;
use PHPStan\Type\GeneralizePrecision;
use PHPStan\Type\StringType;
use PHPStan\Type\TypeUtils;
use PHPStan\Type\VerbosityLevel;
use function sprintf;

/**
 * @implements Rule<Node\Expr\Cast>
 */
class WrongCastRule implements Rule
{
    /**
     * @var array<int, class-string>
     */
    private $classesForCheckStringToIntCast;

    /**
     * @param array<int, class-string> $classesForCheckStringToIntCast
     */
    public function __construct(array $classesForCheckStringToIntCast)
    {
        $this->classesForCheckStringToIntCast = $classesForCheckStringToIntCast;
    }

    public function getNodeType(): string
    {
        return Cast::class;
    }

    /**
     * @param Cast $node
     *
     * @return RuleError[]
     */
    public function processNode(Node $node, Scope $scope): array
    {
        $castType = $scope->getType($node);
        if ($castType instanceof ErrorType) {
            return [];
        }

        $castTypeGeneralize = $castType->generalize(GeneralizePrecision::lessSpecific());
        $expressionType = $scope->getType($node->expr);
        $expressionTypeGeneralize = $expressionType->generalize(GeneralizePrecision::lessSpecific());

        $tmpClass = $scope->getClassReflection();

        // -----------------------------
        // check string to (int)-cast
        // -----------------------------

        foreach ($this->classesForCheckStringToIntCast as $classForCheckStringToIntCast) {
            if (
                $expressionTypeGeneralize instanceof \PHPStan\Type\StringType
                &&
                $castTypeGeneralize instanceof \PHPStan\Type\IntegerType
                &&
                $tmpClass
                &&
                \is_a($tmpClass->getName(), $classForCheckStringToIntCast, true)
            ) {
                return [
                    RuleErrorBuilder::message(
                        sprintf(
                            'Casting to %s something that\'s %s.',
                            $castType->describe(VerbosityLevel::typeOnly()),
                            $expressionType->describe(VerbosityLevel::typeOnly())
                        )
                    )->build(),
                ];
            }
        }

        return [];
    }
}
