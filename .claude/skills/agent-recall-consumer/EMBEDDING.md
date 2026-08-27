# PHP Embedding Contract

When Recall is embedded by another PHP package, prefer the typed owner API:

```php
use voku\AgentRecallCompiler\CompileRequest;
use voku\AgentRecallCompiler\RecallCompiler;

$result = (new RecallCompiler())->compile(new CompileRequest(
    learningRoot: $learningRoot,
    taskBrief: $governedRecallInput,
    outputDirectory: $recallOutput,
));
```

Do not call `Cli` or `Command\CompileCommand` from another package, reproduce Recall provider composition, or parse the human CLI success report. Add optional operating-prompt manifests, document manifests, Kanban context, or map inputs through `CompileRequest` only when the caller actually has those owner facts.

The typed call writes the same canonical artifacts as the CLI while emitting no CLI success prose to `STDOUT`, so an outer JSON or structured protocol remains valid.

See `docs/embedding.md` for the full boundary and result contract.
