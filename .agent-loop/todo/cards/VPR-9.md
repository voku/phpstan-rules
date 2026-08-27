# VPR-9: Triage the agent-loop workflow findings recorded while dogfooding VPR-1

- **Ticket:** VPR-9
- **Lane:** READY
- **Status:** ready
- **Created:** 2026-08-27T00:35:41+00:00
- **Updated:** 2026-08-27T00:40:53+00:00
- **Summary:** Running the governed loop on this repository produced six validated findings about the workflow tooling itself rather than about the PHPStan rules. They live in .agent-loop/learning/findings/validated/ so they survive the session: (1) a directory scope with a trailing slash is accepted by plan and approve but only rejected by the next enter, and correcting it needs --supersede on an approved Contract; (2) learn finding-create reports one missing requirement per invocation and never names the ten accepted evidence types; (3) board card create rejects --validation and --next, which board card update accepts; (4) agent-session rejects --name=value and reports the option as missing rather than as unparsed; (5) agent-loop finish --learning findings_recorded cannot succeed - it needs finding ids, its own command_template omits them, and it rejects --finding, so only the lower-level workflow learn works, and in --format=json the refusal is invisible; (6) init doctor and init status disagree about whether the managed Claude assets are stale on an untouched checkout.
- **Validation:** php tools/agent-loop/vendor/bin/agent-loop learn validate
- **Format version:** 1

## Agent Task Brief
Run 'agent-loop learn backlog' to list them, decide per finding whether it becomes an upstream voku/agent-loop issue or a local convention, and consolidate through 'agent-loop learn prepare' before closing this card. Finding (5) is the one that actually blocks an automated host: it breaks the documented next_action_kind/next_action contract.
