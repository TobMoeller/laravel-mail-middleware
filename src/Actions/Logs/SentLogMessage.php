<?php

namespace TobMoeller\LaravelMailMiddleware\Actions\Logs;

use Illuminate\Support\Facades\Log;
use TobMoeller\LaravelMailMiddleware\Facades\LaravelMailMiddleware;
use TobMoeller\LaravelMailMiddleware\MailSentMiddleware\SentMessageContext;

class SentLogMessage implements SentLogMessageContract
{
    public function __construct(public GenerateSentLogMessageContract $generateLogMessage) {}

    public function log(SentMessageContext $messageContext): void
    {
        $message = $this->generateLogMessage->generate($messageContext);

        Log::channel(LaravelMailMiddleware::sentLogChannel())
            ->log(LaravelMailMiddleware::sentLogLevel(), $message);
    }
}
