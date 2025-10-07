<?php

namespace App\Controller;

use App\Entity\User;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Serializer\SerializerInterface;

/**
 * @method User getUser()
 */
abstract class AbstractApiController extends AbstractController
{
    public function __construct(public SerializerInterface $serializer)
    {
    }

    protected function json($data, int $status = 200, array $context = [], array $headers = []): JsonResponse
    {
        $context['circular_reference_handler'] = function ($object) {
            return is_string($object) ? $object : $object->getId();
        };
        $json = $this->serializer->serialize($data, 'json', $context);
        return new JsonResponse(
            $json,
            $status,
            $headers,
            true
        );
    }

    public function jsonNotFound(string $message = 'Not found'): JsonResponse
    {
        return $this->json([
            'result' => false,
            'errors' => [
                $message
            ]
        ], Response::HTTP_NOT_FOUND);
    }
}
