<?php

namespace App\Controller;

use App\Dto\ValidationErrorResponse;
use App\Entity\SaleOrder;
use Nelmio\ApiDocBundle\Attribute\Model;
use OpenApi\Attributes as OA;
use App\Repository\SaleOrderRepository;
use App\Service\SaleOrderService;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Runtime\Internal\SymfonyErrorHandler;
use Symfony\Component\Serializer\Normalizer\AbstractNormalizer;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;

#[Route('/api/saleOrder')]
class SaleOrderController extends AbstractApiController
{
    #[Route('/search', methods: ['GET'])]
    #[OA\Tag(name: 'SaleOrder')]
    #[OA\Get(
        summary: 'List sale orders (paginated)',
        security: [['Bearer' => []]],
        parameters: [
            new OA\Parameter(
                name: 'page', in: 'query', required: false,
                schema: new OA\Schema(type: 'integer', minimum: 1), example: 1
            ),
            new OA\Parameter(
                name: 'perPage', in: 'query', required: false,
                schema: new OA\Schema(type: 'integer', maximum: 100, minimum: 1), example: 10
            ),
        ],
        responses: [
            new OA\Response(
                response: 200,
                description: 'Paginated list of sale orders',
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: 'total', type: 'integer', example: 123),
                        new OA\Property(
                            property: 'data',
                            type: 'array',
                            items: new OA\Items(ref: new Model(type: SaleOrder::class, groups: ['saleOrder:read']))
                        ),
                    ],
                    type: 'object'
                )
            ),
        ]
    )]
    public function list(
        Request $request,
        SaleOrderRepository $orderRepository,
    ): JsonResponse {
        $page = $request->query->getInt('page', 1);
        $size = $request->query->getInt('perPage', 10);

        $data = $orderRepository->search($page, $size);
        return $this->json(
            ['total' => $data->getTotalItemCount(), 'data' => $data],
            Response::HTTP_OK,
            ['groups' => ['saleOrder:read', 'product:read', 'user:read']]
        );
    }

    #[Route('/{id}', methods: ['GET'])]
    #[OA\Tag(name: 'SaleOrder')]
    #[OA\Get(
        summary: 'Get a sale order by id',
        security: [['Bearer' => []]],
        parameters: [
            new OA\Parameter(name: 'id', in: 'path', required: true, schema: new OA\Schema(type: 'string'))
        ],
        responses: [
            new OA\Response(
                response: 200,
                description: 'Sale order details',
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(
                            property: 'saleOrder',
                            ref: new Model(type: SaleOrder::class, groups: ['saleOrder:read'])
                        ),
                    ],
                    type: 'object'
                )
            ),
            new OA\Response(response: 404, description: 'Not found'),
        ]
    )]
    public function getOrder(
        SaleOrder $saleOrder,
    ): JsonResponse {
        return $this->json(
            ['saleOrder' => $saleOrder],
            Response::HTTP_OK,
            [
                'groups' => ['saleOrder:read'],
            ]
        );
    }

    #[Route('', methods: ['POST'])]
    #[OA\Tag(name: 'SaleOrder')]
    #[OA\Post(
        summary: 'Create a sale order',
        security: [['Bearer' => []]],
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(ref: new Model(type: SaleOrder::class, groups: ['saleOrder:create']))
        ),
        responses: [
            new OA\Response(
                response: 201,
                description: 'Sale order created',
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: 'result', type: 'boolean', example: true),
                        new OA\Property(property: 'errors', type: 'array', items: new OA\Items(ref: new Model(type: ValidationErrorResponse::class))),
                        new OA\Property(property: 'saleOrder', ref: new Model(type: SaleOrder::class, groups: ['saleOrder:read'])),
                    ],
                    type: 'object'
                )
            ),
        ]
    )]
    public function create(
        Request $request,
        ValidatorInterface $validator,
        SerializerInterface $serializer,
        SaleOrderService $orderService,
    ): JsonResponse {
        $result = false;
        $data = $request->getContent();
        /** @var SaleOrder $saleOrder */
        $saleOrder = $serializer->deserialize(
            $data,
            SaleOrder::class,
            'json',
            ['groups' => ['saleOrder:create']]
        );

        $errors = $validator->validate($saleOrder, null, ['saleOrder:create']);
        if (0 === count($errors)) {
            $result = $orderService->create($saleOrder, $this->getUser());
        }

        return $this->json(
            [
                'result' => $result,
                'errors' => $errors,
                'saleOrder' => $saleOrder,
            ],
            $result ? Response::HTTP_CREATED : Response::HTTP_BAD_REQUEST,
            [
                'groups' => ['saleOrder:read'],
            ]
        );
    }

    #[Route('/{id}', methods: ['PUT'])]
    #[OA\Tag(name: 'SaleOrder')]
    #[OA\Put(
        summary: 'Update a sale order',
        security: [['Bearer' => []]],
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(ref: new Model(type: SaleOrder::class, groups: ['saleOrder:update']))
        ),
        parameters: [
            new OA\Parameter(name: 'id', in: 'path', required: true, schema: new OA\Schema(type: 'string'))
        ],
        responses: [
            new OA\Response(
                response: 200,
                description: 'Sale Order updated',
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: 'result', type: 'boolean', example: true),
                        new OA\Property(property: 'errors', type: 'array', items: new OA\Items(ref: new Model(type: ValidationErrorResponse::class))),
                        new OA\Property(property: 'saleOrder', ref: new Model(type: SaleOrder::class, groups: ['saleOrder:read'])),
                    ],
                    type: 'object'
                )
            ),
            new OA\Response(response: 404, description: 'Not found'),
        ]
    )]
    public function update(
        string $id,
        Request $request,
        SaleOrderRepository $orderRepository,
        ValidatorInterface $validator,
        SerializerInterface $serializer,
        SaleOrderService $orderService,
    ): JsonResponse {
        $result = false;

        $saleOrder = $orderRepository->find($id);

        if (null === $saleOrder) {
            return $this->jsonNotFound();
        }

        $data = $request->getContent();
        /** @var SaleOrder $saleOrder */
        $saleOrder = $serializer->deserialize(
            $data,
            SaleOrder::class,
            'json',
            [
                'groups' => ['saleOrder:update'],
                AbstractNormalizer::OBJECT_TO_POPULATE => $saleOrder,
            ]
        );

        $errors = $validator->validate($saleOrder, null, ['saleOrder:update']);
        if (0 === count($errors)) {
            $result = $orderService->update($saleOrder, $this->getUser());
        }

        return $this->json(
            [
                'result' => $result,
                'errors' => $errors,
                'saleOrder' => $saleOrder,
            ],
            $result ? Response::HTTP_OK : Response::HTTP_BAD_REQUEST,
            [
                'groups' => ['saleOrder:read'],
            ]
        );
    }

    #[Route('/{id}', methods: ['DELETE'])]
    #[OA\Tag(name: 'SaleOrder')]
    #[OA\Delete(
        summary: 'Delete a Sale Order',
        security: [['Bearer' => []]],
        parameters: [
            new OA\Parameter(name: 'id', in: 'path', required: true, schema: new OA\Schema(type: 'string'))
        ],
        responses: [
            new OA\Response(
                response: 200,
                description: 'Deletion result',
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: 'result', type: 'boolean', example: true),
                        new OA\Property(property: 'saleOrder', ref: new Model(type: SaleOrder::class, groups: ['saleOrder:read'])),
                    ],
                    type: 'object'
                )
            ),
            new OA\Response(response: 404, description: 'Not found'),
        ]
    )]
    public function delete(
        string $id ,
        SaleOrderRepository $orderRepository,
        SaleOrderService $orderService,
    ): JsonResponse {
        $saleOrder = $orderRepository->find($id);
        if (null === $saleOrder) {
            return $this->jsonNotFound();
        }

        $result = $orderService->delete($saleOrder);

        return $this->json(
            [
                'result' => $result,
                'saleOrder' => $saleOrder,
            ],
            $result ? Response::HTTP_OK : Response::HTTP_BAD_REQUEST,
            [
                'groups' => ['saleOrder:read'],
            ]
        );
    }
}
