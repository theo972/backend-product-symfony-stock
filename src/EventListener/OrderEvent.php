<?php

namespace App\EventListener;

use App\Event\OrderCreatedEvent;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Component\EventDispatcher\Attribute\AsEventListener;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Address;

#[AsEventListener(event: OrderCreatedEvent::class, method: 'onOrderCreated')]
final class OrderEvent
{
    public function __construct(
        private MailerInterface $mailer,
    ) {
    }

    public function onOrderCreated(OrderCreatedEvent $event): void
    {
        $order = $event->getOrder();
        $lines = [];
        foreach ($order->getItems() as $item) {
            $product = $item->getProduct();
            $lines[] = [
                'name' => $product?->getName() ?? 'Produit',
                'quantity' => $item->getQuantity(),
                'price' => $item->getPrice(),
                'sum_total' => $item->getPrice() * $item->getQuantity(),
            ];
        }

        $to = 'dev@example.com';
        $email = (new TemplatedEmail())
            ->from(new Address('admin@symfony-stock.test', 'Stock Shop'))
            ->to($to)
            ->subject('Commande '.$order->getName().' confirmée')
            ->htmlTemplate('email/order_created.html.twig')
            ->context([
                'orderName' => $order->getName(),
                'status' => $order->getStatus(),
                'total' => $order->getTotal(),
                'lines' => $lines,
            ]);

        $this->mailer->send($email);
    }
}
