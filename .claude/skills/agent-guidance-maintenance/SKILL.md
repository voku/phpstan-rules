---
name: agent-guidance-maintenance
description: Maintain package-owned and host-owned agent skills, hooks, docs, sync targets, dogfood evidence, provenance capability mapping, and migration-safe validation.
---

# Agent Guidance Maintenance

Use this skill for repository-managed agent guidance: skills, hooks, shared docs,
validation, client synchronization, installation, and host migration notes.
Apply `agent-loop-discipline` to implementation work and
`agent-loop-dogfood` when behavior or hook semantics change.

## Fast Path

1. Edit the canonical source under `docs/agents/` or typed runtime under `src/`.
2. Keep the change scoped to the guidance contract.
3. Update executable help and focused tests when public `init` behavior changes.
4. Run the local dogfood case before broad validation.
5. Validate canonical assets and dry-run package installation for every affected client.
6. Test a clean installed Composer consumer when package-owned assets change.
7. Update README, changelog, notices, capability matrix, and dogfood notes when
   the public contract or provenance changes.
8. Audit for contradictory instructions, duplicate skills, remote bootstraps,
   lossy evidence handling, and unverified claims.

## Canonical Files

- `docs/agents/skills/`;
- `docs/agents/subagents/`;
- `docs/agents/codex-hooks/`;
- `docs/agents/claude-hooks/`;
- `docs/agents/INFO_Agents.md`;
- `docs/agents/UPSTREAM_CAPABILITY_MATRIX.md`;
- `docs/agents/dogfood/`;
- `docs/agents/THIRD_PARTY_NOTICES.md`;
- `src/AgentGuidance/`;
- `src/Init/`;
- `tools/agent-discipline-dogfood.php`;
- `tests/AgentDisciplineHookTest.php`;
- `tests/InitInstallAssetsCommandTest.php`;
- `README.md`, `CHANGELOG.md`, and `.github/workflows/ci.yml`.

Do not begin with generated copies under `.codex/`, `.claude/`, `.github/`, or
`.agents/`. Update the canonical package or host source, validate it, then use
`install-assets` or `sync-*`.

## Package-owned Versus Host-owned

- `init install-assets` always reads the assets shipped inside the installed
  `voku/agent-loop` package. It projects non-executable host assets by default;
  bundled executable Codex/Claude hooks require explicit `--with-hooks`.
- `init sync-skills`, `sync-subagents`, and `sync-hooks` read the host's resolved
  canonical roots and support config/CLI overrides.
- Package-owned repository hooks currently target Codex and Claude. Both call the
  same typed PHP policy; only host registration/output serialization differs.
- Both install and sync paths use target manifests and refuse unmanaged
  overwrites unless the caller explicitly chooses `--force` or
  `--adopt-existing`.
- `sync-hooks --agent=codex` copies `hooks.json` plus scripts.
- `sync-hooks --agent=claude` installs scripts, merges only the `hooks` key of
  `settings.json`, and records it as `settings.json#hooks`. When a client keeps
  hooks inside a shared settings document, own the single key and write every
  other key back unchanged; never rewrite a file the user also edits.
- Host repositories consume the Make targets from `make/agent-loop.mk` instead of
  writing their own wrappers. Add a target there, not in every host Makefile.

Do not make `install-assets` honor a host override for its source. That would
turn an immutable package-install command into another ambiguous sync command.

## Guidance Rules

- Describe behavior that exists now; label future work explicitly.
- Keep human attention, implementation complexity, context size, workflow state,
  and raw evidence as separate concerns.
- Use `agent-map` for bounded navigation; never dump generated indexes into a
  prompt.
- Preserve source, full diffs, command output, tests, and verification artifacts.
- Reject command rewriting or output compression that can hide lines or alter
  redirected files.
- Keep package ownership explicit across the focused `agent-*` repositories.
- Use concise grammatical prose; do not replace clarity with fragments.
- Keep installation offline and package-owned. No remote script, repository
  clone, marketplace, or runtime dependency may enter the init path silently.
- Keep target-manifest safety explicit.
- A progress/output format is guidance, not proof. Workflow state must come from
  persisted artifacts and observed command results.

## Upstream Inspiration Rechecks

`UPSTREAM_CAPABILITY_MATRIX.md` is the review inventory. A source recheck and an
adaptation decision are separate events.

For every reviewed upstream mechanism:

1. pin the source revision;
2. classify the mechanism as `ALREADY`, `ADAPT`, `DEFER`, or `REJECT`;
3. name the concrete `agent-loop` equivalent, owner, or rejection reason;
4. for `ALREADY`/`ADAPT`, point at the smallest test, dogfood case, workflow gate,
   or executable constraint that makes the claim observable;
5. for `DEFER`, name the missing typed ownership/API rather than reaching into a
   focused package's storage layout;
6. revisit old `REJECT` reasons when the upstream mechanism or our architecture
   changes.

Never infer "nothing relevant changed" from a small commit diff alone. First
compare the current upstream capability set against every matrix row and look for
new mechanisms that have no row. Reading a skill or hook does not mean its
behavior was adapted.

## Hook Changes

Keep hook entrypoints thin. Put behavior in typed PHP under `src/` so PHPUnit and
PHPStan can test the same logic the hook executes.

Codex hook output must be checked against both its current schema and parser
semantics. In particular, `PreToolUse` pass-through returns no artificial
permission decision and no rewritten input; a denial uses a non-empty reason and
continues hook processing.

Claude hook output must be checked against current Claude Code semantics rather
than assumed to match Codex merely because field names overlap. For example,
Claude renders top-level `systemMessage` as a user-visible warning, so the shared
context runtime exposes a Claude-specific serialization that omits the Codex
marker. Keep Claude additional context below the documented host output limit.

A bootstrap resume hint may read only a bounded, validated subset of derived run
manifests. Do not inject free-form `next_action`, disagreement messages, task
prose, or copied evidence into hidden context. The hint is navigation; the agent
must resolve current state through the owning workflow command before mutation.

## Dogfood

For every behavior change:

1. choose a real bounded task or hook case;
2. keep baseline and candidate inputs equivalent;
3. change one mechanism at a time;
4. measure observable artifacts;
5. rerun the same case after every fix;
6. record failures, not only the final green result.

When bootstrap state is involved, include hostile free-form manifest content in
the fixture and prove only validated identifiers/state reach the injected
context. This tests the projection boundary rather than merely proving JSON can
be read.

Do not claim saved reasoning tokens or counterfactual code size without actual
telemetry and a valid baseline.

## Hard Constraints

When a reviewed lesson is statically verifiable, prefer the smallest executable
constraint that protects a real property. Register it, test failing and accepted
examples, and baseline only verified legacy violations. Do not convert
subjective style preferences into noisy PHPStan rules.

## Validation

```bash
tools/agent-loop/vendor/bin/agent-loop init validate --kind=all
tools/agent-loop/vendor/bin/agent-loop init install-assets --agent=all --with-hooks --dry-run
tools/agent-loop/vendor/bin/agent-loop init doctor
composer dogfood:discipline
vendor/bin/phpunit --filter 'AgentDisciplineHook|InitInstallAssets|Init|DispatcherTest'
vendor/bin/phpstan analyse --configuration=phpstan.neon.dist --memory-limit=512M
composer ci
```

The clean installed-consumer CI scenario is required when package assets or
`install-assets` change. Never report a command as passed unless its exit status
was observed.
