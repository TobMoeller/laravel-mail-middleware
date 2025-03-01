<?php

use Illuminate\Mail\Events\MessageSending;
use Illuminate\Mail\Events\MessageSent;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Event;
use Psr\Log\LogLevel;
use TobMoeller\LaravelMailMiddleware\Facades\LaravelMailMiddleware;
use TobMoeller\LaravelMailMiddleware\Listeners\MessageSendingListener;
use TobMoeller\LaravelMailMiddleware\Listeners\MessageSentListener;
use TobMoeller\LaravelMailMiddleware\MailMiddleware\Addresses\AddGlobalBcc;
use TobMoeller\LaravelMailMiddleware\MailMiddleware\Addresses\AddGlobalCc;
use TobMoeller\LaravelMailMiddleware\MailMiddleware\Addresses\AddGlobalTo;
use TobMoeller\LaravelMailMiddleware\MailMiddleware\Addresses\BccFilter;
use TobMoeller\LaravelMailMiddleware\MailMiddleware\Addresses\CcFilter;
use TobMoeller\LaravelMailMiddleware\MailMiddleware\Addresses\EnsureRecipients;
use TobMoeller\LaravelMailMiddleware\MailMiddleware\Addresses\ToFilter;

it('checks if the feature is enabled', function (bool $enabled) {
    Event::fake();
    Config::set('mail-middleware.enabled', $enabled);

    expect(LaravelMailMiddleware::enabled())
        ->toBe($enabled);

    Event::assertListening(MessageSending::class, MessageSendingListener::class);
    Event::assertListening(MessageSent::class, MessageSentListener::class);
})->with([true, false]);

it('returns default mail middleware', function () {
    expect(LaravelMailMiddleware::mailMiddleware())
        ->toBe([
            ToFilter::class,
            CcFilter::class,
            BccFilter::class,
            AddGlobalTo::class,
            AddGlobalCc::class,
            AddGlobalBcc::class,
            EnsureRecipients::class,
        ]);
});

it('returns mail middleware', function (string $event, mixed $value, mixed $expected) {
    Config::set('mail-middleware.'.$event.'.middleware.pipeline', $value);

    $middleware = $event === 'sending' ?
        LaravelMailMiddleware::mailMiddleware() :
        LaravelMailMiddleware::sentMailMiddleware();
    expect($middleware)
        ->toBe($expected);
})->with([
    'sending',
    'sent',
], [
    [
        'value' => ['::class-string::', $class = new class {}],
        'expected' => ['::class-string::', $class],
    ],
    [
        'value' => [],
        'expected' => [],
    ],
    [
        'value' => null,
        'expected' => [],
    ],
    [
        'value' => false,
        'expected' => [],
    ],
]);

it('returns the allowed domain list', function (mixed $value, mixed $expected) {
    Config::set('mail-middleware.sending.middleware.allowed.domains', $value);

    expect(LaravelMailMiddleware::allowedDomainList())
        ->toBe($expected);
})->with([
    [
        'value' => ['foo.de', 'bar.de'],
        'expected' => ['foo.de', 'bar.de'],
    ],
    [
        'value' => 'foo.de;bar.de',
        'expected' => ['foo.de', 'bar.de'],
    ],
    [
        'value' => 'bar.de',
        'expected' => ['bar.de'],
    ],
    [
        'value' => null,
        'expected' => [],
    ],
]);

it('returns the allowed email list', function (mixed $value, mixed $expected) {
    Config::set('mail-middleware.sending.middleware.allowed.emails', $value);

    expect(LaravelMailMiddleware::allowedEmailList())
        ->toBe($expected);
})->with([
    [
        'value' => ['bar@foo.de', 'foo@bar.de'],
        'expected' => ['bar@foo.de', 'foo@bar.de'],
    ],
    [
        'value' => 'bar@foo.de;foo@bar.de',
        'expected' => ['bar@foo.de', 'foo@bar.de'],
    ],
    [
        'value' => 'foo@bar.de',
        'expected' => ['foo@bar.de'],
    ],
    [
        'value' => null,
        'expected' => [],
    ],
]);

it('returns the global to/cc/bcc email lists', function (mixed $value, mixed $expected) {
    Config::set('mail-middleware.sending.middleware.global.to', $value);
    Config::set('mail-middleware.sending.middleware.global.cc', $value);
    Config::set('mail-middleware.sending.middleware.global.bcc', $value);

    expect(LaravelMailMiddleware::globalToEmailList())
        ->toBe($expected)
        ->and(LaravelMailMiddleware::globalCcEmailList())
        ->toBe($expected)
        ->and(LaravelMailMiddleware::globalBccEmailList())
        ->toBe($expected);
})->with([
    [
        'value' => ['bar@foo.de', 'foo@bar.de'],
        'expected' => ['bar@foo.de', 'foo@bar.de'],
    ],
    [
        'value' => 'bar@foo.de;foo@bar.de',
        'expected' => ['bar@foo.de', 'foo@bar.de'],
    ],
    [
        'value' => 'foo@bar.de',
        'expected' => ['foo@bar.de'],
    ],
    [
        'value' => null,
        'expected' => [],
    ],
]);

it('returns if logging is enabled', function (bool $enabled) {
    Config::set('mail-middleware.sending.log.enabled', $enabled);

    expect(LaravelMailMiddleware::logEnabled())
        ->toBe($enabled);
})->with([true, false]);

it('returns if sent logging is enabled', function (?bool $enabled) {
    Config::set('mail-middleware.sending.log.enabled', true);
    Config::set('mail-middleware.sent.log.enabled', $enabled);

    expect(LaravelMailMiddleware::sentLogEnabled())
        ->toBe($enabled === null ? true : $enabled);
})->with([true, false, null]);

it('returns the log channel', function (mixed $value, mixed $default, string $expected) {
    Config::set('logging.default', $default);
    Config::set('mail-middleware.sending.log.channel', $value);

    expect(LaravelMailMiddleware::logChannel())
        ->toBe($expected);
})->with([
    [
        'value' => '::channel::',
        'default' => null,
        'expected' => '::channel::',
    ],
    [
        'value' => null,
        'default' => '::default::',
        'expected' => '::default::',
    ],
    [
        'value' => null,
        'default' => null,
        'expected' => 'stack',
    ],
    [
        'value' => false,
        'default' => '::default::',
        'expected' => '::default::',
    ],
    [
        'value' => false,
        'default' => false,
        'expected' => 'stack',
    ],
]);

it('returns the sent log channel', function (mixed $value, mixed $default, string $expected) {
    Config::set('mail-middleware.sending.log.channel', $default);
    Config::set('mail-middleware.sent.log.channel', $value);

    expect(LaravelMailMiddleware::sentLogChannel())
        ->toBe($expected);
})->with([
    [
        'value' => '::channel::',
        'default' => null,
        'expected' => '::channel::',
    ],
    [
        'value' => null,
        'default' => '::default::',
        'expected' => '::default::',
    ],
    [
        'value' => null,
        'default' => null,
        'expected' => 'stack',
    ],
    [
        'value' => false,
        'default' => '::default::',
        'expected' => '::default::',
    ],
    [
        'value' => false,
        'default' => false,
        'expected' => 'stack',
    ],
]);

it('returns the log level', function (string $level) {
    Config::set('mail-middleware.sending.log.level', $level);

    expect(LaravelMailMiddleware::logLevel())
        ->toBe($level);
})->with(fn () => array_values((new ReflectionClass(LogLevel::class))->getConstants()));

it('returns the sent log level', function (?string $level) {
    Config::set('mail-middleware.sending.log.level', LogLevel::INFO);
    Config::set('mail-middleware.sent.log.level', $level);

    expect(LaravelMailMiddleware::sentLogLevel())
        ->toBe($level === null ? LogLevel::INFO : $level);
})->with(fn () => array_merge(array_values((new ReflectionClass(LogLevel::class))->getConstants()), [null]));

it('throws on invalid log levels', function (mixed $level) {
    Config::set('mail-middleware.sending.log.level', $level);

    expect(fn () => LaravelMailMiddleware::logLevel())
        ->toThrow(InvalidArgumentException::class, 'Invalid log level provided');
})->with([
    '::invalid_level::',
    null,
]);

it('throws on invalid sent log levels', function () {
    Config::set('mail-middleware.sent.log.level', '::invalid_level::');

    expect(fn () => LaravelMailMiddleware::sentLogLevel())
        ->toThrow(InvalidArgumentException::class, 'Invalid log level provided');
});

it('returns if middleware should be logged', function (bool $enabled) {
    Config::set('mail-middleware.sending.log.include.middleware', $enabled);

    expect(LaravelMailMiddleware::logMiddleware())
        ->toBe($enabled);
})->with([true, false]);

it('returns if headers should be logged', function (bool $enabled) {
    Config::set('mail-middleware.sending.log.include.headers', $enabled);

    expect(LaravelMailMiddleware::logHeaders())
        ->toBe($enabled);
})->with([true, false]);

it('returns if body should be logged', function (bool $enabled) {
    Config::set('mail-middleware.sending.log.include.body', $enabled);

    expect(LaravelMailMiddleware::logBody())
        ->toBe($enabled);
})->with([true, false]);

it('returns if sent middleware should be logged', function (bool $enabled) {
    Config::set('mail-middleware.sent.log.include.middleware', $enabled);

    expect(LaravelMailMiddleware::sentLogMiddleware())
        ->toBe($enabled);
})->with([true, false]);

it('returns if sent headers should be logged', function (bool $enabled) {
    Config::set('mail-middleware.sent.log.include.headers', $enabled);

    expect(LaravelMailMiddleware::sentLogHeaders())
        ->toBe($enabled);
})->with([true, false]);

it('returns if sent body should be logged', function (bool $enabled) {
    Config::set('mail-middleware.sent.log.include.body', $enabled);

    expect(LaravelMailMiddleware::sentLogBody())
        ->toBe($enabled);
})->with([true, false]);
