<?php

namespace voku\PHPStan\Rules\Test\fixtures;

final class MyId implements \Stringable {
    private $value;

    final public function __construct(string $value) {
        $this->value = $value;
    }

    final public function __toString(): string {
        return $this->value;
    }
}
