<?php

declare(strict_types=1);

namespace App\Tests\Functional;

use App\Auth\Domain\User\User;
use App\Auth\Domain\User\ValueObject\UserId;
use App\Auth\Infrastructure\Security\JwtTokenizer;
use App\Auth\Infrastructure\Security\UserIdentity;
use App\Tests\Tools\AssertsTrait;
use App\Tests\Tools\TestFixture;
use DateTimeImmutable;
use Doctrine\ORM\EntityManagerInterface;
use Faker\Factory;
use Faker\Generator;
use JsonException;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\PasswordHasherInterface;

class FunctionalTestCase extends WebTestCase
{
    use AssertsTrait;

    protected static Generator $faker;

    private static KernelBrowser $clientBlank;
    private static User $user;
    private static UserIdentity $userIdentity;
    private static JwtTokenizer $jwtTokenizer;

    protected EntityManagerInterface $entityManager;
    protected KernelBrowser $client;

    public static function setUpBeforeClass(): void
    {
        self::$clientBlank = static::createClient();
        parent::setUpBeforeClass();

        self::$faker = Factory::create();
    }

    protected function setUp(): void
    {
        parent::setUp();

        self::$jwtTokenizer = self::get(JwtTokenizer::class);

        $this->entityManager = self::get(EntityManagerInterface::class);
        $this->entityManager->getConnection()->beginTransaction();
        $this->entityManager->getConnection()->setAutoCommit(false);

        $this->client = clone self::$clientBlank;
        $this->client->disableReboot();

        /** @var PasswordHasherInterface $passwordHasher */
        $passwordHasher = self::get(PasswordHasherInterface::class);
        self::$user = User::create(
            id: new UserId('99999999'),
            createdAt: new DateTimeImmutable(),
            updatedAt: new DateTimeImmutable(),
            email: ((new DateTimeImmutable())->getTimestamp()) . '-admin@dev.com',
            roles: [User::ROLE_ADMIN],
            name: 'admin@dev.com'
        );
        self::$user->changePassword($passwordHasher->hash('12345'));
        self::$userIdentity = new UserIdentity(
            id: self::$user->getId()->getValue(),
            username: self::$user->getUsername(),
            password: self::$user->getPassword(),
            display: self::$user->getUsername(),
            role: self::$user->getRole(),
        );
        $this->entityManager->persist(self::$user);
        $this->entityManager->flush();
        $this->client->loginUser(self::$user);

        foreach (static::withFixtures() as $fixtureClass) {
            /** @var TestFixture $fixture */
            $fixture = self::get($fixtureClass);
            $fixture->load($this->entityManager);
        }
    }

    /**
     * @param array<string> $headers
     * @param array<string> $jwtPayload
     *
     * @throws \Exception
     */
    protected function requestAuthJWT(
        string $method,
        string $url,
        string $body = '',
        array $headers = [],
        array $jwtPayload = []
    ): Response {
        return $this->request(
            $method,
            $url,
            $body,
            array_merge(
                $headers,
                [
                    'CONTENT_TYPE' => 'application/json',
                    'HTTP_AUTHORIZATION' => sprintf(
                        'Bearer %s',
                        self::$jwtTokenizer->generateAccessToken(self::$userIdentity, $jwtPayload)
                    ),
                ]
            )
        );
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
        $this->entityManager->close();
        parent::tearDown();
    }

    /**
     * @throws JsonException
     */
    protected function parseEntityData(?string $content = null): array
    {
        if (null === $content) {
            return [];
        }

        return json_decode($content, true, 512, JSON_THROW_ON_ERROR)['data'];
    }

    /**
     * @throws JsonException
     */
    protected function parseEntitiesData(?string $content = null): array
    {
        if (null === $content) {
            return [];
        }

        return json_decode($content, true, 512, JSON_THROW_ON_ERROR)['data']['data'];
    }

    /**
     * @param array<string> $headers
     */
    protected function request(string $method, string $url, string $body = '', array $headers = []): Response
    {
        $this->client->request($method, $url, [], [], $headers, $body);

        return $this->client->getResponse();
    }
}
