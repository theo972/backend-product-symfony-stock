<?php

namespace App\Entity;

use App\Validator\NoDuplicateInCollection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;

#[ORM\Entity]
#[ORM\Table(name: 'order_items')]
class OrderItem
{
    #[ORM\Id]
    #[ORM\GeneratedValue(strategy: 'IDENTITY')]
    #[ORM\Column(name: 'id', type: 'integer', options: ['unsigned' => true])]
    #[Groups(['saleOrder:read'])]
    private ?int $id = null;

    #[ORM\ManyToOne(inversedBy: 'items')]
    #[ORM\JoinColumn(name: 'order_id', nullable: false, onDelete: 'CASCADE')]
    #[Groups(['product:read'])]
    private ?SaleOrder $saleOrder = null;

    #[ORM\ManyToOne(inversedBy: 'orderItems')]
    #[ORM\JoinColumn(name: 'product_id', nullable: false)]
    #[Groups(['saleOrder:read'])]
    private ?Product $product = null;

    #[ORM\Column(type: 'integer')]
    #[Groups(['saleOrder:read'])]
    private int $price = 0;

    #[ORM\Column(type: 'integer')]
    #[Groups(['saleOrder:read'])]
    private int $quantity = 1;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getOrder(): ?SaleOrder
    {
        return $this->saleOrder;
    }

    public function setOrder(?SaleOrder $saleOrder): OrderItem
    {
        $this->saleOrder = $saleOrder;

        return $this;
    }

    public function getProduct(): ?Product
    {
        return $this->product;
    }

    public function setProduct(?Product $product): OrderItem
    {
        $this->product = $product;

        return $this;
    }

    public function getPrice(): int
    {
        return $this->price;
    }

    public function setPrice(int $price): OrderItem
    {
        $this->price = $price;

        return $this;
    }

    public function getQuantity(): int
    {
        return $this->quantity;
    }

    public function setQuantity(int $quantity): OrderItem
    {
        $this->quantity = $quantity;

        return $this;
    }
}
