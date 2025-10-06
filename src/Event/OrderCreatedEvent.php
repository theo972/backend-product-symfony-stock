<?php

namespace App\Event;

use App\Entity\Orders;

final readonly class OrderCreatedEvent
{
    public function __construct(
        private Orders $orders,
    ) {
    }

    public function getOrder(): Orders
    {
        return $this->orders;
    }
}
