<?php
namespace App\EventListener;

use Lexik\Bundle\JWTAuthenticationBundle\Event\AuthenticationFailureEvent;
use Psr\Log\LoggerInterface;

class LoginFailureListener
{
    public function __construct(private LoggerInterface $logger) {}
    public static function getSubscribedEvents(): array
    {
        return[
            'lexik_jwt_authentication.authentication_failure' => 'onAuthenticationFailure'
        ];
    }

    public function onAuthenticationFailure(AuthenticationFailureEvent $event): void
    {
        $data = $event->getRequest()?->toArray();
        $this->logger->error('Login failure', ['data' => $data, 'message' => $event->getException()->getMessage()]);
    }
}
