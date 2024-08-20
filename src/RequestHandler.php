<?php

declare(strict_types=1);

namespace Haikara\MiddlewareStack;

use Closure;
use Psr\Container\ContainerInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;
use RuntimeException;
use SplStack;

class RequestHandler implements RequestHandlerInterface
{
    protected Closure $resolver;

    /**
     * @var SplStack<MiddlewareInterface|RequestHandlerInterface|callable|string>
     */
    protected SplStack $stack;

    public function __construct(callable $resolver)
    {
        $this->resolver = Closure::fromCallable($resolver);
        $this->stack = new SplStack();
    }

    /**
     * Containerを用いる場合の生成方法。
     * @param ContainerInterface $container
     * @return RequestHandlerInterface
     */
    public static function createFromContainer(ContainerInterface $container): static {
        return new RequestHandler(
            fn ($entry) => $container->get($entry)
        );
    }

    /**
     * @param MiddlewareInterface|RequestHandlerInterface|callable|string $entry
     * @return $this
     */
    public function addMiddleware(MiddlewareInterface|RequestHandlerInterface|callable|string $entry): static
    {
        $this->stack->push($entry);

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
        $entry = $this->stack->pop();
        $middleware = is_string($entry) ? ($this->resolver)($entry) : $entry;

        if ($middleware instanceof MiddlewareInterface) {
            return $middleware->process($request, $this);
        }

        if ($middleware instanceof RequestHandlerInterface) {
            return $middleware->handle($request);
        }

        if (is_callable($middleware)) {
            return $middleware($request, $this);
        }

        throw new RuntimeException("{$entry}はミドルウェアとして解決できませんでした。MiddlewareInterfaceかRequestHandlerInterfaceの実装、もしくはcallable値を渡してください。");
    }
}
