<?php

declare(strict_types=1);

namespace voku\PHPStan\Rules\Test;

use PHPStan\Analyser\Analyser;
use PHPStan\Analyser\AnalyserResultFinalizer;
use PHPStan\Analyser\Error;
use PHPStan\File\FileHelper;
use PHPStan\Testing\PHPStanTestCase;

/**
 * End-to-end proof for #74 using the real rules.neon collector/rule boundary.
 */
final class TraitContextIntegrationTest extends PHPStanTestCase
{
    private const TRAIT_FIXTURE = __DIR__ . '/fixtures/TraitContext/LooseComparisonTrait.php';
    private const INT_CONSUMER_ONE_FIXTURE = __DIR__ . '/fixtures/TraitContext/IntTraitConsumerOne.php';
    private const INT_CONSUMER_TWO_FIXTURE = __DIR__ . '/fixtures/TraitContext/IntTraitConsumerTwo.php';
    private const STRING_CONSUMER_FIXTURE = __DIR__ . '/fixtures/TraitContext/StringTraitConsumer.php';
    private const TRAIT_COMPARISON_LINE = 11;
    private const EXTENDED_BINARY_MESSAGE = "string ('') in combination with non-string (int) is not allowed.";

    /**
     * @return array<int, string>
     */
    public static function getAdditionalConfigFiles(): array
    {
        return [
            \dirname(__DIR__) . '/rules.neon',
        ];
    }

    public function testSingleIntConsumerKeepsItsTraitDiagnostics(): void
    {
        $errors = $this->vokuErrors($this->runAnalyse([
            self::TRAIT_FIXTURE,
            self::INT_CONSUMER_ONE_FIXTURE,
        ]));

        self::assertMessageCount($errors, "Condition between '' and int are falsy", 1);
        self::assertMessageCount($errors, 'double negative integer conditions', 1);
        self::assertMessageCount($errors, self::EXTENDED_BINARY_MESSAGE, 1);
        self::assertTraitSourceForMatchingErrors($errors, 'double negative integer conditions');
        self::assertTraitSourceForMatchingErrors($errors, self::EXTENDED_BINARY_MESSAGE);
    }

    public function testTwoEquivalentIntConsumersPublishEachTraitDiagnosticOnce(): void
    {
        $errors = $this->vokuErrors($this->runAnalyse([
            self::TRAIT_FIXTURE,
            self::INT_CONSUMER_ONE_FIXTURE,
            self::INT_CONSUMER_TWO_FIXTURE,
        ]));

        self::assertMessageCount($errors, "Condition between '' and int are falsy", 1);
        self::assertMessageCount($errors, 'double negative integer conditions', 1);
        self::assertMessageCount($errors, self::EXTENDED_BINARY_MESSAGE, 1);
        self::assertTraitSourceForMatchingErrors($errors, 'double negative integer conditions');
        self::assertTraitSourceForMatchingErrors($errors, self::EXTENDED_BINARY_MESSAGE);
    }

    public function testDifferentUsingClassContextsDoNotPublishContextSpecificTraitDiagnostics(): void
    {
        $errors = $this->vokuErrors($this->runAnalyse([
            self::TRAIT_FIXTURE,
            self::INT_CONSUMER_ONE_FIXTURE,
            self::STRING_CONSUMER_FIXTURE,
        ]));

        self::assertMessageCount($errors, "Condition between '' and int are falsy", 0);
        self::assertMessageCount($errors, 'double negative integer conditions', 0);
        self::assertMessageCount($errors, 'double negative string conditions', 0);
        self::assertMessageCount($errors, self::EXTENDED_BINARY_MESSAGE, 0);
    }

    /**
     * @param array<int, string> $files
     *
     * @return array<int, Error>
     */
    private function runAnalyse(array $files): array
    {
        /** @var FileHelper $fileHelper */
        $fileHelper = self::getContainer()->getByType(FileHelper::class);
        foreach ($files as $index => $file) {
            $files[$index] = $fileHelper->normalizePath($file);
        }

        /** @var Analyser $analyser */
        $analyser = self::getContainer()->getByType(Analyser::class);
        /** @var AnalyserResultFinalizer $finalizer */
        $finalizer = self::getContainer()->getByType(AnalyserResultFinalizer::class);

        return $finalizer->finalize(
            $analyser->analyse($files, null, null, true),
            false,
            true
        )->getErrors();
    }

    /**
     * @param array<int, Error> $errors
     *
     * @return array<int, Error>
     */
    private function vokuErrors(array $errors): array
    {
        return \array_values(\array_filter(
            $errors,
            static function (Error $error): bool {
                $identifier = $error->getIdentifier();

                return $identifier !== null && \strpos($identifier, 'voku.') === 0;
            }
        ));
    }

    /**
     * @param array<int, Error> $errors
     */
    private static function assertMessageCount(array $errors, string $needle, int $expectedCount): void
    {
        $count = 0;
        foreach ($errors as $error) {
            if (\strpos($error->getMessage(), $needle) !== false) {
                ++$count;
            }
        }

        static::assertSame($expectedCount, $count, 'Unexpected diagnostic count for: ' . $needle);
    }

    /**
     * @param array<int, Error> $errors
     */
    private static function assertTraitSourceForMatchingErrors(array $errors, string $needle): void
    {
        $matched = false;

        foreach ($errors as $error) {
            if (\strpos($error->getMessage(), $needle) === false) {
                continue;
            }

            $matched = true;
            static::assertSame(self::TRAIT_COMPARISON_LINE, $error->getLine());
            static::assertSame('LooseComparisonTrait.php', \basename($error->getFilePath()));
        }

        static::assertTrue($matched, 'Expected at least one trait diagnostic containing: ' . $needle);
    }
}
