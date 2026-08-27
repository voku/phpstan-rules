# VPR-2: Fix the unreachable array branch in ExtendedBinaryOpRule and ExtendedAssignOpRule

- **Ticket:** VPR-2
- **Lane:** READY
- **Status:** ready
- **Created:** 2026-08-27T00:35:08+00:00
- **Updated:** 2026-08-27T00:35:08+00:00
- **Summary:** Both rules contain a branch that should report "array (...) in combination with non-array (...) is not allowed.", the array counterpart of the string check next to it. Its last condition is strpos($type_2->describe(VerbosityLevel::typeOnly()), 'non-empty-array') !== false. typeOnly() strips accessory types, so a non-empty-array is described as array<int, string> and the needle is never present: the branch has never fired and the message has never been reported. The neighbouring string check works because typeOnly() really does yield 'string' there, which is why the inversion reads as correct.
- **Next:** Decide the intended semantics of the array check, then flip the test.
- **Validation:** php vendor/bin/phpunit -c phpunit.xml --filter ExtendedOpRuleArrayCheckTest
- **Format version:** 1

## Agent Task Brief
Decide what the check should do (most likely: report when the other operand is NOT an array, i.e. the condition wants to be === false or to be replaced by an explicit isArray()->no() test), then make ExtendedOpRuleArrayCheckTest assert the new behaviour instead of today's silence. Evidence: tests/ExtendedOpRuleArrayCheckTest.php pins both the silence and the typeOnly()/value() difference the branch depends on.
