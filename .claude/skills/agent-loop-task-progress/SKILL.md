---
name: agent-loop-task-progress
description: Record bounded working memory during an agent-loop task, including decisions, checkpoints, validation evidence, scope changes, simplification ceilings, and blockers without copying raw evidence.
---

# Agent Loop Task Progress

Use this skill after a task starts and before review or closure. Apply
`agent-loop-discipline` while implementing. Record only information another
agent or human would otherwise have to rediscover.

## Fast Path

```bash
tools/agent-loop/vendor/bin/agent-loop session record <task-id> \
  --kind decision \
  --title "Keep change scoped" \
  --body "Only update dispatcher routing; recall compiler behavior is unchanged."

tools/agent-loop/vendor/bin/agent-loop session checkpoint <task-id> \
  --title "Validation" \
  --body "vendor/bin/phpunit --filter Init passed with exit code 0."

tools/agent-loop/vendor/bin/agent-loop session show <task-id>
tools/agent-loop/vendor/bin/agent-loop workflow status <task-id>
```

## Record

- implementation direction and package ownership;
- assumptions that future work must verify or preserve;
- exact validation commands and observed results;
- scope changes and re-plan decisions;
- deliberate simplifications with a known ceiling and observable revisit trigger;
- blockers and their cause;
- accepted or rejected risky shortcuts with reason;
- concise handoff information.

Do not record unbounded logs, giant diffs, complete transcripts, copied stack
traces when one decisive line suffices, vague notes, secrets, credentials, or
production data.

## Checkpoint Timing

Checkpoint after:

1. selecting the implementation approach;
2. touching risky code or a public contract;
3. each meaningful validation run;
4. changing scope;
5. reaching review readiness;
6. becoming blocked.

## Scope Changes

```bash
tools/agent-loop/vendor/bin/agent-loop session checkpoint <task-id> \
  --title "Scope change" \
  --body "Task expanded from docs-only to docs plus init help because the executable contract was stale."
```

Re-plan when the approved Contract no longer describes the work. A new revision
invalidates old approval and completion evidence.

## Simplification Ceilings

When the minimal implementation deliberately accepts a real limit, do not leave
an anonymous `TODO` or a tool-specific marker in product code. Record the choice
in session working memory with the ceiling and the condition that would justify
more machinery:

```bash
tools/agent-loop/vendor/bin/agent-loop session record <task-id> \
  --kind decision \
  --title "Simplification ceiling: global lock" \
  --body "Current choice: one global lock. Ceiling: serializes independent accounts. Revisit when: measured lock contention materially affects request latency."
```

The trigger must be observable. "Later", "if needed", and "might scale" are not
triggers. If the lesson becomes reusable across tasks, carry it through the
normal `agent-learning` review instead of turning a one-task decision into a
permanent code comment by accident.

## Structured Validation Evidence

A prose checkpoint explains progress but does not satisfy a governed close:

```bash
tools/agent-loop/vendor/bin/agent-loop session validation record <task-id> \
  --contract-revision <current-revision> \
  --command "vendor/bin/phpunit tests/FocusedTest.php" \
  --status passed \
  --exit-code 0 \
  --by <actor>
```

Use the exact command from the brief. Add duration only when measured. Never
claim a pass from a summary, absent output, or another revision.

## Noise And Evidence

Keep session memory compact while preserving raw evidence unchanged:

- summarize the finding and reference the exact command or artifact path;
- preserve source, full diffs, tests, static-analysis output, and verification
  files;
- read redirected harness output from the stored file;
- record size, line count, or hash when completeness matters;
- use `agent-map` commands to select bounded source instead of copying generated
  indexes into memory.

A summary supports navigation. It is not code review or diagnostic evidence.

## Before Review And Close

Record the review-ready checkpoint here, then hand the task to the installed
`agent-loop-review-close` skill. That skill owns the primary code review,
blind-spot review, Recall outcomes, Learning-root validation, Run learning
decision, verification, reporting, accepted-risk boundary, and final close.
Do not duplicate that sequence here; it previously drifted into skipping the
primary review and hard-coding `no_durable_learning`.

```bash
tools/agent-loop/vendor/bin/agent-loop session checkpoint <task-id> \
  --title "Ready for review" \
  --body "Implementation complete; full diff inspected; required validation evidence recorded."
```

## Completion Check

- `session show` contains bounded, useful notes;
- `workflow status` resolves the current revision;
- exact validation evidence exists;
- deliberate simplifications name a ceiling and observable revisit trigger;
- a review-ready checkpoint exists;
- no secret or raw unbounded output was stored.
