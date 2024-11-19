<?php

declare(strict_types=1);

namespace App\Tests\Integration;

use App\Tests\Tools\AssertsTrait;
use App\Tests\Tools\TestFixture;
use ArrayAccess;
use Doctrine\ORM\EntityManagerInterface;
use Faker\Factory;
use Faker\Generator;
use ReflectionException;
use ReflectionProperty;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class IntegrationTestCase extends KernelTestCase
{
    use AssertsTrait;

    protected EntityManagerInterface $entityManager;
    protected static Generator $faker;

    public static function setUpBeforeClass(): void
    {
        parent::setUpBeforeClass();
        static::bootKernel();

        self::$faker = Factory::create();
    }

    protected function setUp(): void
    {
        parent::setUp();

        $this->entityManager = self::get(EntityManagerInterface::class);
        $this->entityManager->getConnection()->beginTransaction();
        $this->entityManager->getConnection()->setAutoCommit(false);

        foreach (static::withFixtures() as $fixtureClass) {
            /** @var TestFixture $fixture */
            $fixture = self::get($fixtureClass);
            $fixture->load($this->entityManager);
        }
    }

    /**
     * @template T
     *
     * @param class-string<T> $id
     *
     * @return T
     * @throws \Exception
     */
    public static function get(string $id)
    {
        /** @var T $instance */
        $instance = parent::getContainer()->get($id);

        return $instance;
    }

    /**
     * @return class-string<TestFixture>[]
     */
    protected static function withFixtures(): array
    {
        return [];
    }

    protected function tearDown(): void
    {
        $this->entityManager->getConnection()->rollback();
        parent::tearDown();
    }

    protected static function bindMock(object $object, string $property, mixed $value): void
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
     * @throws ReflectionException
     */
    private static function getReflectionProperty(string $className, string $property): ReflectionProperty
    {
        $refProperty = new ReflectionProperty($className, $property);
        $refProperty->setAccessible(true);

        return $refProperty;
    }
}
