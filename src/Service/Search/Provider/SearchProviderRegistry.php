<?php
namespace App\Service\Search\Provider;

use Psr\Container\ContainerInterface;
use Symfony\Component\DependencyInjection\Attribute\AutowireLocator;

final readonly class SearchProviderRegistry
{
    public function __construct(
        #[AutowireLocator('app.search_provider', indexAttribute: 'target')]
        private ContainerInterface $locator
    ) {}

    public function get(string $key): ?SearchProviderInterface
    {
        return $this->locator->has($key) ? $this->locator->get($key) : null;
    }
}
