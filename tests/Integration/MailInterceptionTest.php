<?php

use Illuminate\Mail\Events\MessageSent;
use Illuminate\Mail\Transport\ArrayTransport;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Mail;
use Symfony\Component\Mailer\SentMessage;
use Symfony\Component\Mime\Address;
use Symfony\Component\Mime\Email;
use TobMoeller\LaravelMailMiddleware\MailMiddleware\Addresses\AddGlobalBcc;
use TobMoeller\LaravelMailMiddleware\MailMiddleware\Addresses\AddGlobalCc;
use TobMoeller\LaravelMailMiddleware\MailMiddleware\Addresses\AddGlobalTo;
use TobMoeller\LaravelMailMiddleware\MailMiddleware\Addresses\BccFilter;
use TobMoeller\LaravelMailMiddleware\MailMiddleware\Addresses\CcFilter;
use TobMoeller\LaravelMailMiddleware\MailMiddleware\Addresses\EnsureRecipients;
use TobMoeller\LaravelMailMiddleware\MailMiddleware\Addresses\ToFilter;
use TobMoeller\LaravelMailMiddleware\Tests\Fixtures\Mail\IntegrationTestMail;
use TobMoeller\LaravelMailMiddleware\Tests\Fixtures\Mail\QueuedIntegrationTestMail;
use TobMoeller\LaravelMailMiddleware\Tests\Fixtures\MailMiddleware\AddIntegrationHeader;
use TobMoeller\LaravelMailMiddleware\Tests\Fixtures\MailSentMiddleware\RecordSentMessage;

beforeEach(function () {
    RecordSentMessage::reset();

    /** @var ArrayTransport $transport */
    $transport = Mail::mailer()->getSymfonyTransport();
    $transport->flush();

    Config::set('mail-middleware.enabled', true);
    Config::set('mail-middleware.sending.middleware.enabled', true);
    Config::set('mail-middleware.sent.middleware.enabled', true);
    Config::set('mail-middleware.sending.log.enabled', false);
    Config::set('mail-middleware.sent.log.enabled', false);
    Config::set('mail-middleware.sending.middleware.pipeline', [
        AddIntegrationHeader::class,
        ToFilter::class,
        CcFilter::class,
        BccFilter::class,
        AddGlobalTo::class,
        AddGlobalCc::class,
        AddGlobalBcc::class,
        EnsureRecipients::class,
    ]);
    Config::set('mail-middleware.sent.middleware.pipeline', [
        RecordSentMessage::class,
    ]);
});

it('blocks delivery when all to recipients are denied', function () {
    $sentEvents = 0;
    Event::listen(MessageSent::class, function () use (&$sentEvents) {
        $sentEvents++;
    });

    Config::set('mail-middleware.sending.middleware.allowed.domains', ['allowed.test']);

    $result = Mail::to('blocked@denied.test')->send(new IntegrationTestMail);

    expect($result)->toBeNull()
        ->and(deliveredMessages())->toHaveCount(0)
        ->and($sentEvents)->toBe(0)
        ->and(RecordSentMessage::$messages)->toBe([]);
});

it('delivers allowed mail, filters denied recipients, adds global recipients, and runs sent middleware', function () {
    $sentEvents = 0;
    Event::listen(MessageSent::class, function () use (&$sentEvents) {
        $sentEvents++;
    });

    Config::set('mail-middleware.sending.middleware.allowed.domains', ['allowed.test']);
    Config::set('mail-middleware.sending.middleware.global.to', ['audit-to@allowed.test']);
    Config::set('mail-middleware.sending.middleware.global.cc', ['audit-cc@allowed.test']);
    Config::set('mail-middleware.sending.middleware.global.bcc', ['audit-bcc@allowed.test']);

    Mail::to(['allowed@allowed.test', 'blocked@denied.test'])
        ->cc(['copy@allowed.test', 'blocked-cc@denied.test'])
        ->bcc(['blind@allowed.test', 'blocked-bcc@denied.test'])
        ->send(new IntegrationTestMail);

    /** @var Email $message */
    $message = deliveredEmail();

    expect(deliveredMessages())->toHaveCount(1)
        ->and($sentEvents)->toBe(1)
        ->and(addresses($message->getTo()))->toBe([
            'allowed@allowed.test',
            'audit-to@allowed.test',
        ])
        ->and(addresses($message->getCc()))->toBe([
            'copy@allowed.test',
            'audit-cc@allowed.test',
        ])
        ->and(addresses($message->getBcc()))->toBe([
            'blind@allowed.test',
            'audit-bcc@allowed.test',
        ])
        ->and($message->getHeaders()->getHeaderBody('X-Laravel-Mail-Middleware-Test'))->toBe('sending')
        ->and(RecordSentMessage::$messages)->toHaveCount(1)
        ->and(RecordSentMessage::$messages[0]['originatingClass'])->toBe(IntegrationTestMail::class)
        ->and(RecordSentMessage::$messages[0]['subject'])->toBe('Integration Test Mail')
        ->and(RecordSentMessage::$messages[0]['to'])->toBe([
            'allowed@allowed.test',
            'audit-to@allowed.test',
        ]);
});

it('applies the same interception rules to queued mail', function () {
    Config::set('mail-middleware.sending.middleware.allowed.domains', ['allowed.test']);
    Config::set('mail-middleware.sending.middleware.global.to', ['queue-audit@allowed.test']);

    Mail::to(['allowed@allowed.test', 'blocked@denied.test'])
        ->queue(new QueuedIntegrationTestMail);

    /** @var Email $message */
    $message = deliveredEmail();

    expect(deliveredMessages())->toHaveCount(1)
        ->and(addresses($message->getTo()))->toBe([
            'allowed@allowed.test',
            'queue-audit@allowed.test',
        ])
        ->and(RecordSentMessage::$messages)->toHaveCount(1)
        ->and(RecordSentMessage::$messages[0]['originatingClass'])->toBe(QueuedIntegrationTestMail::class)
        ->and(RecordSentMessage::$messages[0]['subject'])->toBe('Integration Test Mail');
});

it('blocks queued mail when no allowed to recipients remain', function () {
    Config::set('mail-middleware.sending.middleware.allowed.domains', ['allowed.test']);

    Mail::to('blocked@denied.test')->queue(new QueuedIntegrationTestMail);

    expect(deliveredMessages())->toHaveCount(0)
        ->and(RecordSentMessage::$messages)->toBe([]);
});

/**
 * @return array<int, SentMessage>
 */
function deliveredMessages(): array
{
    /** @var ArrayTransport $transport */
    $transport = Mail::mailer()->getSymfonyTransport();

    return $transport->messages()->all();
}

function deliveredEmail(): Email
{
    $sentMessage = deliveredMessages()[0];
    $message = $sentMessage->getOriginalMessage();

    expect($message)->toBeInstanceOf(Email::class);

    return $message;
}

/**
 * @param  iterable<int, Address>  $addresses
 * @return array<int, string>
 */
function addresses(iterable $addresses): array
{
    $emails = [];

    foreach ($addresses as $address) {
        $emails[] = $address->getAddress();
    }

    return $emails;
}
