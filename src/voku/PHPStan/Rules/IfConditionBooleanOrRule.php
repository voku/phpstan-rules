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
        $rightScope = $node->getRightScope();

        $errors = IfConditionHelper::processBooleanNodeHelper($cond->left, $scope, $this->classesNotInIfConditions);
        $errors = array_merge($errors, IfConditionHelper::processBooleanNodeHelper($cond->right, $rightScope, $this->classesNotInIfConditions));

        return $errors;
    }
}
