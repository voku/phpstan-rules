<?php

namespace voku\PHPStan\Rules\Test\fixtures;

$a = rand(0, 1) >= 1 ? new \DOMDocument() : null;
$b = $a->createDocumentFragment();
