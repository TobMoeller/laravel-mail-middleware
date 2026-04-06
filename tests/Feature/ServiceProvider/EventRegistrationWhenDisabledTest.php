<?php

namespace TobMoeller\LaravelMailMiddleware\Tests\Feature\ServiceProvider;

use Illuminate\Contracts\Events\Dispatcher;
use Illuminate\Mail\Events\MessageSending;
use Illuminate\Mail\Events\MessageSent;
use Illuminate\Support\Str;
use ReflectionFunction;
use TobMoeller\LaravelMailMiddleware\Listeners\MessageSendingListener;
use TobMoeller\LaravelMailMiddleware\Listeners\MessageSentListener;
use TobMoeller\LaravelMailMiddleware\Tests\DisabledPackageTestCase;

class EventRegistrationWhenDisabledTest extends DisabledPackageTestCase
{
    public function test_it_does_not_register_mail_event_listeners_when_the_package_is_disabled_at_boot(): void
    {
        self::assertFalse($this->hasListener(MessageSending::class, MessageSendingListener::class));
        self::assertFalse($this->hasListener(MessageSent::class, MessageSentListener::class));
    }

    protected function hasListener(string $event, string $expectedListener): bool
    {
        foreach (app(Dispatcher::class)->getListeners($event) as $listenerClosure) {
            $listener = (new ReflectionFunction($listenerClosure))->getStaticVariables()['listener'] ?? null;

            if (is_string($listener)) {
                if ($listener === $expectedListener) {
                    return true;
                }

                if (Str::contains($listener, '@')) {
                    [$class] = Str::parseCallback($listener);

                    if ($class === $expectedListener) {
                        return true;
                    }
                }
            }

            if (is_array($listener) && ($listener[0] ?? null) === $expectedListener) {
                return true;
            }
        }

        return false;
    }
}
