# VPR-5: Check the rules against PHPStan's reportAlwaysTrueInLastCondition semantics

- **Ticket:** VPR-5
- **Lane:** VERIFY
- **Status:** verify
- **Created:** 2026-08-27T00:34:41+00:00
- **Updated:** 2026-08-27T10:47:00+00:00
- **Summary:** Measured and implemented only on the proven native-overlap surface. `IfConditionRule` consumes PHPStan's `reportAlwaysTrueInLastCondition` and `treatPhpDocTypesAsCertain` parameters for constant loose binary comparisons. A final always-true `elseif` generic truth claim is suppressed when PHPStan suppresses it, while extension-only double-negative and PHP 7/8 advice on the same line remain. Switch cases are not marked last by PHPStan, and a match condition immediately before a default is not last, so both remain reportable. Basic array/string truthiness diagnostics are extension-only rather than native overlap and are deliberately left unchanged.
- **Next:** Mark done after exact-head PHPUnit/PHPStan/CI proves both parameter modes and the applicable elseif/match/switch boundaries.
- **Validation:** php vendor/bin/phpunit -c phpunit.xml --filter ComparisonDiagnosticPolicyTest
- **Format version:** 1

## Decision evidence

- Final binary `elseif` is tested with the flag off and on at message level.
- Style/advice on that same line remains when only the generic native-overlap claim is removed.
- Match-before-default remains reportable because `LastConditionVisitor` does not mark it last.
- Switch-before-default remains reportable because PHPStan does not mark switch cases as last conditions.
- Probes showed no corresponding generic extension truth claim for an actual final match arm, so no new diagnostic was invented merely to mirror the parameter.
- Non-empty-array truthiness remains extension-owned; it is not filtered under VPR-5 without proven native overlap.
