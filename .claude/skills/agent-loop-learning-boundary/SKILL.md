---
name: agent-loop-learning-boundary
description: Handle reusable knowledge that surfaces during a task, record honest Recall outcomes, close the governed Run learning decision, and keep findings separate from reviewed durable guidance.
---

# Agent Loop Learning Boundary

Use this skill after implementation and review, before a governed close, when the
Run must record its learning outcome. The task may have produced reusable
findings, a follow-up, or no durable learning at all.

The boundary is simple: **findings are not durable memory.** A Run learning
close-out records what happened; it does not approve future guidance.

`ctx` may provide historical evidence for a finding, but it is not another
memory system. Treat ctx hits as raw local session material until inspected and
checked against current repository evidence.

## Fast Path

First resolve repository-owned paths instead of guessing them:

```bash
tools/agent-loop/vendor/bin/agent-loop init paths --format=json
```

After actual implementation and validation, complete the Recall outcome draft and
append it through the Loop wrapper. The wrapper resolves the configured Learning
root automatically:

```bash
tools/agent-loop/vendor/bin/agent-loop recall log-outcome \
  --draft <recall-root>/<task-id>/recall-log.draft.json \
  --by <actor> \
  --commit <sha>
```

Then record the governed **Run learning decision**:

```bash
tools/agent-loop/vendor/bin/agent-loop workflow learn <task-id> \
  --status findings_recorded|no_durable_learning|follow_up_required \
  --by <actor> \
  --reason "<bounded reason>"
```

In the governed workflow, Recall is compiled by `workflow approve`, not by
`workflow plan`. `workflow plan` only creates or revises candidate Contract
intent. The Recall draft therefore exists only after Recall has actually been
compiled for the task.

Validate the configured learning root:

```bash
tools/agent-loop/vendor/bin/agent-loop learn validate
```

Neither `recall log-outcome` nor `workflow learn` approves durable guidance.
`workflow close --status done` requires the Run learning decision to be present.

## The Boundary Rule

```text
finding -> proposal -> reviewed decision -> durable guidance
```

Each arrow is a gate:

- A **finding** is an evidence-backed observation from work. It is not a rule.
- A **proposal** is a structured candidate derived from one or more findings. It
  is still not a rule.
- A **reviewed decision** requires the owning learning workflow and a named human
  actor where approval is required.
- Durable guidance becomes authoritative only after that governed handoff.

Closing a Run does not promote a finding. `review blindspots`, Session
checkpoints, Recall compilation, and prompt construction do not promote anything
either.

## When Findings Were Recorded

If the task produced one or more durable-learning candidates, make the Recall
outcome truthful, validate the Learning root, and reference the resulting finding
IDs in the Run decision:

```bash
tools/agent-loop/vendor/bin/agent-loop recall log-outcome \
  --draft <recall-root>/<task-id>/recall-log.draft.json \
  --by <actor> \
  --commit <sha>

tools/agent-loop/vendor/bin/agent-loop learn validate

tools/agent-loop/vendor/bin/agent-loop workflow learn <task-id> \
  --status findings_recorded \
  --finding <finding-id> \
  --by <actor> \
  --reason "<what was learned and where the evidence lives>"
```

If the host repository uses the proposal pipeline, validate the candidate with
the owning `learn` CLI rather than editing approval state by hand:

```bash
tools/agent-loop/vendor/bin/agent-loop learn proposal-validate \
  --proposal <learning-root>/proposals/candidate/proposal.001.json
```

When a proposal genuinely requires approval, the approving actor must be the
real named human gate required by that command. Do not invent a self-approval to
keep automation moving.

## When There Is No Durable Learning

A local fix that teaches nothing reusable does **not** justify a manufactured
finding. It still requires an explicit Run learning decision:

```bash
tools/agent-loop/vendor/bin/agent-loop workflow learn <task-id> \
  --status no_durable_learning \
  --by <actor> \
  --reason "The evidence was task-local and adds no reusable guidance."
```

Use `no_durable_learning` when, for example:

- the behavior is one-off or entirely local;
- the reusable rule already exists in authoritative guidance;
- the observation depends on transient repository state;
- no evidence-backed recurring pattern emerged.

Do not interpret “no durable learning” as permission to skip the workflow learning
close-out entirely.

## Follow-up Required

If the Run uncovered a concrete learning or ownership action that cannot safely be
completed inside the current Contract, record the real reference rather than
silently widening scope:

```bash
tools/agent-loop/vendor/bin/agent-loop workflow learn <task-id> \
  --status follow_up_required \
  --follow-up <issue-or-task-ref> \
  --by <actor> \
  --reason "<why this cannot be closed as an in-scope finding>"
```

## ctx Evidence Handoff

When a ctx result materially supports, challenges, or explains a finding, record
it as bounded `agent_history_reference` evidence when the Learning schema supports
that evidence type. Inspect the referenced event/session first, retain only the
necessary identifiers and summary, and never copy raw transcripts or
secret-shaped strings into findings.

## Guidance Evaluation

When existing learning guidance may have drifted, evaluate it read-only:

```bash
tools/agent-loop/vendor/bin/agent-loop learn guidance-evaluate
```

This does not create findings, modify proposals, or approve durable guidance.

## MEMORY.md Promotion

If the repository maintains a `MEMORY.md` promotion queue:

```bash
tools/agent-loop/vendor/bin/agent-loop memory review --file=MEMORY.md
```

This reports promotion candidates. It does not edit `MEMORY.md` or approve them.
Do not place raw task output, Session logs, or unreviewed proposals into durable
memory.

## What These Commands Do Not Do

- None of the deterministic `learn`, `recall`, `memory`, or `review` commands is
  evidence that implementation succeeded merely because it ran.
- Recall outcome logging does not create or approve durable rules.
- Run learning close-out does not promote proposals.
- Blind-spot review does not promote learning.
- Memory review does not edit durable memory.

## Skill Boundary

This skill owns:

- honest Recall outcome logging after the work was actually exercised;
- the required governed Run learning decision before close;
- the distinction between findings, proposals, reviewed decisions, and durable guidance;
- choosing `findings_recorded`, `no_durable_learning`, or `follow_up_required` from evidence rather than ceremony.

This skill does not own:

- starting a task (see `agent-loop-task-start`);
- progress/checkpoints during implementation (see `agent-loop-task-progress`);
- engineering review and final close (see `agent-loop-review-close`);
- Recall-specific CLI semantics (see the installed `agent-recall-consumer` skill);
- developing `agent-loop` itself (see `agent-learning` in this repository).

## Validation

Before close:

- the Recall outcome reflects actual application/use rather than default optimism;
- `tools/agent-loop/vendor/bin/agent-loop learn validate` exits successfully;
- exactly one truthful Run learning decision exists for the current close-out state;
- no proposal or durable memory entry was self-approved merely to satisfy the workflow.

## Example Triggers

- "Capture what we learned from this task."
- "Record no durable learning and close cleanly."
- "This should become portable guidance."
- "Is this finding ready for a proposal?"
