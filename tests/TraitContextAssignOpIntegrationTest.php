<?php

declare(strict_types=1);

namespace voku\PHPStan\Rules\Test;

use PHPStan\Analyser\Analyser;
use PHPStan\Analyser\AnalyserResultFinalizer;
use PHPStan\Analyser\Error;
use PHPStan\File\FileHelper;
use PHPStan\Testing\PHPStanTestCase;

/**
 * End-to-end proof that ExtendedAssignOpRule uses the same trait-context publication boundary.
 */
final class TraitContextAssignOpIntegrationTest extends PHPStanTestCase
{
    private const TRAIT_FIXTURE = __DIR__ . '/fixtures/TraitContext/AssignOpTrait.php';
    private const STRING_CONSUMER_ONE_FIXTURE = __DIR__ . '/fixtures/TraitContext/StringAssignTraitConsumerOne.php';
    private const STRING_CONSUMER_TWO_FIXTURE = __DIR__ . '/fixtures/TraitContext/StringAssignTraitConsumerTwo.php';
    private const INT_CONSUMER_FIXTURE = __DIR__ . '/fixtures/TraitContext/IntAssignTraitConsumer.php';
    private const TRAIT_ASSIGN_LINE = 11;
    private const EXTENDED_ASSIGN_MESSAGE = 'in combination with non-string (1) is not allowed.';

    /**
     * @return array<int, string>
     */
    public static function getAdditionalConfigFiles(): array
    {
        return [
            \dirname(__DIR__) . '/rules.neon',
        ];
    }

    public function testSingleStringConsumerKeepsAssignTraitDiagnostic(): void
    {
        $errors = $this->vokuErrors($this->runAnalyse([
            self::TRAIT_FIXTURE,
            self::STRING_CONSUMER_ONE_FIXTURE,
        ]));

        self::assertMessageCount($errors, self::EXTENDED_ASSIGN_MESSAGE, 1);
        self::assertTraitSourceForMatchingErrors($errors, self::EXTENDED_ASSIGN_MESSAGE);
    }

    public function testEquivalentStringConsumersPublishAssignTraitDiagnosticOnce(): void
    {
        $errors = $this->vokuErrors($this->runAnalyse([
            self::TRAIT_FIXTURE,
            self::STRING_CONSUMER_ONE_FIXTURE,
            self::STRING_CONSUMER_TWO_FIXTURE,
        ]));

        self::assertMessageCount($errors, self::EXTENDED_ASSIGN_MESSAGE, 1);
        self::assertTraitSourceForMatchingErrors($errors, self::EXTENDED_ASSIGN_MESSAGE);
    }

    public function testDifferentAssignContextsDoNotPublishContextSpecificDiagnostic(): void
    {
        $errors = $this->vokuErrors($this->runAnalyse([
            self::TRAIT_FIXTURE,
            self::STRING_CONSUMER_ONE_FIXTURE,
            self::INT_CONSUMER_FIXTURE,
        ]));

        self::assertMessageCount($errors, self::EXTENDED_ASSIGN_MESSAGE, 0);
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
            static::assertSame(self::TRAIT_ASSIGN_LINE, $error->getLine());
            static::assertSame('AssignOpTrait.php', \basename($error->getFilePath()));
        }

        static::assertTrue($matched, 'Expected at least one trait diagnostic containing: ' . $needle);
    }
}
