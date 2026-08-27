# VPR-7: Re-evaluate the unconditional Coalesce skip

- **Ticket:** VPR-7
- **Lane:** BACKLOG
- **Status:** todo
- **Created:** 2026-08-27T00:34:41+00:00
- **Updated:** 2026-08-27T00:34:41+00:00
- **Summary:** IfConditionHelper skips BinaryOp\Coalesce in processObjectMethodUsageForComparison, in processObjectComparison and in processNestedObjectComparisons. PHPStan 2.x meanwhile ships an unnecessaryNullCoalesce feature toggle that reports a ?? whose left-hand side is never null.
- **Format version:** 1

## Agent Task Brief
Establish why the skip exists (probably false positives on $foo['x'] ?? null), write the fixture that made it necessary, and check whether the skip is now wider than it needs to be.
