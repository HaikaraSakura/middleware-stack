<?php

declare(strict_types=1);

namespace Haikara\MiddlewareStack;

use Psr\Container\ContainerInterface;

class RequestHandlerFactory implements RequestHandlerFactoryInterface
{
    /**
     * @inheritDoc
     */
    public static function create(): ExtendedRequestHandlerInterface
    {
        return new RequestHandler();
    }

    /**
     * @inheritDoc
     */
    public function createFromContainer(
        ContainerInterface $container,
        bool $lazy_resolve = ResolverInterface::LAZY_RESOLVE
    ): ExtendedRequestHandlerInterface {
        $resolver = Resolver::createWithContainer($container, $lazy_resolve);
        return new RequestHandler($resolver);
    }

    /**
     * @inheritDoc
     */
    public function createFromResolver(ResolverInterface $resolver): ExtendedRequestHandlerInterface
    {
        return new RequestHandler($resolver);
    }
}
