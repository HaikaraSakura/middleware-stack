<?php

declare(strict_types=1);

namespace Haikara\MiddlewareStack;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;
use SplStack;

class RequestHandler implements ExtendedRequestHandlerInterface
{
    /**
     * @var ResolverInterface
     */
    protected ResolverInterface $resolver;

    /**
     * @var SplStack<MiddlewareInterface>
     */
    protected SplStack $stack;

    public function __construct(?ResolverInterface $resolver = null)
    {
        $this->resolver = $resolver ?? Resolver::create();
        $this->stack = new SplStack();
    }

    /**
     * @inheritDoc
     */
    public function addMiddleware(
        callable|MiddlewareInterface|RequestHandlerInterface|string $entry
    ): ExtendedRequestHandlerInterface
    {
        $middleware = $this->resolver->resolve($entry);

        $this->stack->push($middleware);

        return $this;
    }

    /**
     * 複数のMiddlewareをまとめてスタックに追加する
     * @param iterable<string|MiddlewareInterface|RequestHandlerInterface|callable> $middlewares
     * @return void
     */
    public function addMiddlewares(iterable $middlewares): void
    {
        foreach ($middlewares as $middleware) {
            $this->addMiddleware($middleware);
        }
    }

    /**
     * @inheritDoc
     */
    public function handle(ServerRequestInterface $request): ResponseInterface
    {
        $middleware = $this->stack->pop();

        return $middleware->process($request, $this);
    }
}
