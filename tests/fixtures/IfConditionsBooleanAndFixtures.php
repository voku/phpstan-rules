<?php

namespace voku\PHPStan\Rules\Test\fixtures;

// Do not compare objects directly
$a = new \stdClass();
$b = rand(1, 2);
$b = $a && $b ? $a->lall : 'foo';
