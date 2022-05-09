<?php

declare(strict_types=1);

namespace voku\PHPStan\Rules;

use PhpParser\Node;
use PHPStan\Analyser\Scope;
use PHPStan\Rules\Rule;

/**
 * @implements Rule<\PHPStan\Node\BooleanOrNode>
 */
final class IfConditionBooleanOrRule implements Rule
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
        return \PHPStan\Node\BooleanOrNode::class;
    }

    /**
     * @param \PHPStan\Node\BooleanOrNode $node
     *
     * @return array<int, \PHPStan\Rules\RuleError>
     */
    public function processNode(Node $node, Scope $scope): array
    {
        $cond = $node->getOriginalNode();

        $left = $scope->getType($cond->left);
        $right = $scope->getType($cond->right);
        $errors = [];
        $errors = IfConditionHelper::processNodeHelper(
            $left, 
            $right, 
            $node, 
            $errors, 
            $this->classesNotInIfConditions
        );
        $errors = IfConditionHelper::processNodeHelper(
            $right,
            $left,
            $node,
            $errors,
            $this->classesNotInIfConditions
        );

        return $errors;
    }
}
