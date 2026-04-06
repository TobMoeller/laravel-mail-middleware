<?php

namespace TobMoeller\LaravelMailMiddleware\Tests;

class DisabledPackageTestCase extends TestCase
{
    public function getEnvironmentSetUp($app)
    {
        parent::getEnvironmentSetUp($app);

        config()->set('mail-middleware.enabled', false);
    }
}
