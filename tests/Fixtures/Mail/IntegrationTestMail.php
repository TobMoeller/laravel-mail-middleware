<?php

namespace TobMoeller\LaravelMailMiddleware\Tests\Fixtures\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;

class IntegrationTestMail extends Mailable
{
    use Queueable;

    public function build(): static
    {
        return $this
            ->subject('Integration Test Mail')
            ->html('<p>Integration Test Body</p>');
    }
}
