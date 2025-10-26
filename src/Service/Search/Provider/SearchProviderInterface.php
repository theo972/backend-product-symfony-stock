<?php
namespace App\Service\Search\Provider;

use App\Dto\SearchQuery;

interface SearchProviderInterface
{
    public function support(): string;
    public function search(SearchQuery $searchQuery): array;
}
