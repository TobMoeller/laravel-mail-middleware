<?php

namespace TobMoeller\LaravelMailMiddleware\Actions\Logs;

use TobMoeller\LaravelMailMiddleware\MailMiddleware\MessageContext;

interface LogMessageContract
{
    public function log(MessageContext $messageContext): void;
}
