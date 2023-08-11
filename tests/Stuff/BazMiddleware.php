<?php

namespace Kavw\Psr15Middleware\Tests\Stuff;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;

final readonly class BazMiddleware implements MiddlewareInterface
{
    public function __construct(
        private MessageCollector $collector
    ) {
    }

    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        $this->collector->addMessage('Baz middleware before');
        try {
            return $handler->handle($request);
        } finally {
            $this->collector->addMessage('Baz middleware after');
        }
    }
}
