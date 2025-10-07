<?php

namespace App\Controller;

use DateTime;
use DateTimeInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;
use Symfony\Component\Serializer\Normalizer\AbstractNormalizer;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Contracts\Translation\TranslatorInterface;

abstract class ApiBaseController extends AbstractController
{
    protected array $errors = [];

    public function __construct(public SerializerInterface $serializer)
    {
    }

    /**
     * @param array $data
     * @param int $status
     * @param array $headers
     * @param array $context
     * @return JsonResponse
     */
    protected function json($data, int $status = 200, array $context = [], array $headers = []): JsonResponse
    {
        $context['circular_reference_handler'] = function ($object) {
            return is_string($object) ? $object : $object->getId();
        };
        $context['json_encode_options'] = JsonResponse::DEFAULT_ENCODING_OPTIONS;
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
