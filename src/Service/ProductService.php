<?php
namespace App\Service;

use App\Entity\Product;
use Doctrine\ORM\EntityManagerInterface;

class ProductService
{
    public function __construct(
        private readonly EntityManagerInterface $em,
    ) {
    }

    public function create(Product $product): bool
    {
        $this->em->persist($product);
        $this->em->flush();

        return true;
    }

    public function update(Product $product): bool
    {
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
