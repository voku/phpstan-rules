---
name: agent-loop-l2-context
description: Use agent-loop to compile, inspect, and govern task-scoped Recall/L2 context without duplicating agent-recall-compiler's prompt schema or treating generated artifacts as executed work.
---

# Agent Loop L2 Context

Use this skill when a task needs `agent-loop` Recall compilation, governed L2
prompt construction, bounded context, map navigation, or evidence from the
current repository.

## Ownership Boundary

This skill owns the **agent-loop orchestration surface** around context:

- how Loop invokes Recall for a governed task;
- where Loop exposes the resulting task artifacts;
- when an L2-generated execution contract becomes a Loop gate;
- how Loop combines Recall with map and optional architecture evidence.

`voku/agent-recall-compiler` owns Recall-specific behavior and instructions:

- compile input/output schema;
- operating-prompt manifest schema and bundled first-party recipes;
- L2 -> L1 construction semantics;
- Recall evidence labels and review primitives.

Do not duplicate those contracts here. In an installed project, the canonical
Recall skill and first-party recipe catalog are shipped with the tool at:

```text
tools/agent-loop/vendor/voku/agent-recall-compiler/skills/agent-recall-consumer/SKILL.md
tools/agent-loop/vendor/voku/agent-recall-compiler/skills/agent-recall-consumer/operating-prompts.json
```

When Recall changes, update its owned skill with the code. This Loop skill should
only change when Loop's orchestration of that tool changes.

## Fast Path

For an existing governed task, inspect the bounded view and current state:

```bash
tools/agent-loop/vendor/bin/agent-loop workflow context <task-id> --max-lines 120 --max-bytes 12000
tools/agent-loop/vendor/bin/agent-loop workflow status <task-id>
```

For standalone exploration, compile task-scoped Recall through Loop and let the
wrapper resolve the configured Learning and Recall roots:

```bash
tools/agent-loop/vendor/bin/agent-loop recall compile \
  --task <task-id> \
  --file <path-to-file-1> \
  --file <path-to-file-2>
```

`workflow context` is read-only. It does not recompile Recall, refresh a map, or
embed arbitrary source bodies.

For governed starts, keep `recall-documents.json` intentionally small and
Git-tracked beside the Learning root; the lifecycle owns when Recall receives it.
Use explicit scopes, tags, and excerpt limits instead of making every document
global context.

## Reusable Operational Prompt Recipes

Use Recall's installed first-party catalog instead of a copied recipe file:

```bash
tools/agent-loop/vendor/bin/agent-loop workflow plan <task-id> \
  --by <actor> \
  --file src/Parser.php \
  --file tests/ParserTest.php \
  --goal 'Harden the parser tests.' \
  --validation 'composer ci' \
  --operating-prompt-manifest tools/agent-loop/vendor/voku/agent-recall-compiler/skills/agent-recall-consumer/operating-prompts.json \
  --operating-prompt '{"id":"coverage-mutation","arguments":{"minimum_percentage_points":10,"mutation_command":"vendor/bin/infection --threads=max"}}'
```

Recipe selection and arguments remain explicit Contract policy. Bundling the
catalog with Recall does not make Loop select recipes automatically.

Approval is human authority, and what follows it is the kernel's decision, not
this skill's. Drive governed work through the canonical lifecycle result and use
the bounded context projection when that result or the task calls for it:

```bash
tools/agent-loop/vendor/bin/agent-loop workflow context <task-id> --max-lines 120 --max-bytes 12000
```

If the compiled Recall artifacts require an L2 construction pass, follow the
**current instructions in the Recall-owned `system.md`/skill** rather than a
restated schema in this file. Persist the constructed execution contract through
Loop when the canonical lifecycle result asks for it:

```bash
tools/agent-loop/vendor/bin/agent-loop workflow contract <task-id> \
  --status ready \
  --from <project-specific-l1.md> \
  --by <actor>
```

If construction proves the approved task cannot safely be executed, persist a
`blocked` or `rejected` state with evidence. Do not weaken the approved Contract
inside the generated prompt.

## Recall Artifacts Through Loop

Recall compilation produces the canonical task evidence consumed by Loop,
including `system.md`, `validation-plan.md`, `recall.bundle.json`, `facts.json`,
`selection-report.json`, `recall-log.draft.json`, and `meta.json` under the
configured task Recall root.

These are evidence/harness inputs. They are not automatically injected into an
agent and their presence does not prove an L1 prompt was constructed or executed.

Use the project layout instead of assuming a hard-coded Recall path:

```bash
tools/agent-loop/vendor/bin/agent-loop init paths --format=json
tools/agent-loop/vendor/bin/agent-loop workflow status <task-id>
```

Recompile only through the owning workflow when the approved Contract or relevant
inputs changed. Do not hand-edit generated Recall evidence to make a gate pass.

## Compact Map Navigation

`map` is a lookup tool, not a lifecycle gate. Use it whenever the task needs
precise definitions, callers, or related symbols across more than one or two
files:

```bash
tools/agent-loop/vendor/bin/agent-loop init tools
tools/agent-loop/vendor/bin/agent-loop map build --paths=src,tests   # once; then prefer refresh
tools/agent-loop/vendor/bin/agent-loop map query SomeClass
tools/agent-loop/vendor/bin/agent-loop map related SomeClass
tools/agent-loop/vendor/bin/agent-loop map stale
```

Query the generated map, then inspect the selected real source. Do not dump map
databases into prompts.

## Architecture Intent Evidence

When `init tools` reports `itp-context`, use the reported executable path to query
project-declared architecture intent:

```bash
<reported-path>/itp-context-export var/itp-context src --exclude=vendor --exclude=tests
<reported-path>/itp-context-query var/itp-context --text='<task concepts>'
```

Keep structural and intent evidence distinct:

- map evidence says what code exists and how symbols relate;
- an architecture rule says what the project declared should hold;
- `verified_by` names a check to run, not evidence that it passed.

A repository with no declared architecture rules is normal. Do not invent rules
just to claim the tool was used.

## Direct Edit Routing

For a one-for-one literal replacement inside one exact PHP method, prefer Loop's
token-safe `auto` route:

```bash
tools/agent-loop/vendor/bin/agent-loop edit 'App\Service\UserService::save' \
  --runner=auto \
  --replace-old='$legacyUser->regionId' \
  --replace-new='$legacyUser->getCurrentRegionId()' -- \
  'Replace the deprecated region property exactly once.'
```

With both literals, `auto` can select the scoped mechanical runner. Without
sufficient proof it records `escalation_required` instead of launching a model.
Use an explicit command runner only when the edit actually requires coding
judgment.

## Review And Outcomes

After implementation, use Loop's review routing rather than interpreting Recall
artifacts as approval:

```bash
tools/agent-loop/vendor/bin/agent-loop review blindspots <task-id>
tools/agent-loop/vendor/bin/agent-loop review code <task-id>
```

Record Recall outcomes only after actual work and validation happened. The Loop
wrapper resolves the configured Learning root:

```bash
tools/agent-loop/vendor/bin/agent-loop recall log-outcome \
  --draft <recall-root>/<task-id>/recall-log.draft.json \
  --by <actor> \
  --commit <sha>
```

Selected guidance or recipes are exposure, not proof of usefulness. Keep outcome
classification evidence-backed.

## Validation

Before claiming context work is complete:

- confirm the current task's Recall artifacts exist through `workflow status`;
- inspect generated evidence before using it;
- run the task's approved validation commands and record their observed result;
- run `tools/agent-loop/vendor/bin/agent-loop verify --task-id=<task-id>` before close.

## Skill Boundary

This skill does **not** own:

- Recall's prompt schema or first-party recipe definitions;
- Recall review semantics;
- the task opening/approval decision;
- review/close policy outside context orchestration;
- external evidence-tool installation.

Those contracts stay with their owning tool or workflow skill. This prevents an
umbrella skill from becoming a stale second implementation written in Markdown.
