<?php
namespace App\Service\SaleOrder;

use App\Dto\SearchQuery;
use App\Entity\SaleOrder;
use App\Service\Search\Provider\SearchProviderInterface;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\DependencyInjection\Attribute\AutoconfigureTag;

#[AutoconfigureTag('app.search_provider', attributes: ['target' => 'saleOrder'])]
final class SaleOrderSearchProvider implements SearchProviderInterface
{
    public function __construct(private readonly EntityManagerInterface $em) {}

    public function search(SearchQuery $searchQuery): array
    {
        $qb = $this->em->createQueryBuilder()
            ->select('saleOrder')
            ->from(SaleOrder::class, 'saleOrder');

        if ($searchQuery->query) {
            $qb->andWhere('(saleOrder.name LIKE :query OR saleOrder.description LIKE :query)')
                ->setParameter('query', '%'.$searchQuery->query.'%');
        }
        if (($status = $searchQuery->filters['status'] ?? null) !== null) {
            $qb->andWhere('saleOrder.status = :status')->setParameter('status', $status);
        }

        if ($searchQuery->sort === 'name') {
            $qb->orderBy('saleOrder.name', $searchQuery->order);
        } else {
            $qb->orderBy('saleOrder.id', 'DESC');
        }

        $total = (int) (clone $qb)->select('COUNT(saleOrder.id)')->getQuery()->getSingleScalarResult();
        $offset = ($searchQuery->page - 1) * $searchQuery->perPage;
        $rows = $qb->setFirstResult($offset)->setMaxResults($searchQuery->perPage)->getQuery()->getResult();

        return [
            'data' => $rows,
            'total' => $total,
        ];
    }
}
