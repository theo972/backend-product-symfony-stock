<?php
namespace App\Service;

use App\Entity\Orders;
use Doctrine\ORM\EntityManagerInterface;

class OrderService
{
    public function __construct(
        private readonly EntityManagerInterface $em,
    ) {
    }

    public function create(Orders $order): bool
    {
        $this->em->persist($order);
        $this->em->flush();

        return true;
    }

    public function update(Orders $order): bool
    {
        $this->em->flush();
        return true;
    }

    public function delete(Orders $order): bool
    {
        $this->em->remove($order);
        $this->em->flush();

        return true;
    }

}
