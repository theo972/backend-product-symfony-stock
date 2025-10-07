<?php

namespace App\Service;

use App\Entity\SaleOrder;
use App\Entity\User;
use App\Event\OrderCreatedEvent;
use DateTime;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Contracts\EventDispatcher\EventDispatcherInterface;

class SaleOrderService
{
    public function __construct(
        private readonly EntityManagerInterface $em,
        private readonly EventDispatcherInterface $dispatcher,
    ) {
    }

    public function create(SaleOrder $saleOrder, User $user): bool
    {
        $saleOrder->setCreatedAt(new DateTime());
        $saleOrder->setUpdatedAt(new DateTime());
        $saleOrder->setCreatedBy($user);
        $saleOrder->setUpdatedBy($user);
        $this->em->persist($saleOrder);
        $this->em->flush();
        $this->dispatcher->dispatch(new OrderCreatedEvent($saleOrder));

        return true;
    }

    public function update(SaleOrder $saleOrder, User $user): bool
    {
        $saleOrder->setUpdatedAt(new DateTime());
        $saleOrder->setUpdatedBy($user);
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
