<?php

namespace TobMoeller\LaravelMailMiddleware\MailMiddleware\Addresses;

use Closure;
use Illuminate\Support\Arr;
use TobMoeller\LaravelMailMiddleware\Facades\LaravelMailMiddleware;
use TobMoeller\LaravelMailMiddleware\MailMiddleware\MailMiddlewareContract;
use TobMoeller\LaravelMailMiddleware\MailMiddleware\MessageContext;

class AddGlobalCc implements MailMiddlewareContract
{
    public function handle(MessageContext $messageContext, Closure $next): mixed
    {
        if (! empty($cc = LaravelMailMiddleware::globalCcEmailList())) {
            $messageContext->getMessage()->addCc(...$cc);

            $ccList = Arr::join($cc, ';');
            $messageContext->addLog(static::class.PHP_EOL.'Added Global Cc Recipients: '.$ccList);
        }

        return $next($messageContext);
    }
}
