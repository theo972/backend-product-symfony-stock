<?php


namespace App\Controller;

use App\Entity\Order;
use App\Repository\OrderRepository;
use App\Service\OrderService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Serializer\Normalizer\AbstractNormalizer;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;

#[Route('/api/orders')]
class OrderController extends AbstractController
{
    #[Route('', methods: ['GET'])]
    public function list(
        OrderRepository $orderRepository,
    ): JsonResponse
    {
        $orders = $orderRepository->findAll();
        return $this->json(
            ['orders' => $orders],
            Response::HTTP_OK,
            context: [
                'groups' => ['order:read']
            ]
        );
    }

    #[Route('/{id}', methods: ['GET'])]
    public function getOrder(
        string $id,
        OrderRepository $orderRepository,
        SerializerInterface $serializer
    ): JsonResponse
    {
        $order = $orderRepository->find($id);
        if ($order === null) {
            return $this->json([
                'result' => false,
                'errors' => ['Not Found']
            ], Response::HTTP_NOT_FOUND);
        }
        return $this->json(
            ['order' => $order],
            Response::HTTP_OK,
            context: [
                'groups' => ['order:read']
            ]
        );
    }

    #[Route('', methods: ['POST'])]
    public function create(
        Request $request,
        ValidatorInterface $validator,
        SerializerInterface $serializer,
        OrderService $orderService
    ): JsonResponse
    {
        $result = false;
        $data = $request->getContent();
        /** @var Order $order */
        $order = $serializer->deserialize(
            $data,
            Order::class,
            'json',
            ['groups' => ['order:create']]
        );

        $errors = $validator->validate($order, null, ['order:create']);
        if (count($errors) === 0) {
            $result = $orderService->create($order);
        }

        return $this->json(
            [
                'result' => $result,
                'errors' => $errors,
                'order' => $order
            ],
            $result ? Response::HTTP_CREATED : Response::HTTP_BAD_REQUEST,
            context: [
                'groups' => ['order:read']
            ]
        );
    }

    #[Route('/{id}', methods: ['PUT', 'PATCH'])]
    public function update(
        string $id,
        Request $request,
        OrderRepository $orderRepository,
        ValidatorInterface $validator,
        SerializerInterface $serializer,
        OrderService $orderService
    ): JsonResponse
    {
        $result = false;
        $order = $orderRepository->find($id);
        if ($order === null) {
            return $this->json([
                'result' => false,
                'errors' => ['Not Found']
            ], Response::HTTP_NOT_FOUND);
        }

        $data = $request->getContent();
        /** @var Order $order */
        $order = $serializer->deserialize(
            $data,
            Order::class,
            'json',
            [
                'groups' => ['order:update'],
                AbstractNormalizer::OBJECT_TO_POPULATE => $order
            ]
        );

        $errors = $validator->validate($order, null, ['order:update']);
        if (count($errors) === 0) {
            $result = $orderService->update($order);
        }

        return $this->json(
            [
                'result' => $result,
                'errors' => $errors,
                'order' => $order
            ],
            $result ? Response::HTTP_OK : Response::HTTP_BAD_REQUEST,
            context: [
                'groups' => ['order:read']
            ]
        );
    }

    #[Route('/{id}', methods: ['DELETE'])]
    public function delete(
        string $id,
        OrderRepository $orderRepository,
        OrderService $orderService
    ): JsonResponse
    {
        $order = $orderRepository->find($id);
        if ($order === null) {
            return $this->json([
                'result' => false,
                'errors' => ['Not Found']
            ], Response::HTTP_NOT_FOUND);
        }

        $result = $orderService->delete($order);

        return $this->json(
            [
                'result' => $result,
                'order' => $order
            ],
            $result ? Response::HTTP_OK : Response::HTTP_BAD_REQUEST,
            context: [
                'groups' => ['order:read']
            ]
        );
    }
}
