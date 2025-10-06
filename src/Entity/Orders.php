<?php

namespace App\Entity;

use App\Repository\OrderRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Validator\Constraints as Assert;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;

#[ORM\Entity(repositoryClass: OrderRepository::class)]
#[ORM\Table(name: 'orders')]
class Orders
{
    public function __construct()
    {
        $this->items = new ArrayCollection();
    }

    #[ORM\Column(name: 'id', type: 'integer', nullable: false, options: ['unsigned' => true])]
    #[ORM\Id]
    #[ORM\GeneratedValue(strategy: 'IDENTITY')]
    private ?int $id = null;

    #[ORM\Column(name: 'name', type: 'string', length: 255, nullable: false)]
    #[Assert\NotBlank]
    #[Groups(['order:read', 'order:create', 'order:update'])]
    private string $name;

    #[ORM\Column(name: 'description', type: 'text', nullable: true)]
    #[Groups(['order:read', 'order:create', 'order:update'])]
    private ?string $description = null;

    #[ORM\Column(name: 'status', type: 'string', length: 100, nullable: false)]
    #[Assert\Choice(
        options: ['delivery_in_progress', 'received', 'partially_received', 'cancelled'],
        message: 'Status invalid',
        groups: ['order:create', 'order:update']
    )]
    #[Groups(['order:read', 'order:create', 'order:update'])]
    private string $status = 'delivery_in_progress';

    #[ORM\Column(name: 'total', type: 'integer')]
    #[Assert\NotNull]
    #[Groups(['order:read', 'order:create', 'order:update'])]
    private int $total = 0;

    #[ORM\OneToMany(mappedBy: 'order', targetEntity: OrderItem::class, cascade: ['persist'], orphanRemoval: true)]
    #[Groups(['order:read'])]
    private Collection $items;


    public function getId(): ?int
    {
        return $this->id;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function setName(string $name): Orders
    {
        $this->name = $name;
        return $this;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(?string $description): Orders
    {
        $this->description = $description;
        return $this;
    }

    public function getStatus(): string
    {
        return $this->status;
    }

    public function setStatus(string $status): Orders
    {
        $this->status = $status;
        return $this;
    }

    public function getTotal(): int
    {
        return $this->total;
    }

    public function setTotal(int $total): Orders
    {
        $this->total = $total;
        return $this;
    }

    /** @return Collection<int, OrderItem> */
    public function getItems(): Collection {
        return $this->items;
    }

    public function addItem(OrderItem $item): Orders
    {
        if (!$this->items->contains($item)) {
            $this->items->add($item);
            $item->setOrder($this);
            $this->recalculateTotal();
        }
        return $this;
    }

    public function removeItem(OrderItem $item): Orders
    {
        if ($this->items->removeElement($item)) {
            if ($item->getOrder() === $this) {
                $item->setOrder(null);
            }
            $this->recalculateTotal();
        }
        return $this;
    }

    public function recalculateTotal(): void
    {
        $sum = 0;
        foreach ($this->items as $item) {
            $sum += $item->getPrice() * $item->getQuantity();
        }
        $this->total = $sum;
    }
}
