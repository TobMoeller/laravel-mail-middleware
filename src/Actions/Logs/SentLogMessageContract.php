<?php

namespace TobMoeller\LaravelMailMiddleware\Actions\Logs;

use TobMoeller\LaravelMailMiddleware\MailSentMiddleware\SentMessageContext;

interface SentLogMessageContract
{
    public function log(SentMessageContext $messageContext): void;
}
