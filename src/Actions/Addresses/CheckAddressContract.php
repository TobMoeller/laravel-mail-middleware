<?php

namespace TobMoeller\LaravelMailMiddleware\Actions\Addresses;

use Symfony\Component\Mime\Address;

interface CheckAddressContract
{
    public function check(Address $address): bool;
}
