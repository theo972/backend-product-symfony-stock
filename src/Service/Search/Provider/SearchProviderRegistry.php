<?php
namespace App\Service\Search\Provider;

final class SearchProviderRegistry
{
    /** @var array<string, SearchProviderInterface> */
    private array $byKey = [];

    /**
     * @param iterable<SearchProviderInterface> $providers
     */
    public function __construct(iterable $providers)
    {
        foreach ($providers as $p) {
            $key = $p->support();
            if (isset($this->byKey[$key])) {
                throw new \LogicException(sprintf('Duplicate search provider key "%s"', $key));
            }
            $this->byKey[$key] = $p;
        }
    }

    /** @return string[] */
    public function allKeys(): array
    {
        return array_keys($this->byKey);
    }

    public function get(string $key): ?SearchProviderInterface
    {
        return $this->byKey[$key] ?? null;
    }

    /** @return SearchProviderInterface[] */
    public function all(): array
    {
        return array_values($this->byKey);
    }
}
