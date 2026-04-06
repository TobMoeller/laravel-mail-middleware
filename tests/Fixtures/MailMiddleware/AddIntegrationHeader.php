<?php

namespace TobMoeller\LaravelMailMiddleware\Tests\Fixtures\MailMiddleware;

use Closure;
use TobMoeller\LaravelMailMiddleware\MailMiddleware\MailMiddlewareContract;
use TobMoeller\LaravelMailMiddleware\MailMiddleware\MessageContext;

class AddIntegrationHeader implements MailMiddlewareContract
{
    public function handle(MessageContext $messageContext, Closure $next): mixed
    {
        $messageContext->getMessage()->getHeaders()->addTextHeader('X-Laravel-Mail-Middleware-Test', 'sending');

        return $next($messageContext);
    }
}
