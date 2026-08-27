# VPR-8: checkYodaConditions is silently ignored by three rules

- **Ticket:** VPR-8
- **Lane:** READY
- **Status:** ready
- **Created:** 2026-08-27T00:35:08+00:00
- **Updated:** 2026-08-27T00:35:08+00:00
- **Summary:** rules.neon passes checkYodaConditions to IfConditionRule, IfConditionBasicRule, ElseIfConditionBasicRule, IfConditionBooleanAnd/Or/NotRule and ExtendedAssignOpRule. IfConditionTernaryOperatorRule, IfConditionMatchRule and IfConditionSwitchCaseRule have no such constructor parameter at all, so a project that sets checkYodaConditions: true gets no Yoda reporting inside a ternary, a match arm or a switch case and is never told. Separately, IfConditionMatchRule and IfConditionSwitchCaseRule take (array, bool, ?ReflectionProvider) while every other rule takes (array, ?ReflectionProvider, bool, bool), which is a trap for anyone constructing them positionally.
- **Validation:** php vendor/bin/phpunit -c phpunit.xml --filter 'RuleConfigurationTest|RulesNeonRegistrationTest'
- **Format version:** 1

## Agent Task Brief
Either add checkYodaConditions to the three rules or document why a Yoda condition is acceptable there, and align the constructor parameter order. RulesNeonRegistrationTest already compiles every service, so a signature change is covered; add a RuleConfigurationTest case per rule for the flag itself.
