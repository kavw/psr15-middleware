<?php

namespace Kavw\Psr15Middleware\Tests\Stuff;

use Nyholm\Psr7\Response;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;

final readonly class EmMiddleware implements MiddlewareInterface
{
    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        $res = $handler->handle($request);
        return new Response(
            $res->getStatusCode(),
            $res->getHeaders(),
            "<em>{$res->getBody()->getContents()}</em>"
        );
    }
}
