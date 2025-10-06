<?php

namespace App\Controller;

use App\Entity\User;
use App\Service\UserService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;

final class AuthController extends AbstractController
{
    #[Route('/auth/register', methods: ['POST'])]
    public function register(
        Request $request,
        SerializerInterface $serializer,
        ValidatorInterface $validator,
        UserService $userService,
    ): JsonResponse {
        $result = false;
        /** @var User $user */
        $user = $serializer->deserialize(
            $request->getContent(),
            User::class,
            'json',
            ['groups' => ['register:create']]
        );

        $user->setPasswordPlain($user->getPassword());
        $errors = $validator->validate($user, null, ['register:create']);
        if (count($errors) === 0) {
            $result = $userService->register($user);
        }

        return $this->json(
            [
                'result' => $result,
                'errors' => $errors,
                'user' => $user
            ],
            $result ? Response::HTTP_CREATED : Response::HTTP_BAD_REQUEST
        );
    }
}
