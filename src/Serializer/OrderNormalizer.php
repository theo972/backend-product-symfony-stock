<?php
// src/Serializer/OrderDenormalizer.php
namespace App\Serializer;

use App\Entity\Orders;
use App\Entity\OrderItem;
use App\Repository\ProductRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\DependencyInjection\Attribute\Autowire;
use Symfony\Component\Serializer\Normalizer\DenormalizerInterface;
use Symfony\Component\Serializer\Normalizer\DenormalizerAwareInterface;
use Symfony\Component\Serializer\Normalizer\DenormalizerAwareTrait;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;

final class OrderNormalizer implements DenormalizerInterface, DenormalizerAwareInterface
{
    use DenormalizerAwareTrait;

    public function __construct(
        #[Autowire(service: 'serializer.normalizer.object')]
        private NormalizerInterface $normalizer,
        private ProductRepository $productRepository,
        private EntityManagerInterface $em,
    ) {}

    public function supportsDenormalization($data, $type, $format = null, array $context = []): bool
    {
        return $type === Orders::class;
    }

     public function getSupportedTypes(?string $format): array {
        return [Orders::class => true];
    }

    public function denormalize($data, $type, $format = null, array $context = []): Orders
    {
        $itemsPayload = $data['items'] ?? null;
        unset($data['items']);

        /** @var Orders $order */
        $order = $this->normalizer->denormalize($data, $type, $format, $context);

        if (\is_array($itemsPayload)) {
            foreach ($itemsPayload as $row) {
                $product = $this->productRepository->find($row['productId']);
                if (!$product) {
                    throw new \InvalidArgumentException("PRODUCT_NOT_FOUND: " .$row['productId']);
                }
                $quantity = max(1, (int)($row['quantity'] ?? 1));

                $item = new OrderItem();
                $item->setProduct($product);
                $item->setQuantity($quantity);
                $item->setUnitPrice($product->getPrice()); // prix figé ici
                $order->addItem($item); // maintient la bidirection + recalc
            }
        }

        return $order;
    }
}
