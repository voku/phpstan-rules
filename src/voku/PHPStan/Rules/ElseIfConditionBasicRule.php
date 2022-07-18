<?php

declare(strict_types=1);

namespace voku\PHPStan\Rules;

use PhpParser\Node;
use PHPStan\Analyser\Scope;
use PHPStan\Rules\Rule;

/**
 * @implements Rule<\PhpParser\Node\Stmt\ElseIf_>
 */
final class ElseIfConditionBasicRule implements Rule
{

    /**
     * @var array<int, class-string>
     */
    private $classesNotInIfConditions;

    /**
     * @param array<int, class-string> $classesNotInIfConditions
     */
    public function __construct(array $classesNotInIfConditions = [])
    {
        $this->classesNotInIfConditions = $classesNotInIfConditions;
    }

    public function getNodeType(): string
    {
        return \PhpParser\Node\Stmt\ElseIf_::class;
    }

    /**
     * @param \PhpParser\Node\Stmt\ElseIf_ $node
     *
     * @return array<int, \PHPStan\Rules\RuleError>
     */
    public function processNode(Node $node, Scope $scope): array
    {
        if (
            $node->cond instanceof Node\Expr\BooleanNot
            &&
            $node->cond->expr instanceof Node\Expr\Variable
        ) {
            return IfConditionHelper::processNodeHelper(
                $scope->getType($node->cond->expr),
                null,
                $node->cond,
                [],
                $this->classesNotInIfConditions
            );
        }
        
        if (!$node->cond instanceof Node\Expr\Variable) {
            return [];
        }

        return IfConditionHelper::processNodeHelper(
            $scope->getType($node->cond),
            null,
            $node->cond,
            [],
            $this->classesNotInIfConditions
        );
    }
}
