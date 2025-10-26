<?php
namespace App\Service\Product;

use App\Dto\SearchQuery;
use App\Entity\Product;
use App\Service\Search\Provider\SearchProviderInterface;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\DependencyInjection\Attribute\AutoconfigureTag;

#[AutoconfigureTag('app.search_provider')]
final class ProductSearchProvider implements SearchProviderInterface
{
    public function __construct(private readonly EntityManagerInterface $em) {}

    public function support(): string
    {
        return 'product';
    }


    public function search(SearchQuery $searchQuery): array
    {
        $qb = $this->em->createQueryBuilder()
            ->select('p')
            ->from(Product::class, 'p');

        if ($searchQuery->query) {
            $qb->andWhere('(p.name LIKE :q OR p.description LIKE :q)')
                ->setParameter('q', '%'.$searchQuery->query.'%');
        }

        // Exemples de filtres : ?filters[category]=12
        if (($cat = $searchQuery->filters['category'] ?? null) !== null) {
            $qb->andWhere('p.category = :cat')->setParameter('cat', (int) $cat);
        }

        // Tri
        if ($searchQuery->sort === 'name') {
            $qb->orderBy('p.name', $searchQuery->order);
        } elseif ($searchQuery->sort === 'price') {
            $qb->orderBy('p.price', $searchQuery->order);
        } else {
            // fallback
            $qb->orderBy('p.id', 'DESC');
        }

        // Total
        $total = (int) (clone $qb)->select('COUNT(p.id)')->resetDQLPart('orderBy')->getQuery()->getSingleScalarResult();

        // Pagination
        $offset = ($searchQuery->page - 1) * $searchQuery->perPage;
        $rows = $qb->setFirstResult($offset)->setMaxResults($searchQuery->perPage)->getQuery()->getResult();

        $items = [];
        foreach ($rows as $p) {
            /** @var Product $p */
            $items[] = $p;
        }

        return [
            'items'  => $items,
            'total'  => $total,
        ];
    }
}
