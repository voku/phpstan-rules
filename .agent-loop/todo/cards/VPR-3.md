# VPR-3: Decide how much of PHPStan 2.x native comparison reporting to duplicate

- **Ticket:** VPR-3
- **Lane:** BACKLOG
- **Status:** todo
- **Created:** 2026-08-27T00:34:41+00:00
- **Updated:** 2026-08-27T00:34:41+00:00
- **Summary:** PHPStan 2.x registers ConstantLooseComparisonRule and StrictComparisonOfDifferentTypesRule at level 4, plus BooleanAnd/Or/Not, Ternary, ElseIf, Match, Switch, DoWhile and WhileLoop constant-condition rules. Measured on tests/fixtures/Php8ComparisonSemanticsFixtures.php: PHPStan reports 4 errors (equal.alwaysFalse / notEqual.alwaysTrue), this package reports 19 on the same lines, three of them per line saying the same thing in three ways.
- **Format version:** 1

## Agent Task Brief
Split the rule set into (a) what PHPStan already reports, (b) what only this package reports. (b) is real and worth keeping: none of the double negative checks - $v != '', $v != 0, $v != false, $v != null - are reported by PHPStan, because those conditions are not constant. For (a), consider a parameter that suppresses the duplicated messages, or narrow the checks to the cases PHPStan misses, so that a project at level 4+ does not get the same finding three times.
