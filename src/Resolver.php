<?php

declare(strict_types=1);

namespace Haikara\MiddlewareStack;

use LogicException;
use Psr\Container\ContainerInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;

class Resolver implements ResolverInterface
{
    /**
     * @param ?ContainerInterface $container
     * @param bool $lazyResolve trueなら登録時に、falseなら呼び出し時に解決をおこなうか
     */
    protected function __construct(
        protected ?ContainerInterface $container = null,
        protected bool $lazyResolve = self::LAZY_RESOLVE
    ) {
    }

    public static function create(): Resolver
    {
        return new static();
    }

    public static function createWithContainer(
        ContainerInterface $container,
        bool $lazyResolve = self::LAZY_RESOLVE
    ): Resolver {
        return new static($container, $lazyResolve);
    }

    /**
     * @inheritDoc
     */
    public function resolve(
        callable|MiddlewareInterface|ExtendedRequestHandlerInterface|string $entry
    ): MiddlewareInterface
    {
        // MiddlewareInterfaceのインスタンスならそのまま帰す
        if ($entry instanceof MiddlewareInterface) {
            return $entry;
        }

        if ($entry instanceof RequestHandlerInterface) {
            return AnonymousMiddleware::createFromRequestHandler($entry);
        }

        if (is_callable($entry)) {
            return new AnonymousMiddleware($entry);
        }

        if (!isset($this->container)) {
            throw new LogicException('文字列\'$entry\'を解決するにはContainerInterfaceの実装をセットすることが必要です。');
        }

        // LAZY_RESOLVEモードなら、AnonymousMiddlewareを用いて、Containerでの解決を遅延評価する
        if ($this->lazyResolve) {
            return new AnonymousMiddleware(function ($request, $handler) use ($entry) {
                $middleware = $this->container->get($entry);

                if (!$middleware instanceof MiddlewareInterface) {
                    $middleware = $this->resolve($middleware);
                }

                return $middleware->process($request, $handler);
            });
        }

        // LAZY_RESOLVEモードでなければ、Containerでの解決を実行する
        $middleware = $this->container->get($entry);

        if (!$middleware instanceof MiddlewareInterface) {
            $this->resolve($middleware);
        }

        return $middleware;
    }
}
