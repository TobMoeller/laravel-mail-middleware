<?php

namespace TobMoeller\LaravelMailMiddleware\MailMiddleware;

use Illuminate\Support\Collection;
use Symfony\Component\Mime\Email;
use TobMoeller\LaravelMailMiddleware\Traits\MailMiddleware\HandlesMessageData;
use TobMoeller\LaravelMailMiddleware\Traits\MailMiddleware\LogsMessages;

class MessageContext
{
    use HandlesMessageData;
    use LogsMessages;

    /**
     * Collection for middleware to share temporary data
     * with other middleware further down the pipeline.
     *
     * @var Collection<string, mixed>
     */
    public Collection $sharedData;

    protected Email $message;

    protected bool $shouldSendMessage = true;

    /**
     * @param  array<string, mixed>  $messageData
     */
    public function __construct(Email $message, array $messageData = [])
    {
        $this->message = $message;
        $this->messageData = $messageData;
        $this->sharedData = new Collection;
    }

    public function getMessage(): Email
    {
        return $this->message;
    }

    public function cancelSendingMessage(string $reason): void
    {
        $this->shouldSendMessage = false;
        $this->addLog('Message canceled: '.$reason);
    }

    public function shouldSendMessage(): bool
    {
        return $this->shouldSendMessage;
    }
}
