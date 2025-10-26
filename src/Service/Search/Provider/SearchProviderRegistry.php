<?php
namespace App\Service\Search\Provider;

final class SearchProviderRegistry
{
    /** @var array<string, SearchProviderInterface> */
    private array $providers = [];

    /**
     * @param iterable<SearchProviderInterface> $providers
     */
    public function __construct(iterable $providers)
    {
        foreach ($providers as $provider) {
            $key = $provider->support();
            if (isset($this->providers[$key])) {
                throw new \LogicException(sprintf('Duplicate search provider key "%s"', $key));
            }
            $this->providers[$key] = $provider;
        }
    }

    public function get(string $key): ?SearchProviderInterface
    {
        return $this->providers[$key] ?? null;
    }

    /** @return SearchProviderInterface[] */
    public function all(): array
    {
        return array_values($this->providers);
    }
}
