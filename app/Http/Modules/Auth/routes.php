<?php

$router->group(['prefix' => 'auth', 'middleware' => ['cors', 'key-api']], function() use ($router) {
    $controller     = '\App\Http\Modules\Auth\AuthController';

    $router->post('login', ['uses' => $controller.'@login']);
});
