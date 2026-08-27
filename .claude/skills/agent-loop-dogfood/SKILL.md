---
name: agent-loop-dogfood
description: Evaluate agent guidance against real agent-* tasks with clean, comparable runs and observable artifact metrics instead of invented token savings.
---

# Agent Loop Dogfood

Use this skill when changing agent guidance, hooks, recall, edit orchestration, or
`agent-map` navigation behavior.

## Dogfood Modes

Do not call three different experiments the same thing:

1. **Installer dogfood** proves package-owned skills, subagents, and hooks can be projected into a supported host. Presence is the result; it does not prove a running agent consumed them.
2. **Deterministic lifecycle dogfood** proves Loop/Recall/Session/Learning mechanics and generated artifacts. A harness that never reads `system.md` cannot claim selected Recall guidance or an L2 prompt affected behavior.
3. **Agent-host dogfood** is required for behavioral claims about skills, model guidance, or L2 meta-prompts. Install/project the assets before a clean host session starts, then record which generated briefing the agent actually received.

Host boundaries remain real. A GitHub API/connector write does not execute a local
`pre-commit` or `commit-msg` hook, and repository-local Codex/Claude skills do not
magically become instructions for a different host. Record that capability as
`not_exercised` rather than treating an absent mechanism as a passing experiment.

A fresh hosted environment may also omit reversible workspace bootstrap such as
Composer dependencies, remotes, sibling checkouts, or an isolated implementation
workspace. For agent-host dogfood, record whether the acting agent recovered that
declared environment itself before interpreting lifecycle/tool absence. Do not
count a missing preferred PR/push client as a terminal task blocker while useful
local work still remains.

## Method

1. Choose a real bounded task from `agent-loop`, `agent-map`, or a release-set fixture.
2. Classify the run as installer, deterministic lifecycle, or agent-host dogfood before interpreting its result.
3. Record baseline task, revision, model, host, tools, and validation.
4. For agent-host dogfood, project the intended skills/hooks before starting a clean session. Do not install them mid-session and then claim they influenced earlier work.
5. Record the initial host bootstrap state relevant to execution: lifecycle binary runnable yes/no, declared dependencies installed yes/no, expected public remote available yes/no, required sibling checkout available yes/no, isolated branch/worktree available yes/no, remote-write capability available yes/no/not-needed.
6. When the lifecycle binary is not runnable because reversible declared bootstrap is absent, observe whether the agent restores that environment before declaring the workflow blocked. Record every recovery actually performed, including branch/worktree isolation; do not infer one from the final state.
7. When Map-backed context is under test, let the governed lifecycle surface the discovery repair it requires rather than sequencing Map/Search by hand; build them directly only when the experiment is explicitly about standalone Map/Recall behaviour.
8. When an L2 operating prompt is under test, select it in the approved Contract and prove the agent consumed the generated `system.md`/validation briefing. Compilation plus a hand-written L1 proves wiring, not model use.
9. Run baseline and candidate guidance in separate clean sessions when the host supports it.
10. Keep task wording, repository state, model, and validation identical.
11. Compare observable artifacts, never hidden reasoning.
12. If clean model A/B execution is unavailable, use an already-observed baseline from the same task or review and label that limitation explicitly.
13. Change one guidance/runtime mechanism after each failure and rerun the affected case.

## Real Issues

A guidance, context, prompting, review or learning change is accepted against a
real issue, not only against a synthetic case written beside it. Use
`docs/agents/dogfood/real-issue-acceptance.md` for that protocol: candidate
pre-screen, freeze, the three evidence planes (`agent-map` structure,
`itp-context` architecture intent, `slop-scan` candidate delta), regression
before implementation, project-native gates as the correctness authority, and
the per-tool usefulness ledger.

External evidence tools run from isolated tool projects: `init sync-tools`
writes them, `init tools` reports where they were found. Do not add them to this
package's dependencies to make a run easier.

## Metrics

Record only observable values:

- tool calls and broad source reads;
- files or source lines read before the owning code was found;
- files changed and added/removed lines;
- new dependencies or configuration;
- unrequested behavior added;
- clarification stalls;
- validation commands actually run;
- response words and repeated explanations;
- full diff/evidence inspected: yes/no;
- review findings and regressions;
- projected skills/hooks: yes/no;
- generated Recall briefing consumed by the acting agent: yes/no/not-observable;
- local Git hooks eligible to execute in the host used: yes/no/not-observable;
- initial reversible bootstrap gaps;
- initial isolated branch/worktree present: yes/no/not-observable;
- bootstrap recoveries actually performed by the acting agent;
- isolated branch/worktree recovery performed: yes/no/not-needed/not-observable;
- remote-write capability: available/unavailable/not-needed;
- first genuinely impossible required action, or `none`;
- useful local work left undone when the run stopped: yes/no.

Do not claim saved reasoning tokens without provider telemetry. Do not invent a
counterfactual diff that was never produced.

## Required Cases

- exact PHP symbol change where map plus bounded source should win;
- shared bug where callers must be inspected before the root-cause change;
- documentation-only task where no product code should be added;
- review task where the full diff stays available;
- trivial task where guidance overhead may cost more than it saves;
- hosted-environment task where at least one reversible bootstrap prerequisite is absent and the agent must recover it before governed work can start.

## Acceptance Gate

Keep the candidate only when all are true:

1. correctness, security, validation, and evidence integrity are not worse;
2. no additional unrequested behavior, dependency, abstraction, or configuration is introduced;
3. at least one human-attention or context metric improves on a non-trivial case;
4. trivial tasks do not gain mandatory ceremony;
5. observed failures are reflected in the guidance or runtime, not merely explained away;
6. the report states, per external tool used, whether it materially helped, abstained, missed
   required context, or produced noise;
7. the report does not upgrade installer or deterministic-harness evidence into an unobserved agent-behavior claim;
8. a hosted run does not report `blocked` while reversible bootstrap or useful authorized local work remained available;
9. when a capability gate is reported, the report names the exact next required action that could not be performed.

Installing and invoking a tool is not evidence that it improved the run.
Presence is not usefulness.

A green installer test is not enough. Guidance changes merge only after an
observable behavioral result supports the reason the guidance exists.
