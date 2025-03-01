<?php

namespace TobMoeller\LaravelMailMiddleware\MailMiddleware\Addresses;

use TobMoeller\LaravelMailMiddleware\Actions\Addresses\IsAllowedRecipient;
use TobMoeller\LaravelMailMiddleware\Enums\Header;

class CcFilter extends AddressFilter
{
    public function __construct(IsAllowedRecipient $addressChecker)
    {
        parent::__construct(
            Header::CC,
            $addressChecker,
        );
    }
}
