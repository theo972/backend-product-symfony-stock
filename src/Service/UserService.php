<?php
namespace App\Service;

use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class UserService
{
    public function __construct(
        private readonly EntityManagerInterface $em,
        private readonly UserPasswordHasherInterface $passwordHasher,
    ) {
    }
    public function register(User $user): bool
    {
        if (null !== $user->getPasswordPlain()) {
            $encodedPassword = $this->passwordHasher->hashPassword($user, $user->getPasswordPlain());
            $user->setPassword($encodedPassword);
        }
        $user->eraseCredentials();
        $user->setRoles(['ROLE_USER']);

        $this->em->persist($user);
        $this->em->flush();

        return true;
    }

}
