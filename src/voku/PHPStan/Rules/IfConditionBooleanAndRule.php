<?php

declare(strict_types=1);

namespace voku\PHPStan\Rules;

use PhpParser\Node;
use PHPStan\Analyser\Scope;
use PHPStan\Rules\Rule;

/**
 * @implements Rule<\PHPStan\Node\BooleanAndNode>
 */
final class IfConditionBooleanAndRule implements Rule
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
        return \PHPStan\Node\BooleanAndNode::class;
    }

    /**
     * @param \PHPStan\Node\BooleanAndNode $node
     *
     * @return array<int, \PHPStan\Rules\RuleError>
     */
    public function processNode(Node $node, Scope $scope): array
    {
        $cond = $node->getOriginalNode();
        
        $errors = IfConditionHelper::processBooleanNodeHelper(
            $cond, 
            $scope, 
            $this->classesNotInIfConditions,
            $node
        );

        return $errors;
    }
}
