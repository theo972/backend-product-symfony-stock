<?php
namespace App\Service\Search\Provider;

use App\Dto\SearchQuery;

interface SearchProviderInterface
{
    public function search(SearchQuery $searchQuery): array;
}
