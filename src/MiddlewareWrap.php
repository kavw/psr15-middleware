<?php

namespace Kavw\Psr15Middleware;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;

final readonly class MiddlewareWrap implements RequestHandlerInterface
{
    public function __construct(
        private MiddlewareInterface $current,
        private RequestHandlerInterface $next
    ) {
    }

    public function handle(ServerRequestInterface $request): ResponseInterface
    {
        return $this->current->process($request, $this->next);
    }
}
