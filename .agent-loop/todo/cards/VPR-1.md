# VPR-1: Rebuild the rule test-suite around real regressions

- **Ticket:** VPR-1
- **Lane:** READY
- **Status:** ready
- **Created:** 2026-08-27T00:16:14+00:00
- **Updated:** 2026-08-27T00:16:14+00:00
- **Summary:** The PHPUnit suite asserts a handful of happy-path messages per rule, has almost no false-positive coverage, never asserts error identifiers, never exercises the rules with their configuration flags turned off, and never loads rules.neon. Replace that with a suite that fails when behaviour regresses.
- **Format version:** 1

## Agent Task Brief
Add regression-grade tests for every rule in src/, covering: no-false-positive fixtures, error identifiers, configuration flags in both states, rules.neon wiring, and the PHPStan-version-coupled expectations that are already red.
