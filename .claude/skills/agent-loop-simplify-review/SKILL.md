---
name: agent-loop-simplify-review
description: Run a read-only diff review through the canonical code-review-simplicity engineering lens, adding only agent-loop evidence and package-boundary context.
---

# Agent Loop Simplify Review

Thin workflow overlay for `code-review-simplicity`. General simplicity rules belong to that engineering lens, not here.

- Supply the complete raw diff, not a summary.
- Use `agent-loop map changed`, `map file`, and `map related` only to verify callers and focused `agent-*` package ownership.
- Dispatch `code-review-simplicity` as the dominant lens.
- Preserve its `STATUS: findings|clean|blocked` result and optional single `HANDOFF:` without widening into a review swarm.
- Keep agent-* ownership as the local overlay: prefer an existing focused package/API over umbrella duplication.
- Read-only. Do not apply fixes.

If `code-review-simplicity` is unavailable:

```text
STATUS: blocked
UNKNOWN: code-review-simplicity capability is unavailable.
```
