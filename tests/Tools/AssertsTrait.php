<?php

declare(strict_types=1);

namespace App\Tests\Tools;

trait AssertsTrait
{
    public static function assertDatetimeNow(
        \DateTimeInterface $actual,
        bool $withSeconds = false,
        string $message = ''
    ): void {
        $format = $withSeconds ? 'Y-m-d H:i:s' : 'Y-m-d H:i';

        $expected = (new \DateTime())->format($format);
        $actual = $actual->format($format);

        self::assertEquals($expected, $actual, $message);
    }

    /**
     * @throws \Exception
     */
    public static function assertDatetimeEquals(
        \DateTimeInterface|string $expected,
        \DateTimeInterface|string $actual,
        bool $withSeconds = false,
        string $message = ''
    ): void {
        $format = $withSeconds ? 'Y-m-d H:i:s' : 'Y-m-d H:i';

        $expectedDatetime = ($expected instanceof \DateTimeInterface)
            ? $expected
            : (new \DateTime($expected));

        $actualDatetime = ($actual instanceof \DateTimeInterface)
            ? $actual
            : (new \DateTime($actual));

        self::assertEquals($expectedDatetime->format($format), $actualDatetime->format($format), $message);
    }

    public static function assertIsUuid4(?string $uuid): void
    {
        $pattern = '/^[0-9a-f]{8}-[0-9a-f]{4}-4[0-9a-f]{3}-[89ab][0-9a-f]{3}-[0-9a-f]{12}$/';
        $isUuid = is_string($uuid) && 1 === preg_match($pattern, $uuid);

        self::assertTrue($isUuid, "\"{$uuid}\" is not UUID v4");
    }

    public static function assertHasProperty(string|object $class, string $property): void
    {
        $className = is_string($class) ? $class : $class::class;
        self::assertTrue(
            property_exists($className, $property),
            "\"{$className}\" hasn't property \"{$property}\""
        );
    }

    /**
     * @param string[] $properties
     */
    public static function assertHasProperties(string|object $class, array $properties): void
    {
        array_map(function ($property) use ($class) {
            self::assertHasProperty($class, $property);
        }, $properties);
    }
}
