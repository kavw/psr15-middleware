<?php

declare(strict_types=1);

namespace Kavw\Psr15Middleware\Tests;

use Kavw\Psr15Middleware\Stack;
use Kavw\Psr15Middleware\Tests\Stuff\AuthMiddleware;
use Kavw\Psr15Middleware\Tests\Stuff\BarMiddleware;
use Kavw\Psr15Middleware\Tests\Stuff\BazMiddleware;
use Kavw\Psr15Middleware\Tests\Stuff\DummyAuthService;
use Kavw\Psr15Middleware\Tests\Stuff\DummyHandler;
use Kavw\Psr15Middleware\Tests\Stuff\EmMiddleware;
use Kavw\Psr15Middleware\Tests\Stuff\FooMiddleware;
use Kavw\Psr15Middleware\Tests\Stuff\MessageCollector;
use Kavw\Psr15Middleware\Tests\Stuff\StrongMiddleware;
use Nyholm\Psr7\ServerRequest;
use PHPUnit\Framework\TestCase;

/**
 * @psalm-suppress UnusedClass
 */
final class MiddlewareTest extends TestCase
{
    public function testCollectingMessagesScenario(): void
    {
        $handler = new DummyHandler();
        $request = new ServerRequest('GET', '/');

        $messageCollector = new MessageCollector();
        $stack = new Stack(
            new FooMiddleware($messageCollector),
            new BarMiddleware($messageCollector),
            new BazMiddleware($messageCollector),
        );


        $result = $stack->process($request, $handler);

        $this->assertEquals(
            'Hello, world!',
            $result->getBody()->getContents(),
            'Checking response body'
        );

        $this->assertEquals(
            [
                'Baz middleware before',
                'Bar middleware before',
                'Foo middleware before',
                'Foo middleware after',
                'Bar middleware after',
                'Baz middleware after',
            ],
            $messageCollector->getMessages(),
            'Checking execution order'
        );
    }

    public function testDecorateResponseScenario(): void
    {
        $handler = new DummyHandler();
        $request = new ServerRequest('GET', '/');

        $stack = new Stack(
            new EmMiddleware(),
            new StrongMiddleware(),
        );

        $result = $stack->process($request, $handler);

        $this->assertEquals(
            '<strong><em>Hello, world!</em></strong>',
            $result->getBody()->getContents(),
            'Checking decorated response'
        );
    }

    public function testAuthenticationScenario(): void
    {
        $handler = new DummyHandler();

        $token = bin2hex(random_bytes(32));
        $authService = new DummyAuthService($token);
        $stack = new Stack(new AuthMiddleware($authService));

        $request = new ServerRequest('GET', '/');
        $result  = $stack->process($request, $handler);

        $this->assertEquals(401, $result->getStatusCode(), 'Check forbidden response');


        $request = new ServerRequest('GET', '/', [DummyAuthService::TOKEN_HEADER => $token]);
        $result  = $stack->process($request, $handler);

        $this->assertEquals(200, $result->getStatusCode(), 'Check OK response');
        $this->assertEquals('Hello, world!', $result->getBody()->getContents(), 'Check OK response body');
    }
}
