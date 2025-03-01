<?php

namespace TobMoeller\LaravelMailMiddleware;

use Illuminate\Mail\Events\MessageSending;
use Illuminate\Mail\Events\MessageSent;
use Illuminate\Support\Facades\Event;
use Spatie\LaravelPackageTools\Package;
use Spatie\LaravelPackageTools\PackageServiceProvider;
use TobMoeller\LaravelMailMiddleware\Actions\Addresses\IsAllowedRecipient;
use TobMoeller\LaravelMailMiddleware\Actions\Logs\GenerateLogMessage;
use TobMoeller\LaravelMailMiddleware\Actions\Logs\GenerateLogMessageContract;
use TobMoeller\LaravelMailMiddleware\Actions\Logs\GenerateSentLogMessage;
use TobMoeller\LaravelMailMiddleware\Actions\Logs\GenerateSentLogMessageContract;
use TobMoeller\LaravelMailMiddleware\Actions\Logs\LogMessage;
use TobMoeller\LaravelMailMiddleware\Actions\Logs\LogMessageContract;
use TobMoeller\LaravelMailMiddleware\Actions\Logs\SentLogMessage;
use TobMoeller\LaravelMailMiddleware\Actions\Logs\SentLogMessageContract;
use TobMoeller\LaravelMailMiddleware\Facades\LaravelMailMiddleware;
use TobMoeller\LaravelMailMiddleware\Listeners\MessageSendingListener;
use TobMoeller\LaravelMailMiddleware\Listeners\MessageSentListener;

class LaravelMailMiddlewareServiceProvider extends PackageServiceProvider
{
    public function configurePackage(Package $package): void
    {
        $package
            ->name('laravel-mail-middleware')
            ->hasConfigFile('mail-middleware');
    }

    public function packageRegistered(): void
    {
        $this->app->bind(LogMessageContract::class, LogMessage::class);
        $this->app->bind(GenerateLogMessageContract::class, GenerateLogMessage::class);

        $this->app->bind(SentLogMessageContract::class, SentLogMessage::class);
        $this->app->bind(GenerateSentLogMessageContract::class, GenerateSentLogMessage::class);

        $this->app->singleton(IsAllowedRecipient::class, function () {
            return new IsAllowedRecipient(
                LaravelMailMiddleware::allowedDomainList(),
                LaravelMailMiddleware::allowedEmailList(),
            );
        });
    }

    public function packageBooted(): void
    {
        if (LaravelMailMiddleware::enabled()) {
            Event::listen(MessageSending::class, MessageSendingListener::class);
            Event::listen(MessageSent::class, MessageSentListener::class);
        }
    }
}
