<?php
namespace App\Controller;

use App\Service\Search\SearchHandler;
use App\Service\Search\SearchRequestFactory;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/api/search')]
class SearchController extends AbstractApiController
{
    #[Route(path: '', name: 'search', methods: ['GET'])]
    public function search(
        Request $request,
        SearchRequestFactory $factory,
        SearchHandler $handler
    ): JsonResponse
    {
        $query = $factory->fromRequest($request);
        $result = $handler->handle($query);
        return $this->json(
            [$result],
            Response::HTTP_OK,
            ['groups' => ['search:read', 'product:read', 'saleOrder:read']]
        );
    }

}
