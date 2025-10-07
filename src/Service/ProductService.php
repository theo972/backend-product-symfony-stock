<?php

namespace App\Service;

use App\Entity\Product;
use App\Entity\User;
use DateTime;
use Doctrine\ORM\EntityManagerInterface;

class ProductService
{
    public function __construct(
        private readonly EntityManagerInterface $em,
    ) {
    }

    public function create(Product $product, User $user): bool
    {
        $product->setCreatedAt(new DateTime());
        $product->setUpdatedAt(new DateTime());
        $product->setCreatedBy($user);
        $product->setUpdatedBy($user);
        $this->em->persist($product);
        $this->em->flush();

        return true;
    }

    public function update(Product $product, User $user): bool
    {
        $product->setUpdatedAt(new DateTime());
        $product->setUpdatedBy($user);
        $this->em->flush();
        return true;
    }

    public function delete(Product $product): bool
    {
        $this->em->remove($product);
        $this->em->flush();

        return true;
    }
}
