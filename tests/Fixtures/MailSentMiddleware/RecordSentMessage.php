<?php

namespace TobMoeller\LaravelMailMiddleware\Tests\Fixtures\MailSentMiddleware;

use Closure;
use Symfony\Component\Mime\Address;
use TobMoeller\LaravelMailMiddleware\MailSentMiddleware\MailSentMiddlewareContract;
use TobMoeller\LaravelMailMiddleware\MailSentMiddleware\SentMessageContext;

class RecordSentMessage implements MailSentMiddlewareContract
{
    /** @var array<int, array<string, mixed>> */
    public static array $messages = [];

    public function handle(SentMessageContext $messageContext, Closure $next): mixed
    {
        $message = $messageContext->getMessage();

        self::$messages[] = [
            'originatingClass' => $messageContext->getOriginatingClassName(),
            'subject' => method_exists($message, 'getSubject') ? $message->getSubject() : null,
            'to' => array_map(
                static fn (Address $address): string => $address->getAddress(),
                method_exists($message, 'getTo') ? $message->getTo() : []
            ),
        ];

        return $next($messageContext);
    }

    public static function reset(): void
    {
        self::$messages = [];
    }
}
