# Review evidence for PHP hosts

When a PHP lifecycle host needs to inspect the current blind-spot review, use the Recall-owned read API instead of locating or parsing `*.blindspots.json` itself:

```php
<?php

declare(strict_types=1);

use voku\AgentRecallCompiler\Review\ReviewReportReader;

$artifact = (new ReviewReportReader($projectRoot))->read(
    taskId: $taskId,
    outputDir: $recallOutputDirectory,
);

if ($artifact === null) {
    // No review report exists yet.
}

$artifact?->report->status();
$artifact?->report->contractRevision;
$artifact?->report->implementationSnapshot;
$artifact?->sha256;
```

The returned SHA-256 value identifies the exact persisted JSON report bytes. It is suitable for a lifecycle owner to reference when recording an acknowledgement of the report it actually presented or reviewed.

Recall owns report parsing, status consistency, Contract/snapshot binding validation, and artifact identity. The lifecycle host owns any authority-bearing acknowledgement or close-out decision. A report's existence or digest never proves that a human or model reviewed or understood it.

Missing reports return `null`. Present but malformed, stale-shaped, task-mismatched, or internally inconsistent reports fail explicitly rather than being treated as absent or successful.
