<?php
namespace App\Service;

use App\Entity\Order;
use Doctrine\ORM\EntityManagerInterface;

class OrderService
{
    public function __construct(
        private readonly EntityManagerInterface $em,
    ) {
    }

    public function create(Order $order): bool
    {
        $this->em->persist($order);
        $this->em->flush();

        return true;
    }

    public function update(Order $order): bool
    {
        $this->em->flush();
        return true;
    }

    public function delete(Order $order): bool
    {
        $this->em->remove($order);
        $this->em->flush();

        return true;
    }

}
