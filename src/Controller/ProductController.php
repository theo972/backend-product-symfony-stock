<?php

namespace App\Controller;

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
    public function getProduct(
        Product $product
    ): JsonResponse {
        return $this->json(
            ['product' => $product],
            Response::HTTP_OK,
            context: [
                'groups' => ['product:read'],
            ]
        );
    }

    #[Route('', methods: ['POST'])]
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
            context: [
                'groups' => ['product:read'],
            ]
        );
    }

    #[Route('/{id}', methods: ['PUT'])]
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
            context: [
                'groups' => ['product:read'],
            ]
        );
    }

    #[Route('/{id}', methods: ['DELETE'])]
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
            context: [
                'groups' => ['product:read'],
            ]
        );
    }
}
