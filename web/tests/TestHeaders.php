<?php

namespace Tests;

class TestHeaders
{

    /**
     * Add headers to all requests.
     *
     */
    public static function testHeader()
    { 
        return [
            "Accept" => "application/json",
            "user-id" => env("SUMRA_ADMIN_USERS")
        ];
    }
}
