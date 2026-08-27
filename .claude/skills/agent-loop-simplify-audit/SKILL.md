---
name: agent-loop-simplify-audit
description: Run a bounded repo-wide complexity audit through the canonical code-review-simplicity lens, using agent-map only to choose and verify candidates.
---

# Agent Loop Simplify Audit

Repo-wide workflow overlay for `code-review-simplicity`. Keep engineering simplicity semantics in the canonical lens.

## Bounded audit

Use generated navigation state only to select candidates:

```bash
tools/agent-loop/vendor/bin/agent-loop map stats
tools/agent-loop/vendor/bin/agent-loop map query <suspect-symbol>
tools/agent-loop/vendor/bin/agent-loop map related <symbol>
tools/agent-loop/vendor/bin/agent-loop map file <path>
```

Use `rg` when needed for structural candidate discovery. Map/search output is navigation, not proof. Verify every candidate against real source and actual callers.

Dispatch `code-review-simplicity` for the selected scope. Preserve its deterministic result and optional single handoff. Keep only one local concern: focused `agent-*` ownership must not be duplicated in the umbrella package.

Read-only. Do not apply fixes.

If `code-review-simplicity` is unavailable:

```text
STATUS: blocked
UNKNOWN: code-review-simplicity capability is unavailable.
```
