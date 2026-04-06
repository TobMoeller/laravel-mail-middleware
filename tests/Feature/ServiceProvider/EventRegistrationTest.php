<?php

use Illuminate\Contracts\Events\Dispatcher;
use Illuminate\Mail\Events\MessageSending;
use Illuminate\Mail\Events\MessageSent;
use Illuminate\Support\Str;
use TobMoeller\LaravelMailMiddleware\Listeners\MessageSendingListener;
use TobMoeller\LaravelMailMiddleware\Listeners\MessageSentListener;

it('registers mail event listeners when the package is enabled at boot', function () {
    $hasListener = function (string $event, string $expectedListener): bool {
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
    };

    expect($hasListener(MessageSending::class, MessageSendingListener::class))->toBeTrue()
        ->and($hasListener(MessageSent::class, MessageSentListener::class))->toBeTrue();
});
