---
name: agent-loop-review-close
description: Review, verify, and close an agent-loop task safely after implementation, including code review, blind-spot review, truthful Recall outcomes, Learning validation, governed Run learning close-out, accepted-risk boundaries, and optional reflection.
---

# Agent Loop Review Close

Use this skill after implementation when a governed Run needs review evidence,
truthful learning close-out, verification, reporting, and final close. This is
the single owner of that sequence; progress/edit skills should not copy it.

## Fast Path

Resolve project-owned paths and persisted state first:

```bash
tools/agent-loop/vendor/bin/agent-loop init paths --format=json
tools/agent-loop/vendor/bin/agent-loop workflow status <task-id> --format=json
```

Then preserve the actual execution and review evidence in this order:

```bash
tools/agent-loop/vendor/bin/agent-loop session validation record <task-id> \
  --contract-revision <n> \
  --command "<exact Contract validation command>" \
  --status passed \
  --exit-code 0 \
  --by <actor>

tools/agent-loop/vendor/bin/agent-loop review code <task-id>
tools/agent-loop/vendor/bin/agent-loop review blindspots <task-id>

tools/agent-loop/vendor/bin/agent-loop recall log-outcome \
  --draft <recall-root>/<task-id>/recall-log.draft.json \
  --by <actor> \
  --commit <sha>

tools/agent-loop/vendor/bin/agent-loop learn validate

tools/agent-loop/vendor/bin/agent-loop workflow learn <task-id> \
  --status <findings_recorded|no_durable_learning|follow_up_required> \
  --by <actor> \
  --reason "<bounded conclusion from the observed evidence>"

tools/agent-loop/vendor/bin/agent-loop verify --task-id=<task-id>
tools/agent-loop/vendor/bin/agent-loop workflow report <task-id> --changed-file <path>
tools/agent-loop/vendor/bin/agent-loop workflow close <task-id> --status done
tools/agent-loop/vendor/bin/agent-loop workflow status <task-id> --expect complete
```

Use repeatable `--finding <finding-id>` when the truthful learning status is
`findings_recorded`. Do not invent a finding, a passing validation, or
`no_durable_learning` merely to make the sequence complete.

## Review Boundary

`review code` is the primary correctness review for a governed task and must run
before `review blindspots`. The blind-spot review is separate process/evidence
analysis; neither review grants approval or substitutes for validation.

When there is no governed task/artifact set, use the context-light
`review first-draft` flow instead of pretending a governed review exists.

## Validation Evidence

The Contract owns the required validation command strings. Record a pass only
after observing the exact current-revision command result. Re-planning creates a
new Contract revision; evidence for an older revision does not satisfy it.

Task verification is:

```bash
tools/agent-loop/vendor/bin/agent-loop verify --task-id=<task-id>
```

Repository-wide `verify` remains available separately. Use `--strict` only when
all expected roots/components must exist rather than being allowed to skip.

## Learning Boundary

Recall outcomes describe whether selected guidance held. Learning findings are
not durable guidance. Complete the Recall draft, validate the configured
Learning root, and choose exactly one Run-learning conclusion from evidence:

- `findings_recorded`: reusable evidence-backed findings exist; reference them
  with `--finding`;
- `no_durable_learning`: the evidence is task-local or already covered by
  authoritative guidance;
- `follow_up_required`: a concrete unresolved learning follow-up remains.

The detailed lifecycle remains owned by `agent-loop-learning-boundary`.
`workflow close` consumes the Run decision; it does not approve proposals or
promote memory.

## Report And Scope

`workflow report` is a read-only handoff. Pass every observed changed path with
repeatable `--changed-file`; it deliberately does not run Git or infer scope.
If scope no longer matches the approved Contract, re-plan instead of laundering
the difference through close-out prose.

## Optional Reflection

At `ready_to_close`, task reflection may provide extra scrutiny:

```bash
tools/agent-loop/vendor/bin/agent-loop workflow reflect <task-id> --scope task
```

If it returns `RETURN_TO_REVIEW`, resolve that concrete gap before close. After a
successful close, project reflection can identify future investment:

```bash
tools/agent-loop/vendor/bin/agent-loop workflow reflect <task-id> --scope project
```

Reflection is not one of the workflow lifecycle phases; it is a read-only prompt primitive and never a completion or promotion gate by itself.

## Accepted Risk

Accepted risk is a named waiver only for bypassable evidence gates:

```bash
tools/agent-loop/vendor/bin/agent-loop workflow close <task-id> \
  --status done \
  --accept-risk "<specific understood risk>" \
  --accept-risk-by "<named actor>"
```

It cannot change task authority, and some gates refuse it. Which gates those
are is decided by the lifecycle kernel, not by this skill: close reports the
gate it refused on, and accepted risk is rejected where it does not apply. Do
not use accepted risk as a generic "make it green" switch.

## When Close Fails

Ask the lifecycle kernel which gate is decisive rather than working down a
checklist:

```bash
tools/agent-loop/vendor/bin/agent-loop workflow status <task-id> --format=json
```

`references.<name>.gate` names the decisive gate, `references.<name>.reason`
carries the exact failure, and `next_action` is the canonical next step. Repair
the owner that gate names, checkpoint the repair when it matters for
resumability, then rerun `finish`.

## Completion Check

Completion is `manifest.state == "complete"` with `next_action == "none"` from
`workflow status --format=json` after `finish` succeeds. That state is derived
from current owner evidence; it is the answer, not a summary of one.

Do not restate the gate list here. This skill previously carried a prose copy
of it, which drifted: it enumerated eight of the eleven gates the close
readiness inspector actually runs, omitting compiled-Recall presence,
implementation-snapshot availability and evidence-integrity binding. An agent
following that copy would have believed it had enumerated the requirements
while missing three gates that can block close, and nothing could detect the
drift, because prose has no test.
