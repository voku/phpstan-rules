# VPR-5: Check the rules against PHPStan's reportAlwaysTrueInLastCondition semantics

- **Ticket:** VPR-5
- **Lane:** BACKLOG
- **Status:** todo
- **Created:** 2026-08-27T00:34:41+00:00
- **Updated:** 2026-08-27T00:34:41+00:00
- **Summary:** PHPStan deliberately stays silent about an always-true *last* condition of an if/elseif chain unless reportAlwaysTrueInLastCondition is enabled, because that last branch is often a deliberate guard. The rules in this package have no equivalent notion and report 'are always false' / 'is always true' unconditionally.
- **Format version:** 1

## Agent Task Brief
Build fixtures for the last condition of an if/elseif chain, a match default and a switch default, compare against a native PHPStan run with the parameter both off and on, and decide whether this package should honour the same parameter.
