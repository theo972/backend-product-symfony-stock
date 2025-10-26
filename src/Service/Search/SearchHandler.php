<?php
namespace App\Service\Search;

use App\Dto\SearchQuery;
use App\Service\Search\Provider\SearchProviderRegistry;

final class SearchHandler
{
    public function __construct(private readonly SearchProviderRegistry $registry) {}

    public function handle(SearchQuery $searchQuery): array
    {
        $targets = $searchQuery->support ? [$searchQuery->support] : $this->registry->allKeys();

        $items = [];
        $total = 0;

        foreach ($targets as $key) {
            $provider = $this->registry->get($key);
            if (!$provider) {
                if ($searchQuery->support !== null) {
                    throw new \InvalidArgumentException(sprintf('Unknown support "%s"', $searchQuery->support));
                }
                continue;
            }
            $res = $provider->search($searchQuery);
            foreach ($res['items'] as $it) { $items[] = $it; }
            $total += (int) $res['total'];
        }

        return [
            'items' => $items,
            'total' => $total,
        ];
    }
}
