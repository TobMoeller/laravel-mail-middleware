<?php

use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Log;
use Psr\Log\LogLevel;
use TobMoeller\LaravelMailMiddleware\Actions\Logs\GenerateSentLogMessageContract;
use TobMoeller\LaravelMailMiddleware\Actions\Logs\SentLogMessage;
use TobMoeller\LaravelMailMiddleware\Actions\Logs\SentLogMessageContract;
use TobMoeller\LaravelMailMiddleware\MailSentMiddleware\SentMessageContext;

it('is bound to interface', function () {
    expect(app(SentLogMessageContract::class))
        ->toBeInstanceOf(SentLogMessage::class);
});

it('logs the message context', function () {
    Config::set('mail-middleware.sent.log.channel', '::channel::');
    Config::set('mail-middleware.sent.log.level', LogLevel::INFO);

    $context = new SentMessageContext(generateSentMessage());

    $messageGeneratorMock = Mockery::mock(GenerateSentLogMessageContract::class);
    $messageGeneratorMock->shouldReceive('generate')
        ->once()
        ->with(Mockery::on(fn (SentMessageContext $contextArgument) => $contextArgument === $context))
        ->andReturn('::log_message::');

    Log::shouldReceive('channel')
        ->once()
        ->with('::channel::')
        ->andReturnSelf()
        ->shouldReceive('log')
        ->once()
        ->with(LogLevel::INFO, '::log_message::');

    $logger = new SentLogMessage($messageGeneratorMock);
    $logger->log($context);
});
