<?php
namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;

#[ORM\Entity]
#[ORM\Table(name: 'order_items')]
class OrderItem
{
    #[ORM\Id]
    #[ORM\GeneratedValue(strategy: 'IDENTITY')]
    #[ORM\Column(name: 'id', type: 'integer', options: ['unsigned' => true])]
    #[Groups(['order:read'])]
    private ?int $id = null;

    // Owning side -> FK order_id
    #[ORM\ManyToOne(inversedBy: 'items')]
    #[ORM\JoinColumn(name: 'order_id', nullable: false, onDelete: 'CASCADE')]
    private ?Orders $order = null;

    // Owning side -> FK product_id
    #[ORM\ManyToOne(inversedBy: 'orderItems')]
    #[ORM\JoinColumn(name: 'product_id', nullable: false)]
    #[Groups(['order:read'])]
    private ?Product $product = null;

    // Prix unitaire figé au moment de l’ajout (centimes)
    #[ORM\Column(type: 'integer')]
    #[Groups(['order:read'])]
    private int $unitPrice = 0;

    #[ORM\Column(type: 'integer')]
    #[Groups(['order:read'])]
    private int $quantity = 1;

    public function getId(): ?int { return $this->id; }

    public function getOrder(): ?Orders { return $this->order; }
    public function setOrder(?Orders $order): self { $this->order = $order; return $this; }

    public function getProduct(): ?Product { return $this->product; }
    public function setProduct(?Product $product): self { $this->product = $product; return $this; }

    public function getUnitPrice(): int { return $this->unitPrice; }
    public function setUnitPrice(int $p): self { $this->unitPrice = $p; return $this; }

    public function getQuantity(): int { return $this->quantity; }
    public function setQuantity(int $q): self { $this->quantity = $q; return $this; }
}
