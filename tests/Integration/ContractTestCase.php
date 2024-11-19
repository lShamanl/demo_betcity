<?php

declare(strict_types=1);

namespace App\Tests\Integration;

use Exception;
use Symfony\Component\Validator\ConstraintViolation;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class ContractTestCase extends IntegrationTestCase
{
    protected static ValidatorInterface $validator;

    public static function setUpBeforeClass(): void
    {
        parent::setUpBeforeClass();
        self::$validator = self::get(ValidatorInterface::class);
    }

    /**
     * @param array<string> $expectedErrors
     *
     * @throws Exception
     */
    protected static function assertViolations(object $contract, array $expectedErrors): void
    {
        /** @var array<string, string> $violations */
        $violations = [];

        /** @var ConstraintViolation $violation */
        foreach (self::$validator->validate($contract) as $violation) {
            $violations[$violation->getPropertyPath()] = $violation->getMessage();
        }

        foreach ($expectedErrors as $expectedProperty => $expectedMessage) {
            if (!isset($violations[$expectedProperty])) {
                throw new Exception("Undefined array key '{$expectedProperty}'");
            }
            $violationMessage = $violations[$expectedProperty];
            if (is_int($expectedProperty)) {
                throw new Exception(
                    sprintf('Field name value not set for next error: "%s"', $expectedMessage)
                );
            }

            self::assertNotNull($violationMessage, 'Violation property is not exist');
            self::assertSame(
                $violationMessage,
                $expectedMessage,
                sprintf(
                    'Actual error "%s" is not equals for "%s"',
                    (string) $violationMessage,
                    (string) $expectedMessage
                )
            );
            self::assertContains(
                $expectedMessage,
                $expectedErrors,
                sprintf(
                    'Error for property "%s" with value "%s" is not provided',
                    (string) $expectedProperty,
                    (string) $expectedMessage
                )
            );
        }

        if (!empty($violations) || [] !== $expectedErrors) {
            self::assertCount(
                count($violations),
                $expectedErrors,
                sprintf(
                    'Count violations and expected values not match. %s Violations: %s %s Expected: %s',
                    PHP_EOL,
                    var_export($violations, true),
                    PHP_EOL,
                    var_export($expectedErrors, true)
                )
            );
        } else {
            self::assertEmpty($violations);
        }
    }
}
