<?php

declare(strict_types=1);

namespace Haikara\MiddlewareStack;

use Psr\Container\ContainerInterface;

interface RequestHandlerFactoryInterface
{
    /**
     * Containerを用いない場合の生成方法
     * @return ExtendedRequestHandlerInterface
     */
    public static function create(): ExtendedRequestHandlerInterface;

    /**
     * Containerを用いる場合の生成方法。
     * 第二引数にLAZY_RESOLVEを指定する。
     * @param ContainerInterface $container
     * @param bool $lazy_resolve
     * @return ExtendedRequestHandlerInterface
     */
    public function createFromContainer(
        ContainerInterface $container,
        bool $lazy_resolve = ResolverInterface::LAZY_RESOLVE
    ): ExtendedRequestHandlerInterface;

    /**
     * Containerを用いる場合の生成方法。
     * 第二引数にLAZY_RESOLVEを指定する。
     * @param ResolverInterface $resolver
     * @return ExtendedRequestHandlerInterface
     */
    public function createFromResolver(ResolverInterface $resolver): ExtendedRequestHandlerInterface;
}
