---
name: agent-loop-workflow
description: Operate the ordinary governed agent-loop path by routing through enter/finish and obeying the lifecycle kernel's structured next step instead of reproducing workflow policy in host prose.
---

# Agent Loop Workflow

Use this skill for the ordinary governed coding path. The lifecycle kernel owns
what is legal next. This skill routes into that kernel and presents its result;
it does **not** keep a second phase machine, gate list, repair table, or owner
artifact checklist.

Persisted owner evidence is authoritative. Conversation prose is not.

## Environment Boundary

The lifecycle owns governed task/product mutation, not the host's reversible
workspace bootstrap. If the declared lifecycle binary cannot run because a fresh
host omitted already-declared dependencies or repository plumbing, first restore
the minimum environment needed to execute it: inspect/fetch the public checkout,
run the repository's declared dependency installation, obtain required public
sibling checkouts for cross-package work, discover available host/remote
capabilities without exposing credentials, and establish an isolated branch or
worktree before implementation.

Do not create lifecycle owner state during bootstrap, and do not use bootstrap as
permission to mutate product code. Once `agent-loop` is runnable and the
implementation workspace is isolated, route through `enter` normally.

A missing preferred remote-write mechanism is a capability boundary, not
automatically a terminal task blocker. Continue useful authorized local work
that does not require that capability, and report a human/capability gate only
when the next required action itself cannot be performed and no useful local
work remains.

## Ordinary Host Contract

Start or resume through the front door:

```bash
tools/agent-loop/vendor/bin/agent-loop enter <task-id> --format=json
```

Read the structured result, especially:

- `mutation_ready` — whether host-native implementation work is currently authorized;
- `next_action_kind` — how to treat the canonical next step;
- `next_action` — the one decisive next step;
- `manifest.references` — supporting owner-backed evidence and reasons;
- `future_work` — repository policy for optional post-completion reflection; it never widens the current Contract.

`next_action_kind` has one treatment contract:

- `command` — execute `next_action` as written;
- `command_template` — fill model-owned placeholders from the actual user request
  and current repository evidence, then execute the resulting command. Do **not**
  ask the human merely because a template contains placeholders. If a required
  value is genuinely missing product intent rather than model-resolvable task
  construction, stop and ask for that missing intent instead of inventing it;
- `decision_required` — a genuine human-authority decision is required. Never
  fabricate it, and never ask for a generic confirmation that hides what is being
  decided. Present the exact current owner-backed decision subject first;
- `host_work` — perform the described host-native implementation/model work;
  the text is not a shell command;
- `none` — no further lifecycle action is required.

Do not combine sibling fields to invent a different workflow decision. If a
canonical command refuses deterministically without changing the next step,
record that as a lifecycle defect rather than teaching the host a workaround.

When host-native mutation is complete, reconcile deterministic close-out through:

```bash
tools/agent-loop/vendor/bin/agent-loop finish <task-id> --format=json
```

Then obey the returned `next_action_kind` / `next_action` in the same way until
`none` / complete. Repeated `enter` and `finish` calls are intended to reconcile
current owner evidence; hosts should not reproduce their preconditions.

## Decision And Review Presentation

A human gate is not useful if the developer cannot see the exact thing they are
being asked to own. Before asking for any `decision_required` action, surface the
smallest complete developer-readable projection of that exact current decision.
Do not ask only “approve?”, “confirm?”, or repeat the command template.

For Contract approval, show the current candidate Contract revision from owner
context: goal, Contract scope, non-goals, acceptance criteria, required validation,
behavior anchors and selected operating prompts when present. `enter --format=json`
already carries bounded `context`; if the host no longer has it, refresh the
read-only projection with:

```bash
tools/agent-loop/vendor/bin/agent-loop workflow context <task-id> --format=json
```

The user must be able to see what scope and validation they are approving before
the host supplies the approving actor. That approval is the ordinary task-authority
gate: once the exact Contract is approved, normal implementation, validation,
review acknowledgement, Learning disposition, Recall outcome logging, local
commits and closeout inside that Contract do not require ceremonial re-approval.

For an exact current review, keep the result visible even though acknowledgement
is normally delegated after Contract approval. `finish --format=json` exposes a
`review_presentation` when the current review needs acknowledgement and
materializes the deterministic HTML workbench. A host may also render it directly:

```bash
tools/agent-loop/vendor/bin/agent-loop workflow review <task-id>
```

Surface the HTML path together with the exact current review SHA-256,
verdict/findings and implementation identity. Then fill the returned review
`command_template` with the acting agent identity and continue. Do not create a
second human confirmation solely to acknowledge an already-visible review. If a
repository or user explicitly requires manual review acknowledgement, the typed
`WorkflowHumanDecisionService` remains available for that stricter host policy.

Learning disposition is likewise ordinary post-approval judgment: choose the
status and reason from the current evidence, record it through the returned
`command_template`, and continue. Accepted risk is different: it transfers risk
ownership and remains a genuine human decision. A changed goal, policy, acceptance
intent or deliberate scope boundary likewise requires a new Contract approval.

## Post-completion Future Work

Future-work reflection is allowed only after the current task is already
`complete` (or explicitly `ready_to_close` for a manual reflection). It is not a
hidden close gate and cannot make an otherwise complete task incomplete.

After `finish` reports `complete=true`, read the current repository policy from
the structured context. If the host no longer has the `enter` context, refresh
that read-only projection:

```bash
tools/agent-loop/vendor/bin/agent-loop workflow context <task-id> --format=json
```

Apply `future_work.mode` exactly:

- `focus` — stop. Do not proactively search for adjacent future work.
- `discover` — run one bounded project reflection with
  `tools/agent-loop/vendor/bin/agent-loop workflow reflect <task-id> --scope project`, report the
  strongest evidence-backed direction (or explicitly report that no worthwhile
  investment direction was found), and do not prepare or execute follow-up work.
- `invest` — run the same bounded project reflection. When it identifies an
  evidence-backed direction worth preparing, use the repository's existing
  task/Kanban owner to prepare at most `future_work.max_follow_up_slices`
  independent follow-up candidates when that owner and identifiers are
  unambiguous. Do not approve or execute them automatically.

In every mode, the completed Contract stays closed. Never fold future-work ideas
back into its scope, never manufacture backlog merely to consume the configured
budget, and never reinterpret repo-local `invest` as authority for a new
Contract. Follow-up execution requires its own normal governance/approval.

## Planning And Human Authority

A genuinely new task may cause `enter` to return a `command_template` PLAN
command. Fill its goal, scope/files, validation and other selected task intent
from the actual request and repository evidence, execute it without an extra
human round-trip, then call `enter` again. `agent-loop-task-start` contains
guidance for choosing stable Contract inputs.

Approval is authority-bearing. When the canonical next step asks for approval,
present the exact candidate Contract as described above and obtain the named
human decision instead of self-approving. Approval records authority for the
exact task Contract; deterministic Run, Session and Recall preparation plus
ordinary post-approval closeout judgments live behind that authority.

Do **not** pre-emptively build a map, compile Recall, create a Session, select a
repair command, or walk a remembered phase sequence because this skill once
listed one. If discovery or another deterministic prerequisite is required, the
kernel must surface the owner-backed repair as the canonical next step.

## Implementation

When `mutation_ready` is true or `next_action_kind=host_work`, perform the
smallest correct change inside the approved scope. Apply `agent-loop-discipline`
and use repository-native tests/static analysis. Use specialist skills such as
`agent-loop-investigate`, `agent-loop-l2-context`, or `agent-loop-surgical-edit`
only when the task actually needs those capabilities; they are not mandatory
happy-path phases.

Generated maps and other derived artifacts are navigation/evidence, never a
second source of lifecycle authority. Query real source after navigation selects
it.

## Validation, Review, Learning And Close

`finish` owns deterministic close-out choreography and reports the first
currently actionable obligation. Do not restate which validation, review,
Learning, Recall, integrity, or close gates must pass here; that list has changed
before and a prose copy will drift again.

When `finish` returns `command_template`, fill the model-owned values from current
evidence and continue without inventing a human gate. For review acknowledgement,
surface the current `review_presentation`/HTML before executing the template. For
Learning, choose the disposition and reason from the validated implementation and
review evidence. When `finish` returns `decision_required`, present the exact
current decision subject first, obtain the real human authority, satisfy exactly
that canonical step, and call `finish` again. Typical remaining human decisions
are Contract approval/supersession, accepted risk, destructive or irreversible
actions, and genuinely unresolved product intent.

`agent-loop verify`, `workflow status`, `workflow report`, and reflection remain
useful diagnostic/read-only surfaces when needed. They are not another mandatory
happy-path sequence beside `enter -> host work -> finish`.

## Prompt Controls And Review Routing

Prompt primitives remain optional controls that the host can route to when the
task needs them; they are not lifecycle states. `checkpoint-autonomy` and
`momentum` tune execution behavior without changing lifecycle authority.

`RETURN_TO_REVIEW` is a review result, not a hidden phase transition. Feed the
result back through the ordinary lifecycle and let the kernel decide the next
step.

Reflection is deliberately **not** another lifecycle phase. Use it only when
additional task/project scrutiny is useful and the current lifecycle state allows
it:

```bash
tools/agent-loop/vendor/bin/agent-loop workflow reflect <task-id> --scope task
tools/agent-loop/vendor/bin/agent-loop workflow reflect <task-id> --scope project
```

## Durable Handoff To Another Agent

When the user asks to preserve the useful current conversation/session context
inside an existing TODO/task/card for a later agent that will not have this chat,
do not paste the transcript and do not reconstruct Recall's `todo-card-handoff`
recipe manually. Summarize only the bounded current-session facts needed to
resume, then route them through:

```bash
tools/agent-loop/vendor/bin/agent-loop workflow handoff <task-id> --context '<bounded handoff notes>'
```

For larger notes, write a temporary/task-local file and use `--context-file`.
The command binds the notes to the current governed Run/Session, adds current
Contract and board evidence, and selects Recall's existing `todo-card-handoff`
recipe. Treat the resulting `system.md` as the prompt for the acting agent; that
agent re-grounds material claims and updates the existing durable task/card
through its owner. `workflow handoff` itself does not make model-authored prose
durable.

## Re-planning

When goal, scope, policy, or acceptance intent genuinely changes, re-plan rather
than stretching the approved Contract conversationally. Ask the lifecycle kernel
for the current state first and follow its canonical next step. Do not manually
retire Session/Run/Recall state from host prose; deterministic supersession and
reconciliation belong to the lifecycle owners.

## Evidence Discipline

Keep complete source/diff/test/static-analysis and generated verification
evidence. Summaries help navigation but do not replace evidence. Findings are not
durable guidance until the Learning owner accepts the appropriate promotion
boundary.

Do not ask humans to run reads, edits, tests, reports, post-approval review
acknowledgements, or Learning dispositions the host can perform. Human interaction
is reserved for real authority, ambiguity, irreversible actions, changed intent,
and explicit risk ownership.

## Progress Receipt

After meaningful progress, report only verified state:

```text
RESULT: <verified result>
STATE: <current persisted lifecycle state>
NEXT: <canonical next step or explicit human gate>
```

Derive `STATE` and `NEXT` from the structured lifecycle result, never from a
remembered phase diagram.
