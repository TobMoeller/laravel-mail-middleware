<?php

namespace TobMoeller\LaravelMailMiddleware\Listeners;

use Illuminate\Mail\Events\MessageSent;
use Illuminate\Support\Facades\Pipeline;
use TobMoeller\LaravelMailMiddleware\Actions\Logs\SentLogMessageContract;
use TobMoeller\LaravelMailMiddleware\Facades\LaravelMailMiddleware;
use TobMoeller\LaravelMailMiddleware\MailSentMiddleware\SentMessageContext;

class MessageSentListener
{
    public function __construct(public SentLogMessageContract $messageLogger)
    {
        //
    }

    public function handle(MessageSent $messageSent): void
    {
        if (! LaravelMailMiddleware::enabled()) {
            return;
        }

        $messageContext = app(SentMessageContext::class, [
            'sentMessage' => $messageSent->sent,
            'messageData' => $messageSent->data,
        ]);

        if (LaravelMailMiddleware::sentMailMiddlewareEnabled()) {
            Pipeline::send($messageContext)
                ->through(LaravelMailMiddleware::sentMailMiddleware())
                ->thenReturn();
        }

        if (LaravelMailMiddleware::sentLogEnabled()) {
            $this->messageLogger->log($messageContext);
        }
    }
}
