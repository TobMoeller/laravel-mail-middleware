<?php

namespace TobMoeller\LaravelMailMiddleware\Actions\Logs;

use TobMoeller\LaravelMailMiddleware\MailSentMiddleware\SentMessageContext;

interface GenerateSentLogMessageContract
{
    public function generate(SentMessageContext $messageContext): string;
}
