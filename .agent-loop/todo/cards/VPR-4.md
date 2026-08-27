# VPR-4: Cover trait bodies, and decide whether the trait deferral PHPStan uses is needed here

- **Ticket:** VPR-4
- **Lane:** BACKLOG
- **Status:** todo
- **Created:** 2026-08-27T00:34:41+00:00
- **Updated:** 2026-08-27T00:34:41+00:00
- **Summary:** PHPStan 2.x routes every constant-condition rule through ConstantConditionInTraitHelper plus ConstantConditionInTraitCollector, because a condition can be constant for one using class and not for another. None of the rules in this package know about traits. Measured: a plain `$int == ''` inside a trait is reported by a native PHPStan run but produces nothing at all under RuleTestCase, whether the trait and the using class live in one file or two - a probe rule that reports unconditionally is never invoked on the trait body either, so it is the harness and not the rules.
- **Format version:** 1

## Agent Task Brief
First establish how to test a trait body at all (PHPStan's own tests use the collector pair; a collector-based harness or an end-to-end run over a fixture project may be needed). Then check whether the rules produce a false positive or a duplicate per using class in a real run, and whether they should defer through the same helper.
