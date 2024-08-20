# MiddlewareStack

PSR-15 RequestHandlerInterfaceの実装。  
Middlewareをスタックし、順に実行する。

## 基本的な使い方

ミドルウェアの登録

```php
// $containerはPSR-11:ContainerInterfaceの実装
// 後述のMiddleware1, Middleware2, Middleware3を登録済みのものとする
$handler = RequestHandler::createFromContainer($container);

// 中心になるAction
$action = static function () {
    echo 'Action' . PHP_EOL;
    return new Response();
};

$handler->addMiddleware($action);

// 一括登録
$handler->addMiddlewares([
    Middleware1::class,
    Middleware2::class,
    Middleware3::class,
]);

// 実行
$response = $handler->handle($request);

/*
 * Middleware3, Middleware2, Middleware1, $actionの順で実行される
 */
```

## インスタンス化

PSR-15:ContainerInterfaceを渡す方法。

```php
$handler = RequestHandler::createFromContainer($container);
```

任意の処理を渡す方法。

```php
$handler = new RequestHandler(fn (string $entry) => $entry);
```
