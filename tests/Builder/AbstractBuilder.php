<?php

declare(strict_types=1);

namespace App\Tests\Builder;

use ArrayAccess;
use Closure;
use Faker\Factory;
use Faker\Generator;
use ReflectionClass;
use ReflectionException;
use ReflectionProperty;

abstract class AbstractBuilder
{
    protected static bool $isInit = false;
    protected static Generator $faker;

    public function __construct()
    {
        self::init();
    }

    abstract public function build(): object;

    abstract public static function randomPayload(object $entity): array;

    /**
     * @return class-string
     */
    abstract public static function getEntityClass(): string;

    public static function create(self $builder): object
    {
        $clone = clone $builder;

        $entity = static::newWithoutConstructor(
            static::getEntityClass()
        );

        $hydratePayload = array_merge(static::randomPayload($entity), $clone->serialize());

        return static::hydrate($entity, $hydratePayload);
    }

    protected static function init(): void
    {
        if (!self::$isInit) {
            self::$faker = Factory::create();
            self::$isInit = true;
        }
    }

    public function grab(object $object): static
    {
        $clone = clone $this;

        $grabber = Closure::bind(
            function () {
                return get_object_vars($this);
            },
            $object,
            $object::class
        );

        foreach ($grabber() as $field => $data) {
            $clone->$field = $data;
        }

        return $clone;
    }

    protected function serialize(): array
    {
        $payload = [];
        foreach (get_object_vars($this) as $field => $data) {
            $payload[$field] = $data;
        }

        return $payload;
    }

    /**
     * @throws ReflectionException
     */
    protected static function hydrate(object $entity, array $data): object
    {
        static::init();

        foreach ($data as $property => $datum) {
            static::setProperty($entity, $property, $datum);
        }

        return $entity;
    }

    /**
     * @param class-string $class
     *
     * @throws ReflectionException
     */
    protected static function newWithoutConstructor(string $class): object
    {
        return (new ReflectionClass($class))->newInstanceWithoutConstructor();
    }

    /**
     * @throws ReflectionException
     */
    protected static function getProperty(object $object, string $property): mixed
    {
        $className = $object::class;

        try {
            $refProperty = self::getReflectionProperty($className, $property);

            return $refProperty->getValue($object);
        } catch (ReflectionException $reflectionException) {
            if ($object instanceof ArrayAccess) {
                return $object[$property];
            }
            throw $reflectionException;
        }
    }

    public static function setProperty(object $object, string $property, mixed $value): void
    {
        $className = $object::class;
        try {
            $refProperty = self::getReflectionProperty($className, $property);
            $refProperty->setValue($object, $value);
        } catch (ReflectionException $reflectionException) {
            if ($object instanceof ArrayAccess) {
                $object[$property] = $value;
            } else {
                throw $reflectionException;
            }
        }
    }

    /**
     * @param class-string $className
     *
     * @throws ReflectionException
     */
    private static function getReflectionProperty(string $className, string $property): ReflectionProperty
    {
        $refProperty = new ReflectionProperty($className, $property);
        $refProperty->setAccessible(true);

        return $refProperty;
    }
}
