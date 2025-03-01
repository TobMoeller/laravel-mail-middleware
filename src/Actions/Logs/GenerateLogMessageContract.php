<?php

namespace TobMoeller\LaravelMailMiddleware\Actions\Logs;

use TobMoeller\LaravelMailMiddleware\MailMiddleware\MessageContext;

interface GenerateLogMessageContract
{
    public function generate(MessageContext $messageContext): string;
}
