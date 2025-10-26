<?php
namespace App\Service\Search;

use App\Dto\SearchQuery;
use App\Service\Search\Provider\SearchProviderRegistry;

final class SearchHandler
{
    public function __construct(private readonly SearchProviderRegistry $registry) {}

    public function handle(SearchQuery $searchQuery): array
    {
        $provider = $this->registry->get($searchQuery->support);
        if (!$provider && $searchQuery->support !== null) {
            throw new \InvalidArgumentException(sprintf('Unknown support "%s"', $searchQuery->support));
        }
        return $provider->search($searchQuery);
    }
}
