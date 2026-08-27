---
name: agent-loop-code-review
description: Govern a read-only review of the complete raw diff, routing one dominant engineering lens and at most one focused handoff while preserving exact evidence and deterministic terminal state.
---

# Agent Loop Code Review

Own the review workflow, not the engineering handbook.

## Contract

1. Review the complete raw diff and task/brief evidence. Never review a summary instead.
2. For a governed task, run `agent-loop review code <task-id>` first and use the generated task-artifact-backed prompt as the review framing. It carries Recall's first-draft falsification lens and evidence boundaries; do not replace it with remembered prose.
3. If there is no governed task/artifact set and only a context-light adversarial pass is needed, use `agent-loop review first-draft` instead.
4. Use `agent-loop map changed --base=<ref>` and focused caller/context lookup when a claim depends on surrounding code. Verify against real source.
5. Select **one dominant** installed `code-review-*` engineering lens for the most material concern. Do not run a default review swarm.
6. Accept at most one `HANDOFF:` only when it names an installed lens plus evidence `path:line` and why that concern is dominant. Otherwise return `STATUS: blocked` with the missing target/evidence.
7. Persist/report the lens result without turning it into workflow approval. `review blindspots` remains a separate deterministic process/evidence check.
8. Read-only. Do not apply fixes during review.

The first-draft lens is adversarial without creating a finding quota. `STATUS: clean` remains valid after concrete falsification attempts find no evidence-backed defect. Missing material evidence stays `UNKNOWN`/`blocked`; model confidence, prior rationale, prompt construction, or an unexecuted command are not verification.

## Optional Deterministic Observation

When `init tools` reports `slop-scan` as available, it can supply heuristic
findings alongside the lens. Ask it for the delta between the base and the
candidate — do not hand-roll one from two `scan` runs:

```bash
<reported-path> delta <base-checkout> . --json --ignore 'vendor/**'
```

Its `added` count includes findings whose only change is a line number, so
match `added` against `resolved` by rule, path and line content first.

- existing repository slop is not a finding against this diff; the change owns
  new findings, resolved findings and changed fingerprints;
- a heuristic hit is an observation, not a verdict: promote it to
  `STATUS: findings` only when you can state the defect and the concrete fix
  from the real source;
- no score threshold decides a review outcome.

The tool is an input to the dominant lens. It does not replace it, does not add
a second lens, and its absence does not block a review.

If no applicable engineering lens is available:

```text
STATUS: blocked
UNKNOWN: no applicable code-review-* capability is available.
```

Lens results are:

```text
STATUS: findings
<path>:<line>: <severity> <problem>. <concrete fix>.
HANDOFF: <code-review-* lens> <path>:<line> <why this concern is dominant>   # optional, at most one
```

```text
STATUS: clean
```

```text
STATUS: blocked
UNKNOWN: <exact missing evidence>.
```

Correctness comes from the selected engineering capability plus exact evidence. `agent-loop` owns scope, routing, persistence, and workflow progression.
