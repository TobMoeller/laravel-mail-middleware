<?php

namespace TobMoeller\LaravelMailMiddleware\MailMiddleware\Addresses;

use Closure;
use Illuminate\Support\Arr;
use TobMoeller\LaravelMailMiddleware\Facades\LaravelMailMiddleware;
use TobMoeller\LaravelMailMiddleware\MailMiddleware\MailMiddlewareContract;
use TobMoeller\LaravelMailMiddleware\MailMiddleware\MessageContext;

class AddGlobalBcc implements MailMiddlewareContract
{
    public function handle(MessageContext $messageContext, Closure $next): mixed
    {
        if (! empty($bcc = LaravelMailMiddleware::globalBccEmailList())) {
            $messageContext->getMessage()->addBcc(...$bcc);

            $bccList = Arr::join($bcc, ';');
            $messageContext->addLog(static::class.PHP_EOL.'Added Global Bcc Recipients: '.$bccList);
        }

        return $next($messageContext);
    }
}
