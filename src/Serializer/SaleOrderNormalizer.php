<?php

namespace App\Serializer;

use App\Entity\OrderItem;
use App\Entity\SaleOrder;
use App\Repository\ProductRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\DependencyInjection\Attribute\Autowire;
use Symfony\Component\Serializer\Normalizer\DenormalizerAwareInterface;
use Symfony\Component\Serializer\Normalizer\DenormalizerAwareTrait;
use Symfony\Component\Serializer\Normalizer\DenormalizerInterface;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;

final class SaleOrderNormalizer implements DenormalizerInterface, DenormalizerAwareInterface
{
    use DenormalizerAwareTrait;

    public function __construct(
        #[Autowire(service: 'serializer.normalizer.object')]
        private readonly NormalizerInterface $normalizer,
        private readonly ProductRepository $productRepository,
        private readonly EntityManagerInterface $em,
    ) {
    }

    public function supportsDenormalization($data, $type, $format = null, array $context = []): bool
    {
        return SaleOrder::class === $type;
    }

    public function getSupportedTypes(?string $format): array
    {
        return [SaleOrder::class => true];
    }

    public function denormalize($data, $type, $format = null, array $context = []): SaleOrder
    {
        $itemsPayload = $data['items'] ?? null;
        unset($data['items']);
        /** @var SaleOrder $saleOrder */
        $saleOrder = $this->normalizer->denormalize($data, $type, $format, $context);
        if (\is_array($itemsPayload)) {
            foreach ($itemsPayload as $row) {
                $product = $this->productRepository->find($row['productId']);
                if (!$product) {
                    throw new \InvalidArgumentException('PRODUCT_NOT_FOUND: '.$row['productId']);
                }

                $item = new OrderItem();
                $item->setProduct($product);
                $item->setQuantity($row['quantity']);
                $item->setPrice($product->getPrice());
                $saleOrder->addItem($item);
            }
        }

        return $saleOrder;
    }
}
