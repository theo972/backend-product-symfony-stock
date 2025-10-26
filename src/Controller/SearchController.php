<?php
namespace App\Controller;

use App\Dto\SearchQuery;
use App\Service\Search\SearchHandler;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\SerializerInterface;

#[Route('/api/search')]
class SearchController extends AbstractApiController
{
    #[Route(path: '', name: 'search', methods: ['POST'])]
    public function search(
        Request $request,
        SearchHandler $handler,
        SerializerInterface $serializer,
    ): JsonResponse
    {
        $searchQuery = $serializer->deserialize(
            $request->getContent(),
            SearchQuery::class,
            'json',
        );
        $result = $handler->handle($searchQuery);
        return $this->json(
            [$result],
            Response::HTTP_OK,
            ['groups' => ['search:read', 'product:read', 'saleOrder:read']]
        );
    }

}
