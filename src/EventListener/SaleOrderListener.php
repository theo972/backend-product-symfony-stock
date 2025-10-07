<?php

namespace App\EventListener;

use App\Event\OrderCreatedEvent;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Component\EventDispatcher\Attribute\AsEventListener;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Address;

#[AsEventListener(event: OrderCreatedEvent::class, method: 'onOrderCreated')]
final class SaleOrderListener
{
    public function __construct(
        private MailerInterface $mailer,
    ) {
    }

    public function onOrderCreated(OrderCreatedEvent $event): void
    {
        $saleOrder = $event->getOrder();
        $lines = [];
        foreach ($saleOrder->getItems() as $item) {
            $product = $item->getProduct();
            $lines[] = [
                'name' => $product?->getName(),
                'quantity' => $item->getQuantity(),
                'price' => $item->getPrice(),
                'sum_total' => $item->getPrice() * $item->getQuantity(),
            ];
        }

        $email = (new TemplatedEmail())
            ->from(new Address('admin@symfony-stock.test', 'Stock Shop'))
            ->to($saleOrder->getCreatedBy()?->getEmail())
            ->subject('Commande '.$saleOrder->getName().' confirmée')
            ->htmlTemplate('email/order_created.html.twig')
            ->context([
                'orderName' => $saleOrder->getName(),
                'status' => $saleOrder->getStatus(),
                'total' => $saleOrder->getTotal(),
                'lines' => $lines,
            ]);

        $this->mailer->send($email);
    }
}
