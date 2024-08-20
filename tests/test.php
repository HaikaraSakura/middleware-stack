<?php

declare(strict_types=1);

use Haikara\MiddlewareStack\RequestHandler;
use Laminas\Diactoros\Response;
use Laminas\Diactoros\ServerRequestFactory;
use League\Container\Container;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;

require_once __DIR__ . '/../vendor/autoload.php';

class Middleware1 implements MiddlewareInterface
{
    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        echo static::class . '前処理' . PHP_EOL;
        $response = $handler->handle($request);
        echo static::class . '後処理' . PHP_EOL;
        return $response;
    }
}

class Middleware2 implements MiddlewareInterface
{
    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        echo static::class . '前処理' . PHP_EOL;
        $response = $handler->handle($request);
        echo static::class . '後処理' . PHP_EOL;
        return $response;
    }
}

class Middleware3 implements MiddlewareInterface
{
    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        echo static::class . '前処理' . PHP_EOL;
        $response = $handler->handle($request);
        echo static::class . '後処理' . PHP_EOL;
        return $response;
    }
}

// 中心になるAction
$action = static function () {
    echo 'Action' . PHP_EOL;
    return new Response();
};

$container = new Container();

$container->add(Middleware1::class, function () {
    return new Middleware1;
});

$container->add(Middleware2::class, function () {
    return new Middleware2;
});

$container->add(Middleware3::class, function () {
    return new Middleware3;
});

// ActionとMiddlewareをRequestHandlerにセット
$layer = RequestHandler::createFromContainer($container);
$layer->addMiddlewares([
    $action,
    Middleware1::class,
    Middleware2::class
]);

// Middlewareを追加
$layer->addMiddleware(Middleware3::class);

$layer->addMiddleware(static function ($request, $handler) {
    echo 'Closure1前処理' . PHP_EOL;
    $response = $handler->handle($request);
    echo 'Closure1後処理' . PHP_EOL;
    return $response;
});

// Requestを渡してRequestHandlerを実行
$request = ServerRequestFactory::fromGlobals();
$response = $layer->handle($request);

echo $response->getBody();
