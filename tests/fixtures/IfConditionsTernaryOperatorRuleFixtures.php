<?php

namespace voku\PHPStan\Rules\Test\fixtures;

// Do not compare objects directly
$a = new \stdClass();
$b = $a ? $a->lall : 'foo';
