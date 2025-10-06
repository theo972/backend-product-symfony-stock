<?php
namespace App\Service;

use App\Entity\Orders;
use App\Event\OrderCreatedEvent;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Contracts\EventDispatcher\EventDispatcherInterface;

class OrderService
{
    public function __construct(
        private readonly EntityManagerInterface $em,
        private readonly EventDispatcherInterface $dispatcher
    ) {
    }

    public function create(Orders $order): bool
    {
        $this->em->persist($order);
        $this->em->flush();
        $this->dispatcher->dispatch(new OrderCreatedEvent($order));
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
