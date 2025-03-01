<?php

namespace TobMoeller\LaravelMailMiddleware\Listeners;

use Illuminate\Mail\Events\MessageSending;
use Illuminate\Support\Facades\Pipeline;
use TobMoeller\LaravelMailMiddleware\Actions\Logs\LogMessageContract;
use TobMoeller\LaravelMailMiddleware\Facades\LaravelMailMiddleware;
use TobMoeller\LaravelMailMiddleware\MailMiddleware\MessageContext;

class MessageSendingListener
{
    public function __construct(public LogMessageContract $messageLogger)
    {
        //
    }

    public function handle(MessageSending $messageSendingEvent): ?false
    {
        if (! LaravelMailMiddleware::enabled()) {
            return null;
        }

        $messageContext = app(MessageContext::class, [
            'message' => $messageSendingEvent->message,
            'messageData' => $messageSendingEvent->data,
        ]);

        if (LaravelMailMiddleware::mailMiddlewareEnabled()) {
            Pipeline::send($messageContext)
                ->through(LaravelMailMiddleware::mailMiddleware())
                ->thenReturn();
        }

        if (LaravelMailMiddleware::logEnabled()) {
            $this->messageLogger->log($messageContext);
        }

        // The `Mailer` dispatches the `MessageSending` event until
        // a non null value is returned by a listener. If false is
        // returned, the mail will be stopped entirely. If true is
        // returned, the mail will be sent, but the event will not
        // be dispatched again for other listeners registered
        // after this one.
        return $messageContext->shouldSendMessage() ? null : false;
    }
}
