# VPR-4: Cover trait bodies, and decide whether the trait deferral PHPStan uses is needed here

- **Ticket:** VPR-4
- **Lane:** VERIFY
- **Status:** done
- **Created:** 2026-08-27T00:34:41+00:00
- **Updated:** 2026-08-27T10:33:00+00:00
- **Summary:** Measured and explicitly deferred. End-to-end probes showed context-sensitive divergence between PHPStan and this extension for trait bodies, including duplicate per-use diagnostics and cases where PHPStan emits no contextual finding but the extension does. PHPStan 2.2 solves this through `ConstantConditionInTraitHelper` plus `ConstantConditionInTraitCollector`, but that API is not available across the currently declared `~2.0` range. Durable follow-up issue #74 owns the compatibility/floor decision and executable trait-context acceptance criteria.
- **Next:** Continue in #74; do not mix trait deferral into ordinary native-overlap suppression.
- **Validation:** voku/phpstan-rules#74 records the measured divergence and acceptance criteria.
- **Format version:** 1

## Decision evidence

- Multiple trait uses measured native-vs-extension divergence rather than relying on `RuleTestCase` reachability assumptions.
- Context-dependent uses prove that line-level deduplication cannot substitute for trait-context collection.
- Current duplicate suppression fails open in traits so VPR-3 cannot silently delete findings while #74 is unresolved.
