<?php

namespace TobMoeller\LaravelMailMiddleware\Facades;

use Illuminate\Support\Facades\Facade;

/**
 * @see \TobMoeller\LaravelMailMiddleware\LaravelMailMiddleware
 */
class LaravelMailMiddleware extends Facade
{
    protected static function getFacadeAccessor(): string
    {
        return \TobMoeller\LaravelMailMiddleware\LaravelMailMiddleware::class;
    }
}
