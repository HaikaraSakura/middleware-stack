<?php

declare(strict_types=1);

namespace Haikara\MiddlewareStack;

use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;

interface ExtendedRequestHandlerInterface extends RequestHandlerInterface
{
    /**
     * Middlewareをスタックに追加する
     * @param callable|string|MiddlewareInterface|RequestHandlerInterface $entry
     * @return static
     */
    public function addMiddleware(
        callable|MiddlewareInterface|RequestHandlerInterface|string $entry
    ): ExtendedRequestHandlerInterface;
}
