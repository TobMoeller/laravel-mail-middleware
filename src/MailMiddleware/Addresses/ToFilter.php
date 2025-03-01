<?php

namespace TobMoeller\LaravelMailMiddleware\MailMiddleware\Addresses;

use TobMoeller\LaravelMailMiddleware\Actions\Addresses\IsAllowedRecipient;
use TobMoeller\LaravelMailMiddleware\Enums\Header;

class ToFilter extends AddressFilter
{
    public function __construct(IsAllowedRecipient $addressChecker)
    {
        parent::__construct(
            Header::TO,
            $addressChecker,
        );
    }
}
