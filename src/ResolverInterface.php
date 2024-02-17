<?php

declare(strict_types=1);

namespace Haikara\MiddlewareStack;

use Psr\Http\Server\MiddlewareInterface;

interface ResolverInterface
{
    public const LAZY_RESOLVE = true;

    /**
     * @param callable|string|ExtendedRequestHandlerInterface|MiddlewareInterface $entry $entry
     * @return MiddlewareInterface
     */
    public function resolve(
        callable|MiddlewareInterface|ExtendedRequestHandlerInterface|string $entry
    ): MiddlewareInterface;
}
