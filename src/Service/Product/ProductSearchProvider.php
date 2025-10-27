<?php
namespace App\Service\Product;

use App\Dto\SearchQuery;
use App\Entity\Product;
use App\Service\Search\Provider\SearchProviderInterface;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\DependencyInjection\Attribute\AutoconfigureTag;

#[AutoconfigureTag('app.search_provider', attributes: ['target' => 'product'])]
final class ProductSearchProvider implements SearchProviderInterface
{
    public function __construct(private readonly EntityManagerInterface $em) {}

    public function search(SearchQuery $searchQuery): array
    {
        $qb = $this->em->createQueryBuilder()
            ->select('product')
            ->from(Product::class, 'product');

        if ($searchQuery->query) {
            $qb->andWhere('(product.name LIKE :query OR product.description LIKE :query)')
                ->setParameter('query', '%'.$searchQuery->query.'%');
        }

        if ($searchQuery->sort === 'name') {
            $qb->orderBy('product.name', $searchQuery->order);
        } elseif ($searchQuery->sort === 'price') {
            $qb->orderBy('product.price', $searchQuery->order);
        }

        $total = (int) (clone $qb)->select('COUNT(product.id)')->getQuery()->getSingleScalarResult();
        $offset = ($searchQuery->page - 1) * $searchQuery->perPage;
        $rows = $qb->setFirstResult($offset)->setMaxResults($searchQuery->perPage)->getQuery()->getResult();

        return [
            'data' => $rows,
            'total' => $total,
        ];
    }
}
