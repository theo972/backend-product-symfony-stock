<?php

namespace App\Entity;

use App\Repository\ProductRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Validator\Constraints as Assert;
#[ORM\Entity(repositoryClass: ProductRepository::class)]
#[ORM\Table(name: 'products')]
class Product
{
    public function __construct()
    {
        $this->orderItems = new ArrayCollection();
    }

    #[ORM\Column(name: 'id', type: 'integer', nullable: false, options: ['unsigned' => true])]
    #[ORM\Id]
    #[ORM\GeneratedValue(strategy: 'IDENTITY')]
    private ?int $id = null;

    #[ORM\Column(name: 'name', type: 'string', length: 255, nullable: false)]
    #[Assert\NotBlank]
    #[Groups(['product:read', 'product:create', 'product:update'])]
    private string $name;

    #[ORM\Column(name: 'description', type: 'text', nullable: true)]
    #[Groups(['product:read', 'product:create', 'product:update'])]
    private ?string $description = null;

    #[ORM\Column(name: 'price', type: 'integer', nullable: false)]
    #[Assert\NotNull]
    #[Assert\GreaterThanOrEqual(0)]
    #[Groups(['product:read', 'product:create', 'product:update'])]
    private int $price;

    #[ORM\Column(name: 'stock', type: 'integer', nullable: false)]
    #[Assert\NotNull]
    #[Assert\GreaterThanOrEqual(0)]
    #[Groups(['product:read', 'product:create', 'product:update'])]
    private int $stock;

    #[ORM\OneToMany(targetEntity: OrderItem::class, mappedBy: 'product')]
    private Collection $orderItems;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function setName(string $name): Product
    {
        $this->name = $name;
        return $this;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(?string $description): Product
    {
        $this->description = $description;
        return $this;
    }

    public function getPrice(): int
    {
        return $this->price;
    }

    public function setPrice(int $price): Product
    {
        $this->price = $price;
        return $this;
    }

    public function getStock(): int
    {
        return $this->stock;
    }

    public function setStock(int $stock): Product
    {
        $this->stock = $stock;
        return $this;
    }

    /** @return Collection<int, OrderItem> */
    public function getOrderItems(): Collection
    {
        return $this->orderItems;
    }

    public function addOrderItem(OrderItem $orderItem): Product
    {
        if (!$this->orderItems->contains($orderItem)) {
            $this->orderItems->add($orderItem);
            $orderItem->setProduct($this);
        }
        return $this;
    }

    public function removeOrderItem(OrderItem $orderItem): Product
    {
        if ($this->orderItems->removeElement($orderItem)) {
            if ($orderItem->getProduct() === $this) {
                $orderItem->setProduct(null);
            }
        }
        return $this;
    }
}
