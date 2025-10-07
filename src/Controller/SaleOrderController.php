<?php

namespace App\Controller;

use App\Entity\SaleOrder;
use App\Repository\SaleOrderRepository;
use App\Service\SaleOrderService;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Serializer\Normalizer\AbstractNormalizer;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;

#[Route('/api/saleOrder')]
class SaleOrderController extends ApiBaseController
{
    #[Route('', methods: ['GET'])]
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
            ['groups' => ['saleOrder:read']]
        );
    }

    #[Route('/{id}', methods: ['GET'])]
    public function getOrder(
        SaleOrder $saleOrder,
    ): JsonResponse {
        return $this->json(
            ['saleOrder' => $saleOrder],
            Response::HTTP_OK,
            context: [
                'groups' => ['saleOrder:read'],
            ]
        );
    }

    #[Route('', methods: ['POST'])]
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
            $result = $orderService->create($saleOrder);
        }

        return $this->json(
            [
                'result' => $result,
                'errors' => $errors,
                'saleOrder' => $saleOrder,
            ],
            $result ? Response::HTTP_CREATED : Response::HTTP_BAD_REQUEST,
            context: [
                'groups' => ['saleOrder:read'],
            ]
        );
    }

    #[Route('/{id}', methods: ['PUT', 'PATCH'])]
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
            $result = $orderService->update($saleOrder);
        }

        return $this->json(
            [
                'result' => $result,
                'errors' => $errors,
                'saleOrder' => $saleOrder,
            ],
            $result ? Response::HTTP_OK : Response::HTTP_BAD_REQUEST,
            context: [
                'groups' => ['saleOrder:read'],
            ]
        );
    }

    #[Route('/{id}', methods: ['DELETE'])]
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
            context: [
                'groups' => ['saleOrder:read'],
            ]
        );
    }
}
