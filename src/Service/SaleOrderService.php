<?php

namespace App\Service;

use App\Entity\SaleOrder;
use App\Event\OrderCreatedEvent;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Contracts\EventDispatcher\EventDispatcherInterface;

class SaleOrderService
{
    public function __construct(
        private readonly EntityManagerInterface $em,
        private readonly EventDispatcherInterface $dispatcher,
    ) {
    }

    public function create(SaleOrder $saleOrder): bool
    {
        $this->em->persist($saleOrder);
        $this->em->flush();
        $this->dispatcher->dispatch(new OrderCreatedEvent($saleOrder));

        return true;
    }

    public function update(SaleOrder $saleOrder): bool
    {
        $this->em->flush();

        return true;
    }

    public function delete(SaleOrder $saleOrder): bool
    {
        $this->em->remove($saleOrder);
        $this->em->flush();

        return true;
    }
}
