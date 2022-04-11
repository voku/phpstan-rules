<?php

declare(strict_types=1);

namespace voku\PHPStan\Rules;

use PhpParser\Node;
use PHPStan\Analyser\Scope;
use PHPStan\Rules\Rule;

/**
 * @implements Rule<\PhpParser\Node\Expr\Ternary>
 */
final class IfConditionTernaryOperatorRule implements Rule
{

    /**
     * @var array<int, class-string>
     */
    private $classesNotInIfConditions;

    /**
     * @param array<int, class-string> $classesNotInIfConditions
     */
    public function __construct(array $classesNotInIfConditions)
    {
        $this->classesNotInIfConditions = $classesNotInIfConditions;
    }

    public function getNodeType(): string
    {
        return \PhpParser\Node\Expr\Ternary::class;
    }

    /**
     * @param \PhpParser\Node\Expr\Ternary $node
     *
     * @return array<int, \PHPStan\Rules\RuleError>
     */
    public function processNode(Node $node, Scope $scope): array
    {
        if ($node->if === null) {
            return []; // elvis ?:
        }

        $cond = $node->cond;

        return IfConditionHelper::processNode($cond, $scope, $this->classesNotInIfConditions);
    }
}
