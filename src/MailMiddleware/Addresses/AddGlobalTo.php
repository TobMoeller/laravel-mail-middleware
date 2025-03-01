<?php

namespace TobMoeller\LaravelMailMiddleware\MailMiddleware\Addresses;

use Closure;
use Illuminate\Support\Arr;
use TobMoeller\LaravelMailMiddleware\Facades\LaravelMailMiddleware;
use TobMoeller\LaravelMailMiddleware\MailMiddleware\MailMiddlewareContract;
use TobMoeller\LaravelMailMiddleware\MailMiddleware\MessageContext;

class AddGlobalTo implements MailMiddlewareContract
{
    public function handle(MessageContext $messageContext, Closure $next): mixed
    {
        if (! empty($to = LaravelMailMiddleware::globalToEmailList())) {
            $messageContext->getMessage()->addTo(...$to);

            $toList = Arr::join($to, ';');
            $messageContext->addLog(static::class.PHP_EOL.'Added Global To Recipients: '.$toList);
        }

        return $next($messageContext);
    }
}
