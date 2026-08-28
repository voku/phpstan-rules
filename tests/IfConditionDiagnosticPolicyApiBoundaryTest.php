<?php

declare(strict_types=1);

namespace voku\PHPStan\Rules\Test;

use PHPStan\Parser\LastConditionVisitor;
use PHPUnit\Framework\TestCase;
use voku\PHPStan\Rules\IfConditionDiagnosticPolicy;

final class IfConditionDiagnosticPolicyApiBoundaryTest extends TestCase
{
    public function testMirroredLastConditionAttributeStaysPinnedToPhpStan(): void
    {
        $reflection = new \ReflectionClass(IfConditionDiagnosticPolicy::class);
        $constant = $reflection->getReflectionConstant('LAST_CONDITION_ATTRIBUTE');

        static::assertNotFalse($constant);
        static::assertSame(LastConditionVisitor::ATTRIBUTE_NAME, $constant->getValue());
    }
}
