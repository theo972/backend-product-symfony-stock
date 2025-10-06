<?php

namespace App\Event;

use App\Entity\SaleOrder;

final readonly class OrderCreatedEvent
{
    public function __construct(
        private SaleOrder $saleOrder,
    ) {
    }

    public function getOrder(): SaleOrder
    {
        return $this->saleOrder;
    }
}
