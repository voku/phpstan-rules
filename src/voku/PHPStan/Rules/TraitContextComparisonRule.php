<?php

declare(strict_types=1);

namespace voku\PHPStan\Rules;

use PhpParser\Node;
use PHPStan\Analyser\Scope;
use PHPStan\Node\CollectedDataNode;
use PHPStan\Rules\Rule;
use PHPStan\Rules\RuleErrorBuilder;

/**
 * Publishes only trait comparison diagnostics that are valid in every observed using-class context.
 *
 * @implements Rule<CollectedDataNode>
 */
final class TraitContextComparisonRule implements Rule
{
    public function getNodeType(): string
    {
        return CollectedDataNode::class;
    }

    /**
     * @param CollectedDataNode $node
     *
     * @return array<int, \PHPStan\Rules\RuleError>
     */
    public function processNode(Node $node, Scope $scope): array
    {
        /**
         * @var array<string, array<string, array{
         *     file: string,
         *     contexts: array<string, array<string, array{string, null|string, int}>>
         * }>> $expressions
         */
        $expressions = [];

        foreach ($node->get(TraitContextComparisonCollector::class) as $fileData) {
            foreach ($fileData as $data) {
                $traitName = $data[0];
                $contextName = $data[1];
                $expressionKey = $data[2];
                $file = $data[3];
                $serializedErrors = $data[4];

                if (!isset($expressions[$traitName][$expressionKey])) {
                    $expressions[$traitName][$expressionKey] = [
                        'file' => $file,
                        'contexts' => [],
                    ];
                }

                if (!isset($expressions[$traitName][$expressionKey]['contexts'][$contextName])) {
                    $expressions[$traitName][$expressionKey]['contexts'][$contextName] = [];
                }

                foreach ($serializedErrors as $serializedError) {
                    $errorKey = \serialize($serializedError);
                    $expressions[$traitName][$expressionKey]['contexts'][$contextName][$errorKey] = $serializedError;
                }
            }
        }

        $errors = [];
        foreach ($expressions as $traitExpressions) {
            foreach ($traitExpressions as $expression) {
                $commonErrors = null;

                foreach ($expression['contexts'] as $contextErrors) {
                    if ($commonErrors === null) {
                        $commonErrors = $contextErrors;

                        continue;
                    }

                    $commonErrors = \array_intersect_key($commonErrors, $contextErrors);
                }

                if ($commonErrors === null || $commonErrors === []) {
                    continue;
                }

                \ksort($commonErrors);
                foreach ($commonErrors as $serializedError) {
                    $builder = RuleErrorBuilder::message($serializedError[0])
                        ->line($serializedError[2])
                        ->file($expression['file']);

                    if ($serializedError[1] !== null) {
                        $builder->identifier($serializedError[1]);
                    }

                    $errors[] = $builder->build();
                }
            }
        }

        return $errors;
    }
}
