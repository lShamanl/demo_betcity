<?php

declare(strict_types=1);

namespace App;

use App\Auth\Infrastructure\CompillerPass\RateLimitingPass;
use Symfony\Bundle\FrameworkBundle\Kernel\MicroKernelTrait;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;
use Symfony\Component\HttpKernel\Kernel as BaseKernel;
use Symfony\Component\Routing\Loader\Configurator\RoutingConfigurator;

/** @codeCoverageIgnore */
class Kernel extends BaseKernel
{
    use MicroKernelTrait;

    public ?array $paths = null;

    /**
     * @return string[]
     */
    private function getConfigPaths(): array
    {
        if (null === $this->paths) {
            $this->paths = [];
            /** @var false|string[] $phpFiles */
            $phpFiles = glob(__DIR__ . '/.modules/*.php');
            if (false === $phpFiles) {
                return [];
            }

            foreach ($phpFiles as $file) {
                foreach (require_once $file as $path) {
                    $this->paths[] = $path;
                }
            }
        }

        return $this->paths;
    }

    public function __construct(string $environment, bool $debug)
    {
        # You can set need timezone here:
        date_default_timezone_set('Europe/Moscow');

        parent::__construct($environment, $debug);
    }

    protected function configureContainer(ContainerConfigurator $container): void
    {
        $this->configureContainerForPath($container, __DIR__ . '/../config');
        foreach ($this->getConfigPaths() as $path) {
            $this->configureContainerForPath($container, __DIR__ . '/' . $path);
        }
    }

    protected function configureRoutes(RoutingConfigurator $routes): void
    {
        $this->configureRoutesForPath($routes, __DIR__ . '/../config');
        foreach ($this->getConfigPaths() as $path) {
            $this->configureRoutesForPath($routes, __DIR__ . '/' . $path);
        }
    }

    public function configureContainerForPath(ContainerConfigurator $container, string $pathToConfig): void
    {
        $container->import($pathToConfig . '/{packages}/*.yaml');
        $container->import($pathToConfig . '/{packages}/' . $this->environment . '/*.yaml');

        if (is_file($pathToConfig . '/services.yaml')) {
            $container->import($pathToConfig . '/services.yaml');
            $container->import($pathToConfig . '/{services}_' . $this->environment . '.yaml');

            $container->import($pathToConfig . '/{services}/*.yaml');
            $container->import($pathToConfig . '/{services}/' . $this->environment . '/*.yaml');
        } elseif (is_file($path = $pathToConfig . '/services.php')) {
            (require $path)($container->withPath($path), $this);
        }
    }

    public function configureRoutesForPath(RoutingConfigurator $routes, string $pathToConfig): void
    {
        $routes->import($pathToConfig . '/{routes}/' . $this->environment . '/*.yaml');
        $routes->import($pathToConfig . '/{routes}/**/*.yaml');

        if (is_file($pathToConfig . '/routes.yaml')) {
            $routes->import($pathToConfig . '/routes.yaml');
        } elseif (is_file($path = $pathToConfig . '/routes.php')) {
            (require $path)($routes->withPath($path), $this);
        }
    }

    protected function build(ContainerBuilder $container): void
    {
        $container->addCompilerPass(new RateLimitingPass());
    }
}
