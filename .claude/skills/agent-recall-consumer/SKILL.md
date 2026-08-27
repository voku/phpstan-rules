---
name: agent-recall-consumer
description: Use voku/agent-recall-compiler to compile task-scoped Recall briefings, embed Recall through its typed PHP API, apply explicit operating-prompt recipes, review implementations, and record evidence-backed outcomes.
---

# Agent Recall Consumer

Use this skill when a coding agent needs task-scoped Recall guidance from the current repository. Treat the public PHP API, CLI, and generated artifacts as deterministic inputs to the coding workflow, not as proof that work was executed or verified.

## Ownership

This directory is the canonical home for instructions and reusable recipe assets that directly exercise `agent-recall-compiler`. Recall owns its commands, public compile contract, output contract, review primitives, L2 construction semantics, and bundled operating-prompt catalog.

The bundled manifest is:

```text
skills/agent-recall-consumer/operating-prompts.json
```

From an installed Composer dependency:

```text
tools/agent-loop/vendor/voku/agent-recall-compiler/skills/agent-recall-consumer/operating-prompts.json
```

Callers still select every recipe and provide every required argument explicitly. Bundling the catalog does not create hidden defaults.

## Current Defaults

The standalone CLI owns compact `.agent-loop` defaults:

```text
learning root: <cwd>/.agent-loop/learning
recall output: <cwd>/.agent-loop/recall/<task-id>
```

Do not copy historical project-specific roots or ad-hoc output directories into new automation. Override `--root` or `--output-dir` only when the consuming project intentionally uses a different location.

When `agent-loop` is installed, prefer its wrapper for project-owned path resolution:

```bash
tools/agent-loop/vendor/bin/agent-loop init paths --format=json
tools/agent-loop/vendor/bin/agent-loop recall compile --task PROJECT-123 --description "Implement region-aware navigation" --file src/Navigation/Menu.php
```

`agent-loop recall compile` resolves the configured Learning and Recall roots through the project layout. The standalone compiler uses the defaults above.

## PHP Embedding

When another PHP package owns orchestration, use `RecallCompiler` with `CompileRequest` instead of calling `Cli` or `Command\\CompileCommand`, reproducing provider composition, or parsing human CLI output:

```php
use voku\AgentRecallCompiler\CompileRequest;
use voku\AgentRecallCompiler\RecallCompiler;

$result = (new RecallCompiler())->compile(new CompileRequest(
    learningRoot: $learningRoot,
    taskBrief: $governedRecallInput,
    outputDirectory: $recallOutput,
));
```

The task brief may be the governed `governed_recall_input` envelope; Recall remains responsible for validating its Run/Contract binding. Add optional operating-prompt manifests, document manifests, Kanban context, map input, and ranked-search input through `CompileRequest` only when the embedding host has those owner facts.

Embedded compilation writes the same canonical artifacts as the CLI but emits no CLI success report to `STDOUT`, so an outer JSON or structured host protocol stays valid. `CompileResult` exposes the compilation ID, bundle digest, and common artifact paths without requiring the caller to know CLI spelling or internal provider construction.

See `EMBEDDING.md` and `docs/embedding.md` for the compact and full embedding boundaries.

## Compile

Minimal standalone compile:

```bash
tools/agent-loop/vendor/bin/agent-recall-compiler compile \
  --task PROJECT-123 \
  --description "Implement region-aware navigation" \
  --file src/Navigation/Menu.php
```

For behavioral work, provide concrete files and behavior anchors through the supported task-brief/Contract input instead of dumping the repository. Missing or conflicting governed guidance is a real blocker, not an invitation to invent a fallback.

Use a bundled L2 recipe without copying its semantics elsewhere:

```bash
tools/agent-loop/vendor/bin/agent-recall-compiler compile \
  --task PROJECT-123 \
  --description "Review the current implementation as a first draft" \
  --file src/Navigation/Menu.php \
  --operating-prompt-manifest tools/agent-loop/vendor/voku/agent-recall-compiler/skills/agent-recall-consumer/operating-prompts.json \
  --operating-prompt '{"id":"adversarial-review","arguments":{"minimum_failure_modes":3}}'
```

Recipe selection and arguments are task policy. A selected recipe may be L1 or L2; follow the generated `system.md` contract and do not treat prompt construction as implementation.

## Review

For an immediate context-light falsification lens:

```bash
tools/agent-loop/vendor/bin/agent-recall-compiler review first-draft
```

For project/task-backed review use the current `review` subcommands exposed by `agent-recall-compiler review --help` or the owning `agent-loop review` wrapper when Loop is installed. Review output is evidence for a decision, not approval by itself.

The bundled `adversarial-review` recipe requires actual falsification attempts. Its numeric floor counts plausible failure-mode hypotheses or attack scenarios investigated, not defects that must be manufactured. `CLEAN` remains valid when those probes find no evidence-backed defect.

## Future-work Reflection

Context-light reflection is a separate prompt surface:

```bash
tools/agent-loop/vendor/bin/agent-recall-compiler prompt future-work --scope project
tools/agent-loop/vendor/bin/agent-recall-compiler prompt future-work --scope task
```

It does not mutate workflow state or imply that follow-up work must be created.

## Handoff TODO Cards

Use the bundled `todo-card-handoff` L2 recipe when validated follow-up work must be persisted for another coding agent that will not have the current chat, Session-private context, hidden reasoning, or prior-agent memory:

```bash
tools/agent-loop/vendor/bin/agent-recall-compiler compile \
  --task PROJECT-123 \
  --description "Prepare self-contained follow-up TODO cards for the next coding agent" \
  --file src/Navigation/Menu.php \
  --operating-prompt-manifest tools/agent-loop/vendor/voku/agent-recall-compiler/skills/agent-recall-consumer/operating-prompts.json \
  --operating-prompt '{"id":"todo-card-handoff","arguments":{}}'
```

The recipe does not create cards by itself. It constructs a project-specific L1 prompt from Recall evidence. That prompt must use the repository's existing durable task/card owner and format, update an existing matching card instead of duplicating work, record verified current state and already-completed work, name concrete repository anchors and dependencies, preserve scope/non-goals and owner boundaries, provide exact supported verification/evidence probes and observable Done When criteria, and keep blockers or unknowns explicit. Treat the result as a portable durable work-package candidate, not approved Contract/Run authority. Keep transient host availability, giant environment dumps, secrets, tokens, credentials, and speculative future-VM facts out of durable state. If Recall does not establish which durable task system owns the handoff, the generated prompt must return `BLOCKED` instead of inventing one.

When a requested `production-ready-handoff` contains independently resumable milestones, cross-repository/release sequencing, or follow-up work that should survive the current execution context, do not turn the execution prompt into durable backlog. Route that work explicitly through `todo-card-handoff` and the existing task owner. Recipe selection remains explicit; Recall does not silently create or persist cards.

## Execution Dispatch

Use the bundled `execution-dispatch` L2 recipe only after a durable task/work package already exists and the current execution slice has applicable approved authority:

```bash
tools/agent-loop/vendor/bin/agent-recall-compiler compile \
  --task PROJECT-123 \
  --description "Dispatch the current authorized slice from the durable work package" \
  --file src/Navigation/Menu.php \
  --operating-prompt-manifest tools/agent-loop/vendor/voku/agent-recall-compiler/skills/agent-recall-consumer/operating-prompts.json \
  --operating-prompt '{"id":"execution-dispatch","arguments":{}}'
```

The dispatch recipe builds one short current execution prompt. It must bind to the selected durable task/work-package revision and applicable current Contract/Run/stage authority, re-ground current repository anchors, and include only the current slice plus the evidence needed to execute it. A durable card is not approval, and a generated dispatch prompt is not workflow authority.

If correct execution genuinely depends on runtime capability, consume only bounded current capability evidence supplied through an explicit owner boundary. Do not ingest arbitrary environment dumps, secrets, tokens, credentials, or host-selected task policy. Missing, stale, conflicting, or insufficient required environment evidence is `NOT_READY_TO_DELEGATE` / `BLOCKED`; do not guess. Current repository, authority, and bounded environment evidence win over stale dispatch text, so regenerate the prompt when they drift.

Recall owns prompt semantics, not the Runner-side observation API. In the governed stack, the typed boundary that can supply bounded runtime observations before final prompt construction belongs to `agent-loop`; Runner remains an observation/execution plane and must not concatenate its own workflow prompt context.

## Guidance-gap Technique

To expose where an implementation had to guess because a usable source of truth was absent, run the opt-in L2 helper explicitly:

```bash
tools/agent-loop/vendor/bin/agent-recall-compiler prompt guidance-gaps
```

Give the returned L2 prompt to an agent that already has the current task/spec and repository context. It asks that agent to create a project-specific implementation prompt that maintains `implementation-notes.html` while work proceeds. The journal separates normal design decisions, deviations, tradeoffs, open questions, and actual guidance gaps. Treat the journal as task-local working evidence and do not commit it unless the approved task or harness explicitly requires that artifact.

A guidance gap means the expected authority, such as the specification, repository docs, this or another installed skill, workflow guidance, CLI/tool contract, code, or tests, did not determine the action because it was missing, stale, conflicting, misleading, or too vague. Record the exact anchor and evidence checked instead of merely saying that the agent was uncertain.

This technique is intentionally not a default workflow stage. Do not automatically edit docs or skills, create backlog, or promote journal entries to durable learning. If an interpretation would change approved Goal, acceptance criteria, scope/non-goals, a public contract, security/safety boundaries, or destructive/irreversible behavior, return `HUMAN_DECISION_REQUIRED` / `BLOCKED` instead of guessing.

## Outcomes

After actual implementation and validation, complete the generated `recall-log.draft.json` honestly, then append it to learning history:

```bash
tools/agent-loop/vendor/bin/agent-recall-compiler log-outcome \
  --draft .agent-loop/recall/PROJECT-123/recall-log.draft.json \
  --by agent \
  --commit working-tree
```

When the project overrides the compact layout, pass the real `--root` and draft path rather than assuming defaults. With `agent-loop`, prefer the wrapper because it resolves the configured Learning root:

```bash
tools/agent-loop/vendor/bin/agent-loop recall log-outcome \
  --draft <recall-root>/PROJECT-123/recall-log.draft.json \
  --by <actor> \
  --commit <sha>
```

Treat `selected` as exposure only. Set `applied=true` only when guidance affected the work, and classify outcomes from evidence as `helpful`, `irrelevant`, `harmful`, `not_used`, or `unknown`.

An untouched compiler draft is not feedback: its pre-filled `outcome: unknown`, `applied: false`, `comment: null` rows are placeholders for the later session to complete. An explicit `unknown` outcome requires a non-empty comment explaining why the selected guidance cannot be judged.

When the caller has no evidence to judge selected guidance, do not manufacture `not_used` or `irrelevant` merely to satisfy completeness. Remove the placeholder outcome rows and set `guidance_outcomes_withheld_reason` to a bounded reason. Silent omission without that declared withholding fails. The resulting selection events retain `outcome_withheld_reason`, so downstream Learning can distinguish deliberate absence from dropped feedback.

`not_used` and `irrelevant` are real negative signals used by staleness/retirement policy. A harness that did not read or apply the guidance must not emit either as a convenient empty bucket.

## Output Expectations

A normal compile writes task artifacts below the selected Recall output directory, including:

- `system.md`: selected guidance, warnings, constraints, evidence labels, and selected operating prompts;
- `validation-plan.md`: required validation commands and rule identifiers;
- `recall.bundle.json`, `facts.json`, and `selection-report.json`: deterministic evidence/provenance;
- `meta.json`: compilation identity, selected guidance, constraints, prompt provenance, and output hashes;
- `recall-log.draft.json`: outcome template for close-out;
- verification-plan/key artifacts when the selected map target makes them applicable.

Generated files are not automatically injected into a coding model by the standalone compiler. A harness or `agent-loop` workflow must explicitly consume them.

## Evidence Rules

- Acceptance criteria are required outcomes, not evidence that they passed.
- Use `VERIFIED`, `INFERRED`, `ASSUMED`, `BLOCKED`, or `CONTRADICTED` honestly for material conclusions.
- Prior model reasoning, confidence, reviewer consensus, prompt construction, and unexecuted commands are not verification.
- If required evidence is unavailable, remain `UNKNOWN`/`BLOCKED`; do not silently weaken scope, constraints, or acceptance criteria.
- Selected context does not grant edit permission.

## Close-out Boundary

`log-outcome` appends immutable selection/outcome events under the Learning root. It does not approve durable guidance. Duplicate retries fail rather than partially appending a second outcome.

When `agent-loop` owns the governed Run, enter through `agent-loop enter <task-id>` and reconcile deterministic close-out through `agent-loop finish <task-id>`, following the returned canonical next action until complete. This skill remains the canonical reference for Recall-specific commands, public embedding API, and artifacts; it is not a second workflow lifecycle.
