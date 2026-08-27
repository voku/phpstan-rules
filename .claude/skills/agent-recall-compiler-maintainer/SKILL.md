---
name: agent-recall-compiler-maintainer
description: Maintain voku/agent-recall-compiler package source, tests, docs, prompt output, manifest loading, scope selection, outcome logging, and local vendor syncs in consuming projects.
---

# Agent Recall Compiler Maintainer

Use this skill when changing the `voku/agent-recall-compiler` package itself. Keep package behavior generic and deterministic; consuming-project conventions belong in config, examples, or tests.

## Fast Path

1. Inspect `git status --short` and the relevant package source before editing.
2. Preserve source-package authority: package repo first, consuming-project `vendor/voku/agent-recall-compiler` only for local integration testing.
3. Keep selection deterministic: active guidance and constraints are selected by scope overlap and validated metadata, not by semantic similarity.
4. Treat compile blockers as intentional safety behavior. Add tests before relaxing any blocker.
5. Add focused regression tests for manifest loading, config path resolution, scope selection, prompt output, validation-plan output, or outcome logging when those change.
6. For guidance event changes, verify compile output includes `compilation_id`, `evaluated_guidance`, and per-guidance `guidance_outcomes`, and verify `log-outcome` appends both JSONL event types transactionally.
7. Prefer PHP for project-local automation, validation, and dogfood runners. Keep shell only for thin operating-system glue when PHP would make the task less direct.
8. Validate from the package repo.

## Validation

```bash
vendor/bin/phpunit
vendor/bin/phpstan analyse --configuration=phpstan.neon.dist
```

When testing inside a consuming project, separately report those integration commands and do not treat them as a substitute for package tests.
