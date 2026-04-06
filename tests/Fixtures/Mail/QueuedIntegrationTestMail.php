<?php

namespace TobMoeller\LaravelMailMiddleware\Tests\Fixtures\Mail;

use Illuminate\Contracts\Queue\ShouldQueue;

class QueuedIntegrationTestMail extends IntegrationTestMail implements ShouldQueue
{
    //
}
