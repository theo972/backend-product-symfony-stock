<?php
namespace App\Service\SaleOrder;

use App\Dto\SearchQuery;
use App\Entity\SaleOrder;
use App\Service\Search\Provider\SearchProviderInterface;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\DependencyInjection\Attribute\AutoconfigureTag;

#[AutoconfigureTag('app.search_provider', attributes: ['key' => 'saleOrder'])]
final class SaleOrderSearchProvider implements SearchProviderInterface
{
    public function __construct(private readonly EntityManagerInterface $em) {}

    public function search(SearchQuery $searchQuery): array
    {
        $qb = $this->em->createQueryBuilder()
            ->select('o')
            ->from(SaleOrder::class, 'o');

        if ($searchQuery->query) {
            $qb->andWhere('(o.name LIKE :q OR o.description LIKE :q)')
                ->setParameter('q', '%'.$searchQuery->query.'%');
        }
        if (($status = $searchQuery->filters['status'] ?? null) !== null) {
            $qb->andWhere('o.status = :st')->setParameter('st', $status);
        }

        if ($searchQuery->sort === 'name') {
            $qb->orderBy('o.name', $searchQuery->order);
        } elseif ($searchQuery->sort === 'total') {
            $qb->orderBy('o.total', $searchQuery->order);
        } else {
            $qb->orderBy('o.id', 'DESC');
        }

        $total = (int) (clone $qb)->select('COUNT(o.id)')->resetDQLPart('orderBy')->getQuery()->getSingleScalarResult();
        $offset = ($searchQuery->page - 1) * $searchQuery->perPage;
        $rows = $qb->setFirstResult($offset)->setMaxResults($searchQuery->perPage)->getQuery()->getResult();

        return [
            'data' => $rows,
            'total' => $total,
        ];
    }
}
