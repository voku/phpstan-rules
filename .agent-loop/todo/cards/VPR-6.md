# VPR-6: New rule candidate: loose in_array()/array_search() without $strict

- **Ticket:** VPR-6
- **Lane:** BACKLOG
- **Status:** todo
- **Created:** 2026-08-27T00:34:41+00:00
- **Updated:** 2026-08-27T00:34:41+00:00
- **Summary:** in_array($needle, $haystack) without the third argument is the same PHP 8 comparison hazard the package already covers for ==, and it is the form that silently changed behaviour. PHPStan only covers the impossible-haystack case, through ImpossibleInArrayHaystackFiniteTypesRule, and only behind the finiteTypesInHaystack feature toggle (off by default, on in bleedingEdge).
- **Format version:** 1

## Agent Task Brief
Evaluate a rule that reports a loose in_array()/array_search() when the needle and the haystack value types can compare surprisingly under PHP 8 semantics, in the same spirit as the existing 'Possible insane comparison' message. Keep it out of rules.neon until it has a false-positive fixture.
