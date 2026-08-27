---
name: agent-loop-discipline
description: Governed agent-* orchestration: resumable state, map-first navigation, exact evidence, L2 gates, token-efficient agent I/O, review routing, and guidance changes.
---

# Agent Loop Discipline

Rule: persisted workflow state beats conversational state. Keep orchestration, evidence, navigation, and human attention bounded.

## Governed Workflow

The lifecycle kernel decides what may happen next. This skill is injected at
SessionStart, so anything it states about ordering becomes an always-on rule
the kernel cannot correct - which is why it states none.

```bash
tools/agent-loop/vendor/bin/agent-loop enter <task-id> --format=json
tools/agent-loop/vendor/bin/agent-loop finish <task-id> --format=json
```

Obey `next_action_kind` and `next_action` from that result:

- `command` - run it as written;
- `command_template` - fill model-owned placeholders from the actual request and current repository evidence, then execute it without asking a human merely because placeholders exist;
- `decision_required` - a genuine human-authority decision is required; present the exact current decision subject before asking and never fabricate it;
- `host_work` - do the described host-native implementation work;
- `none` - there is no further lifecycle action.

Do not decide when mutation is legal, which gate must pass, whether an
execution contract is current, or how a superseded scope is replaced. Those are
owner decisions the canonical result already carries. If an advertised command
refuses without changing the next step, report a workflow defect rather than
inventing a private repair sequence.

A SessionStart/SubagentStart hint is navigation only. Never infer approval,
contract readiness, validation, review, learning, product intent, or a next
command from it.

Human gates include Contract approval, exact review acknowledgement, Learning disposition when requested by the owner, real risk/irreversible action, and genuinely missing product intent. Reads, edits, tests, diagnostics, reports, model-owned PLAN/execution-contract construction, and agent checkpoints remain agent work.

## Agent I/O

- Inside PHP call the owner's typed API; never render then parse in-process.
- For structured agent reads, request the smallest owner projection and prefer TOON. Keep bounded text when smaller; keep JSON for durable/hash/replay contracts.
- Projection drops fields, never truncates selected values; a missing projected key means “not requested”.

## Prompt Controls

When selected by the approved Contract:
- `checkpoint-autonomy`: at its explicit anchor, inspect scope, evidence, validation, blockers, and done condition; if valid and no human gate exists, checkpoint and continue. Never persist a synthetic human/self approval.
- `momentum`: reuse still-valid files, symbols, commands, constraints, decisions, and evidence; re-check authority/freshness when they may have changed.

These are L1 controls. They do not create an L2 gate by themselves.

## Navigate Before Editing

Trace the real call path before changing shared behavior:

```bash
tools/agent-loop/vendor/bin/agent-loop map query <symbol> --format=toon
tools/agent-loop/vendor/bin/agent-loop map related <symbol> --format=toon
tools/agent-loop/vendor/bin/agent-loop map file <path> --format=toon
tools/agent-loop/vendor/bin/agent-loop map scope <symbol> --format=toon
tools/agent-loop/vendor/bin/agent-loop map context <symbol> --format=toon
tools/agent-loop/vendor/bin/agent-loop map changed --base=<ref> --format=toon
```

Use map projections for PHP symbols/callers/tests/ranges; `rg`/`rg --files` for literal/file discovery. Repository `grep`, `find`, and `sed -i` are blocked. Edit via `agent-loop edit` or an inspected patch; bounded `sed -n` is read-only.

Skip map ceremony for trivial docs or already-localized edits. Never dump map databases; map output selects bounded source reads and is not source evidence.

## L2 Execution Contract

For an approved L2 recipe, construct one project-specific L1 before mutation:

```text
Goal
Context
Constraints
Verification
Done When
```

`Verification` says how reality is measured; `Done When` says which observed result permits success.

```bash
tools/agent-loop/vendor/bin/agent-loop workflow contract <task-id> \
  --status ready \
  --from <project-specific-l1.md> \
  --by <actor>
```

This is model-owned construction from approved task intent and Recall evidence unless the lifecycle explicitly reports a human authority boundary. Do not create an extra confirmation merely because the canonical command is a template.

`missing`, `stale`, `invalid`, `blocked`, or `rejected` means IMPLEMENT is unavailable. Record the evidence and minimum required change; never weaken approved policy merely to reach `ready`.

## Engineering Skill Routing

`agent-loop` owns orchestration, not reusable engineering judgment.

- Simple coding/refactoring -> `coding-simplicity` when installed.
- PHP implementation -> `php-best-practices` when relevant.
- Engineering review -> one dominant installed `code-review-*` lens and at most one evidence-backed handoff.
- Missing required skill -> name the capability gap; do not recreate its rules here.

`coding-simplicity` owns implementation search order, root-cause, safety, and verification floors.

## Role Routing

Use narrow roles only when their verified contract fits:
- definitions/callers/tests -> `agent-loop-investigate`;
- understood 1–2 file edit -> `agent-loop-surgical-edit`;
- correctness review -> `agent-loop-code-review`;
- current-diff complexity -> `agent-loop-simplify-review`;
- repo-wide complexity -> `agent-loop-simplify-audit`;
- ambiguous, architectural, new-feature, or 3+ file work -> main governed workflow.

A narrow role never widens scope or bypasses the execution contract.

## Uncertainty Is State

- Never fabricate versions, paths, lines, commands/results, approvals, contract state, validation/review results, product intent, or runtime facts.
- Prefer the owning state/source or a safe probe; otherwise state the exact unknown and whether it blocks.
- Repeated equivalent failure means inspect the suspect assumption and return to CONTEXT, CONTRACT, or PLAN when necessary.

Preserve exact paths, symbols, commands, numbers, constraints, negation, errors, diffs, tests, static-analysis output, contracts, and verification artifacts. Summaries may point to evidence; they never replace it.

## Workflow Output

Update only when result, blocker, scope, decision, or phase changes:

```text
RESULT: <verified result, decision, artifact, or blocker>
STATE: <phase> <task-id> <Contract revision when known>
NEXT: <one agent-owned action or exact human gate>
```

On completion:

```text
RESULT: <what changed and why>
EVIDENCE: <exact validation results and decisive artifacts>
OMITTED: <deliberate omissions plus revisit trigger, or none>
```

Receipts compress narration, never evidence.

## Hook Boundary

Hooks are behavioral guardrails, never correctness or security boundaries. Code, CI, trust-boundary validation, and offline installation must remain correct without them. Resume hints expose navigation only; authoritative state comes from `workflow status`.

## Validation And Close

Run the narrowest proof first, then the Contract/L1 gates. Claim a pass only after observing it. Stop when approved behavior is satisfied and required gates are closed; do not manufacture follow-up work.

At `ready_to_close`, optional task reflection can expose a concrete completion gap:

```bash
tools/agent-loop/vendor/bin/agent-loop workflow reflect <task-id> --scope task
```

`RETURN_TO_REVIEW` routes that gap back through REVIEW/IMPLEMENT/PLAN.

After successful close, optional project reflection may identify at most one future investment:

```bash
tools/agent-loop/vendor/bin/agent-loop workflow reflect <task-id> --scope project
```

Reflection is read-only, not a close gate, and creates no follow-up automatically.

`workflow close --status done` requires any selected L2 contract to remain current and `ready`. `--accept-risk` never bypasses that boundary.