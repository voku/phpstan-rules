# agent-loop tool project

[`voku/agent-loop`](https://github.com/voku/agent-loop) requires PHP 8.3+, while
`voku/phpstan-rules` still supports PHP 7.4. Adding the workflow CLI to the root
`composer.json` would therefore make `composer update` unsolvable on the lower
matrix entries of the CI build.

The same pattern agent-loop itself uses for `slop-scan` is used here: the tool
lives in its own Composer project and is invoked from the repository root, so it
sees the repository (and writes `.agent-loop/`) without entering the library's
dependency graph.

## Install

```bash
composer install --working-dir=tools/agent-loop
# or
make agent_loop_install
```

## Use

```bash
php tools/agent-loop/vendor/bin/agent-loop init doctor
php tools/agent-loop/vendor/bin/agent-loop board summary
php tools/agent-loop/vendor/bin/agent-loop enter VPR-1 --format=json
```

`make` wraps the package-owned targets (`agent_init_doctor`, `agent_init_status`,
`install_agent_assets`, ...) with `AGENT_LOOP_BIN` pointed at this project.

The workflow state itself (`.agent-loop/`) is Git-tracked; generated caches
(`map/`, `recall/`, `sessions/`, `edit/`) are ignored through
`.agent-loop/.gitignore`.
