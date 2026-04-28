<?php

declare(strict_types=1);

namespace voku\PHPStan\Rules\Test\fixtures;

$callbackResolver = new class {
    /**
     * @param callable(bool): bool $callback
     *
     * @phpstan-return ($flag is true ? non-empty-string : array{status: 'off'})
     */
    public function resolve(bool $flag, callable $callback)
    {
        if ($callback($flag)) {
            return $flag ? 'ready' : ['status' => 'off'];
        }

        return $flag ? 'ready' : ['status' => 'off'];
    }
};

$callbackResult = $callbackResolver->resolve(
    random_int(0, 1) === 1,
    static fn (bool $flag): bool => $flag
);

if ($callbackResult == []) {
    // ...
}
if ($callbackResult != []) {
    // ...
}
