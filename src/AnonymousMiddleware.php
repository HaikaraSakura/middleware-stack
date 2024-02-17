<?php

declare(strict_types=1);

namespace Haikara\MiddlewareStack;

use Closure;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;

class AnonymousMiddleware implements MiddlewareInterface
{
    /**
     * @var Closure
     */
    protected $callback;

    public function __construct(callable $callback)
    {
        $this->callback = Closure::fromCallable($callback);
    }

    /**
     * @inheritDoc
     */
    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        return ($this->callback)($request, $handler);
    }

    public static function createFromRequestHandler(RequestHandlerInterface $handler): AnonymousMiddleware
    {
        return new static(function (ServerRequestInterface $request) use ($handler): ResponseInterface {
            return $handler->handle($request);
        });
    }
}
