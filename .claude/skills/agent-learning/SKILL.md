---
name: agent-learning
description: Capture reusable lessons about agent-loop workflow, validation, migration, evidence integrity, discipline dogfood, and package ownership before promoting durable guidance.
---

# Agent Learning

Use this skill after implementation or migration work exposes a reusable lesson
for `agent-loop` or another `agent-*` package. Keep the lesson evidence-backed,
bounded, and placed in the surface that owns it.

## Fast Path

1. Check whether the lesson already exists in README, changelog,
   `docs/agents/`, or `docs/workflow/`.
2. Search history only when prior decisions or failed attempts materially affect
   the conclusion.
3. Sweep the complete validated backlog, not only the current session.
4. Cluster findings by owning package or workflow boundary.
5. Promote each lesson to the lowest mechanism that solves the verified problem:
   existing doc, existing skill, focused new skill, typed runtime, test, dogfood
   case, or executable constraint.
6. Validate the behavior and name every deliberate residual item.

## Whole-backlog Discipline

Use the learning registry and backlog gate to enumerate every validated,
unconsolidated item. Completion means zero residual or an explicit reason for
each deferred item. Handling only recent findings is recency bias, not a
maintenance pass.

## Value Ladder

```text
raw finding
  -> reviewed guidance or durable memory
  -> typed runtime or dogfood case when behavior must execute
  -> static constraint when the property is reliably analyzable
```

Do not stop at a memory sentence when a small test, hook runtime, or PHPStan rule
can prevent recurrence. Do not create executable noise for subjective advice.

## Evidence Integrity

Source, full diffs, command output, tests, static-analysis output, and generated
verification artifacts remain unchanged during evaluation. Concise human-facing
summaries may point to evidence; they never replace it.

When a harness redirects output, read the stored file and record size, line
count, or hash when completeness matters. Do not infer a pass from silence or an
agent explanation.

## Guidance Dogfood

Use `agent-loop-dogfood` when changing skills, hooks, recall, edit orchestration,
or map navigation behavior:

- baseline and candidate use the same task and repository revision;
- compare observable artifacts and review quality;
- change one mechanism at a time;
- rerun the same case after a failure;
- record failed iterations in `docs/agents/dogfood/`;
- add the stable case to `composer dogfood:discipline` or the installed-consumer
  gate when it protects a package contract.

Do not publish token or code savings without provider telemetry and a valid
baseline. The unbuilt alternative is not measurable evidence.

## Existing Guidance First

Inspect:

- `docs/agents/INFO_Agents.md`;
- `agent-loop-discipline`;
- `agent-loop-simplify-review`;
- `agent-loop-dogfood`;
- the relevant workflow skill;
- `docs/agents/dogfood/` and `THIRD_PARTY_NOTICES.md`;
- `docs/workflow/learning-boundary.md`;
- README and changelog.

Refine the existing home instead of creating duplicate rules with different
names.

## Historical Context

```bash
ctx search "<task / migration / failure / command>"
ctx show event <ctx-event-id> --window 5
```

History explains what happened; it does not prove current behavior. Persist only
bounded IDs, query, retrieval time, reviewed summary, and verification status.
Never promote raw transcripts or secrets.

## Promotion Targets

- `agent-loop-discipline` for map-first navigation, minimal PHP changes, concise
  communication, package ownership, and evidence integrity;
- `agent-loop-simplify-review` for complexity-only review;
- `agent-loop-dogfood` and `docs/agents/dogfood/` for repeatable behavioral
  evaluation;
- typed classes under `src/AgentGuidance/` when behavior must execute;
- focused PHPUnit and installed-consumer scenarios for runtime/package contracts;
- shared docs and workflow docs for operational boundaries;
- PHPStan or coding-standard rules for precise static constraints;
- changelog for released or unreleased behavior.

Keep lessons specific. Good guidance names a command, file, consumer, failure
boundary, and verification. Generic slogans are decoration.

## Validation

```bash
tools/agent-loop/vendor/bin/agent-loop init doctor
tools/agent-loop/vendor/bin/agent-loop init validate --kind=all
tools/agent-loop/vendor/bin/agent-loop init install-assets --agent=codex --dry-run
composer dogfood:discipline
vendor/bin/phpunit --filter 'AgentDisciplineHook|InitInstallAssets|Init|DispatcherTest'
vendor/bin/phpstan analyse --configuration=phpstan.neon.dist --memory-limit=512M
composer ci
```

The installed-consumer gate is required when package-owned assets change. Claim
only validation whose exit status was observed.
