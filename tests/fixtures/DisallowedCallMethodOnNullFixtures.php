<?php

namespace voku\PHPStan\Rules\Test\fixtures;

$a = rand(0, 1) >= 1 ? new \DOMDocument() : null;
$b = $a->createDocumentFragment();

// No error: variable is definitely not null
$c = new \DOMDocument();
$d = $c->createDocumentFragment();

// No error: nullable but method called after null-check
$e = rand(0, 1) >= 1 ? new \DOMDocument() : null;
if ($e !== null) {
    $f = $e->createDocumentFragment();
}

// Error: another nullable object used directly
$g = rand(0, 1) >= 1 ? new \DOMElement('div') : null;
$h = $g->getAttribute('id');
