<?php

namespace App\Controller;

use App\Dto\ValidationErrorResponse;
use Nelmio\ApiDocBundle\Attribute\Model;
use OpenApi\Attributes as OA;
use App\Entity\Product;
use App\Entity\UserTrait;
use App\Repository\ProductRepository;
use App\Service\ProductService;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Serializer\Normalizer\AbstractNormalizer;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;

#[Route('/api/products')]
class ProductController extends AbstractApiController
{
    use UserTrait;

    #[Route('', methods: ['GET'])]
    #[OA\Tag(name: 'Products')]
    #[OA\Get(
        summary: 'List products (paginated)',
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
                description: 'Paginated list of products',
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: 'total', type: 'integer', example: 123),
                        new OA\Property(
                            property: 'data',
                            type: 'array',
                            items: new OA\Items(ref: new Model(type: Product::class, groups: ['product:read']))
                        ),
                    ],
                    type: 'object'
                )
            ),
        ]
    )]
    public function list(
        Request $request,
        ProductRepository $productRepository,
    ): JsonResponse {
        $page = $request->query->getInt('page', 1);
        $size = $request->query->getInt('perPage', 10);

        $data = $productRepository->search($page, $size);
        return $this->json(
            ['total' => $data->getTotalItemCount(), 'data' => $data],
            Response::HTTP_OK,
            ['groups' => ['product:read']]
        );
    }


    #[Route('/{id}', methods: ['GET'])]
    #[OA\Tag(name: 'Products')]
    #[OA\Get(
        summary: 'Get a product by id',
        security: [['Bearer' => []]],
        parameters: [
            new OA\Parameter(name: 'id', in: 'path', required: true, schema: new OA\Schema(type: 'string'))
        ],
        responses: [
            new OA\Response(
                response: 200,
                description: 'Product details',
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(
                            property: 'product',
                            ref: new Model(type: Product::class, groups: ['product:read'])
                        ),
                    ],
                    type: 'object'
                )
            ),
            new OA\Response(response: 404, description: 'Not found'),
        ]
    )]
    public function getProduct(
        Product $product
    ): JsonResponse {
        return $this->json(
            ['product' => $product],
            Response::HTTP_OK,
            [
                'groups' => ['product:read'],
            ]
        );
    }

    #[Route('', methods: ['POST'])]
    #[OA\Tag(name: 'Products')]
    #[OA\Post(
        summary: 'Create a product',
        security: [['Bearer' => []]],
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(ref: new Model(type: Product::class, groups: ['product:create']))
        ),
        responses: [
            new OA\Response(
                response: 201,
                description: 'Product created',
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: 'result', type: 'boolean', example: true),
                        new OA\Property(property: 'errors', type: 'array', items: new OA\Items(ref: new Model(type: ValidationErrorResponse::class))),
                        new OA\Property(property: 'product', ref: new Model(type: Product::class, groups: ['product:read'])),
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
        ProductService $productService,
    ): JsonResponse {
        $result = false;
        /** @var Product $product */
        $product = $serializer->deserialize(
            $request->getContent(),
            Product::class,
            'json',
            ['groups' => ['product:create']]
        );

        $errors = $validator->validate($product, null, ['register:create']);
        if (0 === count($errors)) {
            $result = $productService->create($product, $this->getUser());
        }

        return $this->json(
            [
                'result' => $result,
                'errors' => $errors,
                'product' => $product,
            ],
            $result ? Response::HTTP_CREATED : Response::HTTP_BAD_REQUEST,
            [
                'groups' => ['product:read'],
            ]
        );
    }

    #[Route('/{id}', methods: ['PUT'])]
    #[OA\Tag(name: 'Products')]
    #[OA\Put(
        summary: 'Update a product',
        security: [['Bearer' => []]],
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(ref: new Model(type: Product::class, groups: ['product:update']))
        ),
        parameters: [
            new OA\Parameter(name: 'id', in: 'path', required: true, schema: new OA\Schema(type: 'string'))
        ],
        responses: [
            new OA\Response(
                response: 200,
                description: 'Product updated',
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: 'result', type: 'boolean', example: true),
                        new OA\Property(property: 'errors', type: 'array', items: new OA\Items(ref: new Model(type: ValidationErrorResponse::class))),
                        new OA\Property(property: 'product', ref: new Model(type: Product::class, groups: ['product:read'])),
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
        ValidatorInterface $validator,
        SerializerInterface $serializer,
        ProductRepository $productRepository,
        ProductService $productService,
    ): JsonResponse {
        $result = false;
        $product = $productRepository->find($id);
        if (null === $product) {
            return $this->jsonNotFound();
        }
        $content = (string) $request->getContent();
        /** @var Product $product */
        $product = $serializer->deserialize($content, Product::class, 'json', [
            'groups' => ['product:update'],
            AbstractNormalizer::OBJECT_TO_POPULATE => $product,
        ]);

        $errors = $validator->validate($product, null, ['product:update']);
        if (0 === count($errors)) {
            $result = $productService->update($product, $this->getUser());
        }

        return $this->json(
            [
                'result' => $result,
                'errors' => $errors,
                'product' => $product,
            ],
            $result ? Response::HTTP_OK : Response::HTTP_BAD_REQUEST,
            [
                'groups' => ['product:read'],
            ]
        );
    }

    #[Route('/{id}', methods: ['DELETE'])]
    #[OA\Tag(name: 'Products')]
    #[OA\Delete(
        summary: 'Delete a product',
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
                        new OA\Property(property: 'product', ref: new Model(type: Product::class, groups: ['product:read'])),
                    ],
                    type: 'object'
                )
            ),
            new OA\Response(response: 404, description: 'Not found'),
        ]
    )]
    public function delete(
        string $id,
        ProductRepository $productRepository,
        ProductService $productService,
    ): JsonResponse {
        $result = false;
        $product = $productRepository->find($id);
        if (null === $product) {
            return $this->jsonNotFound();
        }
        $result = $productService->delete($product);

        return $this->json(
            [
                'result' => $result,
                'product' => $product,
            ],
            $result ? Response::HTTP_OK : Response::HTTP_BAD_REQUEST,
            [
                'groups' => ['product:read'],
            ]
        );
    }
}
