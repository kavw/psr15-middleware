<?php

declare(strict_types=1);

namespace Kavw\Psr15Middleware;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;

final class Stack
{
    /**
     * @var MiddlewareInterface[]
     */
    private array $middleware = [];

    public function __construct(
        MiddlewareInterface $coreMiddleware,
        MiddlewareInterface ...$middleware
    ) {
        $this->middleware[] = $coreMiddleware;
        foreach ($middleware as $item) {
            $this->middleware[] = $item;
        }
    }

    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        $next = $handler;
        for ($i = 0; $i < count($this->middleware); $i++) {
            $curr = $this->middleware[$i];
            $wrap = new MiddlewareWrap($curr, $next);
            $next = $wrap;
        }

        return $next->handle($request);
    }
}
