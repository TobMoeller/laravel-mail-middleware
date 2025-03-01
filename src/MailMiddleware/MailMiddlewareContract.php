<?php

namespace TobMoeller\LaravelMailMiddleware\MailMiddleware;

use Closure;

interface MailMiddlewareContract
{
    public function handle(MessageContext $messageContext, Closure $next): mixed;
}
