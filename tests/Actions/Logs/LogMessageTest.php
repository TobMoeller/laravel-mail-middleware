<?php

use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Log;
use Psr\Log\LogLevel;
use Symfony\Component\Mime\Email;
use TobMoeller\LaravelMailMiddleware\Actions\Logs\GenerateLogMessageContract;
use TobMoeller\LaravelMailMiddleware\Actions\Logs\LogMessage;
use TobMoeller\LaravelMailMiddleware\Actions\Logs\LogMessageContract;
use TobMoeller\LaravelMailMiddleware\MailMiddleware\MessageContext;

it('is bound to interface', function () {
    expect(app(LogMessageContract::class))
        ->toBeInstanceOf(LogMessage::class);
});

it('logs the message context', function () {
    Config::set('mail-middleware.sending.log.channel', '::channel::');
    Config::set('mail-middleware.sending.log.level', LogLevel::INFO);

    $mail = new Email;
    $context = new MessageContext($mail);

    $messageGeneratorMock = Mockery::mock(GenerateLogMessageContract::class);
    $messageGeneratorMock->shouldReceive('generate')
        ->once()
        ->with(Mockery::on(fn (MessageContext $contextArgument) => $contextArgument === $context))
        ->andReturn('::log_message::');

    Log::shouldReceive('channel')
        ->once()
        ->with('::channel::')
        ->andReturnSelf()
        ->shouldReceive('log')
        ->once()
        ->with(LogLevel::INFO, '::log_message::');

    $logger = new LogMessage($messageGeneratorMock);
    $logger->log($context);
});
