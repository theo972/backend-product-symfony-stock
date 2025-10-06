<?php

namespace App\Entity;

use App\Repository\OrderRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: OrderRepository::class)]
#[ORM\Table(name: "orders")]
class Order
{
    #[ORM\Column(name: 'id', type: 'integer', nullable: false, options: ['unsigned' => true])]
    #[ORM\Id]
    #[ORM\GeneratedValue(strategy: 'IDENTITY')]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    #[Assert\NotBlank]
    #[Groups(['order:read', 'order:create', 'order:update'])]
    private string $name;

    #[ORM\Column(type: 'text', nullable: true)]
    #[Groups(['order:read', 'order:create', 'order:update'])]
    private ?string $description = null;

    #[ORM\Column(length: 20)]
    #[Assert\NotBlank]
    #[Groups(['order:read', 'order:create', 'order:update'])]
    private string $status = 'DRAFT';

    #[ORM\Column(type: 'integer')]
    #[Assert\NotNull]
    #[Groups(['order:read', 'order:create', 'order:update'])]
    private int $total = 0;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function setName(string $n): self
    {
        $this->name = $n;
        return $this;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(?string $d): self
    {
        $this->description = $d;
        return $this;
    }

    public function getStatus(): string
    {
        return $this->status;
    }

    public function setStatus(string $s): self
    {
        $this->status = $s;
        return $this;
    }

    public function getTotal(): int
    {
        return $this->total;
    }

    public function setTotal(int $t): self
    {
        $this->total = $t;
        return $this;
    }
}
