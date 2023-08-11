<?php

namespace Kavw\Psr15Middleware\Tests\Stuff;

final readonly class DummyAuthService
{
    public const TOKEN_HEADER = 'X-Dummy-Auth';

    public function __construct(
        private string $token
    ) {
    }

    public function verify(string $token): bool
    {
        return $this->token === $token;
    }
}
