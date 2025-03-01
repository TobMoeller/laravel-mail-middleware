<?php

use Symfony\Component\Mime\Email;
use TobMoeller\LaravelMailMiddleware\Actions\Addresses\IsAllowedRecipient;
use TobMoeller\LaravelMailMiddleware\Enums\Header;
use TobMoeller\LaravelMailMiddleware\MailMiddleware\Addresses\CcFilter;
use TobMoeller\LaravelMailMiddleware\MailMiddleware\MessageContext;

it('creates an address filter for "to" with IsAllowedRecipient address checker', function () {
    $filter = app(CcFilter::class);

    expect($filter)
        ->header->toBe(Header::CC)
        ->addressChecker->toBeInstanceOf(IsAllowedRecipient::class);
});

it('creats a log entry', function () {
    $mail = new Email;
    $context = new MessageContext($mail);

    $filter = app(CcFilter::class);
    $filter->handle($context, fn () => null);

    expect($context->getLog()[0])
        ->toBe(CcFilter::class);
});
