---
name: agent-loop-task-start
description: Define durable task intent for a governed agent-loop task, then route startup through the lifecycle kernel instead of reproducing preparation or discovery choreography in host guidance.
---

# Agent Loop Task Start

Use this skill when a task needs a durable Contract with explicit intent. This
skill helps choose that intent; it does not own the lifecycle sequence that
follows. The lifecycle kernel decides what is legal next.

## Pre-Lifecycle Workspace Bootstrap

A fresh or isolated coding host may receive a checkout that cannot yet execute the
repository's declared workflow. Restore only the minimum reversible environment
needed to make that workflow runnable before interpreting its absence as a
workflow failure. Typical bootstrap work includes inspecting the current
worktree/remotes, fetching or reconstructing the obvious public repository remote,
installing already-declared Composer dependencies, obtaining required public
sibling checkouts for cross-package work, discovering available host/GitHub
capabilities without printing credentials, and establishing an isolated branch or
worktree before implementation.

This bootstrap is outside the governed product-mutation boundary: it does not
create or approve a Contract, Session, Recall, Finding, proposal, or other owner
state, and it does not authorize product-code changes. Once the lifecycle CLI is
runnable and an isolated implementation workspace exists, return immediately to
the governed front door below.

Do not declare a task blocked merely because `vendor/`, an expected public remote,
`gh`, push credentials, or one preferred PR helper is missing. Recover safe local
bootstrap first. If remote mutation remains unavailable, continue all useful
local inspection, implementation, validation, commits, and dogfood preparation
that the current authority permits. A capability gate is terminal only when the
next genuinely required action cannot be performed and no useful local work
remains.

## Start Through The Front Door

For a stable task id, start or resume with:

```bash
tools/agent-loop/vendor/bin/agent-loop enter <task-id> --format=json
```

For a genuinely new task the kernel may return a `command_template` PLAN command.
Fill the missing Contract inputs from the actual request and current repository
evidence, execute that command without an extra human confirmation merely because
placeholders exist, then call `enter` again and obey the new structured
`next_action_kind` / `next_action`.

Do not pre-build a map, manually create a Session, compile Recall, or infer that
approval should run merely because an old startup checklist said so. If discovery
or another deterministic prerequisite is required, the owner-backed lifecycle
result must name that repair.

A named human approval remains authority-bearing. Never fabricate the approving
actor or self-approve. Before asking, show the exact candidate Contract the human
would own: goal, scope, non-goals, acceptance criteria, validation and relevant
behavior/prompt policy. Approval records authority for the exact Contract
revision; Run, Session and Recall preparation happens deterministically behind
`enter`.

## Contract Intent

A PLAN should carry enough durable intent that a later agent does not need the
original chat to understand the task:

- stable task id;
- actor/planner identity;
- smallest honest file/scope boundary;
- goal;
- explicit non-goals when they prevent scope drift;
- behavior anchors when runtime behavior matters;
- executable validation commands supported by repository evidence;
- acceptance criteria for required outcomes;
- selected operating-prompt policy only when a real recipe/control applies.

The canonical PLAN template is intentionally incomplete until those values are
chosen. Do not persist unresolved placeholders such as `<goal>` or
`<validation>` as real Contract values.

## Task ID

Reuse the external ticket/issue id when one exists. Otherwise choose one stable
local id such as `LOCAL-001` and keep it for the life of the task. Do not create a
new id on every resume.

If existing durable state may exist, `enter`/`workflow status --format=json`
should discover it before another plan is invented beside it.

## Choosing Scope

Select scope intentionally. The Contract should be narrow enough to constrain
work but stable enough that ordinary discovery inside one cohesive subsystem does
not force repeated supersession approvals.

Before persisting the first Contract for a non-trivial task, do one bounded
read-only scope pass using the request and obvious repository evidence. The goal
is not exhaustive design; it is to avoid approving an accidental first-file guess.
Identify the likely implementation owner, its focused tests and the nearest
cohesive path boundary that honestly contains the requested behavior.

Use `--file` for concrete evidence/owners you already know. Use explicit
`--scope` when the honest mutation boundary is broader than those seed files. For
example, when a change clearly spans one component directory plus its focused
tests, approve those bounded paths once rather than revising the Contract every
time another sibling file is discovered.

Prefer the smallest **stable** boundary, not the smallest textual list:

- one exact file is appropriate for a genuinely isolated edit;
- a focused implementation directory plus its focused test directory is
  appropriate when the task is structurally multi-file;
- repository root is not an acceptable default merely to avoid future prompts.

A later file that is already inside the approved scope is ordinary implementation
discovery and does not require re-planning. A file outside the approved scope
requires supersession only when it is actually necessary. If that expansion also
changes goal, policy, acceptance intent or a deliberate boundary, surface the
changed Contract to the human for approval rather than quietly broadening it.

Do not pass the whole repository merely because context is available. Initial
`--file` values become approved scope unless explicit `--scope` values replace
them. If intent or scope genuinely changes later, revise the Contract and let the
lifecycle kernel reconcile superseded working state.

## Acceptance, Validation, And Behavior Anchors

Keep these concepts distinct:

- **acceptance criterion** — outcome/condition that must remain true;
- **validation command** — executable observation used to measure current code;
- **behavior anchor** — runtime/request/consumer seam whose behavior matters.

Example:

```text
acceptance: installed guidance exposes the new control
validation: composer ci
anchor: SessionStart -> injected agent-loop-discipline
```

A criterion is intent, not proof. A validation string must be a real
repository-supported command, not prose or an unresolved placeholder.

## Existing Work Preflight

Inspect overlap before invention. For non-trivial work, inspect bounded relevant
current/recent work when the host can do so cheaply.
An open PR is not correctness evidence.
Classify useful candidates as landed, active, superseded, abandoned, or independent, then try to **falsify it** against the current task intent before
creating a competing implementation.

When evidence shows an existing candidate already owns the same change,
close superseded work instead of creating a competing implementation.
If external history is unavailable, continue from current repository evidence and state the
limitation.

Do not turn this preflight into a new lifecycle state. It informs Contract intent;
the lifecycle kernel still owns what happens next.

## External Reference Preflight

Use this preflight **only** when the task is explicitly defined relative to an
upstream implementation, specification, prior version, or other external
authority. Before running `workflow plan` or sealing scope for approval:

1. identify the exact reference and requested comparison term;
2. inspect a bounded relevant inventory and state what is included, excluded, and still unknown;
3. distinguish a direct port from an adaptation;
4. do not claim parity from a partial inventory;
5. if the reference is too broad, intentionally scope to one rule or behavior;
6. if evidence cannot establish a surface, record that surface as unknown.

For example, `adapt upstream checks` must not silently become `port upstream
checks wholesale`.
This is evidence for choosing Contract intent, not a new lifecycle state. Do not turn it into a parallel discovery workflow.

## Operating Prompts

Recall owns the recipe semantics. Select a reusable recipe/control only when it
actually matches the task, and select it in the Contract rather than copying its
rules into this skill.

For behavior-changing work with a meaningful automated test seam, select
`test-driven-development` using the Recall-owned manifest, for example:

```bash
tools/agent-loop/vendor/bin/agent-loop workflow plan <task-id> \
  --by <actor> \
  --file <path> \
  --goal <goal> \
  --validation <validation> \
  --operating-prompt-manifest tools/agent-loop/vendor/voku/agent-recall-compiler/skills/agent-recall-consumer/operating-prompts.json \
  --operating-prompt '{"id":"test-driven-development","arguments":{}}'
```

Do not restate RED/GREEN/REFACTOR here; the selected Recall recipe owns those
constraints.

For a specific bug claim that first needs proof, prefer `reproduce-before-fix`.
Do not stack both merely because both exist; choose the one whose constraint is
actually needed.

## After PLAN

Once the Contract exists, stop using this skill as a workflow engine. Return to:

```bash
tools/agent-loop/vendor/bin/agent-loop enter <task-id> --format=json
```

and obey the lifecycle kernel. In particular:

- discovery repair comes from `next_action`, not a remembered map preflight;
- approval happens only when the kernel asks for that authority;
- deterministic preparation belongs to `enter`;
- implementation is host-native once authorized;
- deterministic close-out belongs to `finish`.

Use `agent-loop-workflow` for the ordinary routing contract and specialist skills
only for specialist work actually requested by the kernel/task.

## Lower-Level Tools Are Not The Happy Path

Direct `session`, `recall`, `map`, and edit commands remain useful diagnostics,
recovery, navigation, or specialist tools. Their existence does not make them
mandatory startup phases. Do not bypass the governed front door merely because a
lower-level command can reproduce part of its work.

## Skill Boundary

This skill owns choosing durable PLAN inputs and preserving task intent. It does
not own approval policy, discovery readiness, Run/Session/Recall preparation,
close gates, recovery choreography, or package-internal artifact paths.

## Example Triggers

- "Start an agent-loop task for this change."
- "Define the governed task scope before editing."
- "Use agent-loop for this task."
