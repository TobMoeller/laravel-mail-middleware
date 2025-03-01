<?php

namespace TobMoeller\LaravelMailMiddleware\MailMiddleware\Addresses;

use Closure;
use TobMoeller\LaravelMailMiddleware\MailMiddleware\MailMiddlewareContract;
use TobMoeller\LaravelMailMiddleware\MailMiddleware\MessageContext;

class EnsureRecipients implements MailMiddlewareContract
{
    public function handle(MessageContext $messageContext, Closure $next): mixed
    {
        if (! empty($messageContext->getMessage()->getTo())) {
            return $next($messageContext);
        }

        $messageContext->cancelSendingMessage('No recipients left.');

        return null;
    }
}
