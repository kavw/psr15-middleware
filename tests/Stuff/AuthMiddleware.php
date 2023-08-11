<?php

namespace Kavw\Psr15Middleware\Tests\Stuff;

use Nyholm\Psr7\Response;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;

final readonly class AuthMiddleware implements MiddlewareInterface
{
    public function __construct(
        private DummyAuthService $authService
    ) {
    }

    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        $token = $request->getHeaderLine(DummyAuthService::TOKEN_HEADER);
        if (!$token || !$this->authService->verify($token)) {
            return new Response(401);
        }

        return $handler->handle($request);
    }
}
