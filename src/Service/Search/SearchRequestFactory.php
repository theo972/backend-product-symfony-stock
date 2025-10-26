<?php
namespace App\Service\Search;

use App\Dto\SearchQuery;
use Symfony\Component\HttpFoundation\Request;

final class SearchRequestFactory
{
    public function fromRequest(Request $request): SearchQuery
    {
        $support = $request->query->get('support') ?: null;
        $query = trim((string) $request->query->get('q', '')) ?: null;
        $page = max(1, (int) $request->query->get('page', 1));
        $perPage = max(1, min(100, (int) $request->query->get('perPage', 20)));
        $sort = $request->query->get('sort') ?: null;
        $order = strtolower((string) $request->query->get('order', 'asc')) === 'desc' ? 'desc' : 'asc';
        $filters = $request->query->all('filters');

        return new SearchQuery($support, $query, $page, $perPage, $sort, $order, $filters);
    }
}
