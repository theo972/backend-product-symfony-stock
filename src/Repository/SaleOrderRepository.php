<?php

namespace App\Repository;

use App\Entity\SaleOrder;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\Persistence\ManagerRegistry;
use Knp\Component\Pager\Pagination\PaginationInterface;
use Knp\Component\Pager\PaginatorInterface;

class SaleOrderRepository extends ServiceEntityRepository
{
    public function __construct(
        ManagerRegistry $registry,
        private readonly EntityManagerInterface $entityManager,
        private readonly PaginatorInterface $paginator
    )
    {
        parent::__construct($registry, SaleOrder::class);
    }

    public function search(int $page, int $size): PaginationInterface
    {
        $query = $this->entityManager->createQueryBuilder()
            ->select('o')
            ->from(SaleOrder::class, 'o')
            ->orderBy('o.name', 'ASC');

        return $this->paginator->paginate(
            $query->getQuery(),
            $page,
            $size
        );
    }
}
