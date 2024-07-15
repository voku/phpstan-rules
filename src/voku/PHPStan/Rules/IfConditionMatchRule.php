<?php

declare(strict_types=1);

namespace voku\PHPStan\Rules;

use PhpParser\Node;
use PHPStan\Analyser\Scope;
use PHPStan\Reflection\ReflectionProvider;
use PHPStan\Rules\Rule;

/**
 * @implements Rule<Node\Expr\Match_>
 */
final class IfConditionMatchRule implements Rule
{

    /**
     * @var array<int, class-string>
     */
    private $classesNotInIfConditions;

    /**
     * @var bool
     */
    private $checkForAssignments;

    /**
     * @var null|ReflectionProvider
     */
    private $reflectionProvider;

    /**
     * @param array<int, class-string> $classesNotInIfConditions
     */
    public function __construct(
        array $classesNotInIfConditions,
        bool $checkForAssignments = false,
        ?ReflectionProvider $reflectionProvider = null
    )
    {
        $this->reflectionProvider = $reflectionProvider;
        
        $this->checkForAssignments = $checkForAssignments;
        
        $this->classesNotInIfConditions = $classesNotInIfConditions;
    }

    public function getNodeType(): string
    {
        return Node\Expr\Match_::class;
    }

    /**
     * @param Node\Expr\Match_ $node
     *
     * @return array<int, \PHPStan\Rules\RuleError>
     */
    public function processNode(Node $node, Scope $scope): array
    {
        // init
        $errors = [];

        foreach ($node->arms as $arm) {
            if ($arm->conds === null) {
                continue;
            }
            
            foreach ($arm->conds as $case) {
                $errors = IfConditionHelper::processNodeHelper(
                    $scope->getType($node->cond),
                    $scope->getType($case),
                    $case,
                    $errors,
                    $this->classesNotInIfConditions,
                    $node,
                    $this->reflectionProvider,
                    $this->checkForAssignments,
                    false
                );
            }
        }
        
        return $errors;
    }
}
