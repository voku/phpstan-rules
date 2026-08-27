---
name: agent-loop-surgical-edit
description: Apply an already-localized 1-2 file PHP change with the smallest verified diff. Use agent-map evidence, load coding-simplicity for implementation choices when installed, prefer deterministic agent-loop edit when possible, and escalate instead of silently widening scope.
---

# Agent Loop Surgical Edit

Use only when the target and required behavior are already understood and the expected change is bounded to one or two existing files.

## Contract

1. Read the exact target source. Never edit from map output alone.
2. Check relevant callers/tests with `agent-loop map related` when shared behavior changes.
3. Apply `coding-simplicity` when installed; it owns implementation minimization. This role owns only the bounded workflow/edit contract.
4. Apply the smallest correct change in the owning layer.
5. Prefer deterministic execution for exact replacements:

```bash
tools/agent-loop/vendor/bin/agent-loop edit '<Class>::<method>' \
  --runner=auto \
  --replace-old='<old>' \
  --replace-new='<new>' -- \
  '<exact requested behavior>'
```

6. Otherwise edit only the verified target ranges.
7. Run the narrowest meaningful validation, then the required repository gate.
8. Re-read the changed range and inspect the complete raw diff.

## Do Not Expand Silently

No new abstraction, dependency, configuration switch, compatibility layer, cleanup, or unrelated refactor unless the request or validation requires it.

If the correct fix needs 3+ files, a new architectural seam, an irreversible/risk decision, or unresolved product intent, stop the surgical role. Broader work belongs in the normal governed workflow.

## Terminal Result Contract

Return exactly one terminal `STATUS` first. Do not bury escalation in prose.

Successful bounded edit:

```text
STATUS: applied
CHANGE: <path>:<line-range> — <short change>.
EVIDENCE: <exact validation command> — exit <code>; re-read <path>:<line-range>.
```

Verified scope exceeds this role:

```text
STATUS: scope_expanded
EVIDENCE: <verified paths/reason>.
NEXT: main governed workflow must re-plan; do not widen here.
```

A real human decision is required:

```text
STATUS: human_gate
GATE: <exact irreversible action, risk ownership, or missing product-intent decision>.
```

The requested behavior is not yet objectively resolvable:

```text
STATUS: ambiguous
UNKNOWN: <single missing fact that prevents a correct edit>.
```

Validation proves the edit regressed behavior and the fix does not fit this role:

```text
STATUS: regressed
EVIDENCE: <exact failing command/error>.
```

`human_gate` is not a generic request for permission. Reads, edits, tests, source inspection, and diagnostics the agent can run are agent work. `ambiguous` preserves an unknown fact; it must not be replaced by a plausible guess.
