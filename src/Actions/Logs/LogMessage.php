<?php

namespace TobMoeller\LaravelMailMiddleware\Actions\Logs;

use Illuminate\Support\Facades\Log;
use TobMoeller\LaravelMailMiddleware\Facades\LaravelMailMiddleware;
use TobMoeller\LaravelMailMiddleware\MailMiddleware\MessageContext;

class LogMessage implements LogMessageContract
{
    public function __construct(public GenerateLogMessageContract $generateLogMessage) {}

    public function log(MessageContext $messageContext): void
    {
        $message = $this->generateLogMessage->generate($messageContext);

        Log::channel(LaravelMailMiddleware::logChannel())
            ->log(LaravelMailMiddleware::logLevel(), $message);
    }
}
