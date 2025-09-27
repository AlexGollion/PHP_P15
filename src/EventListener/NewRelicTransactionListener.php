<?php

namespace App\EventListener;

use Symfony\Component\EventDispatcher\Attribute\AsEventListener;
use Symfony\Component\HttpKernel\Event\RequestEvent;

final class NewRelicTransactionListener
{
    #[AsEventListener(event: 'kernel.request', priority: 255)]
    public function onRequestEvent(RequestEvent $event): void
    {
        if (extension_loaded('newrelic')) {
            $request = $event->getRequest();
            newrelic_name_transaction($request->getPathInfo());
        }
    }
}
