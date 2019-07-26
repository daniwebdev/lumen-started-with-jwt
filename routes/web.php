<?php

/*
|--------------------------------------------------------------------------
| Application Routes
|--------------------------------------------------------------------------
|
| Here is where you can register all of the routes for an application.
| It is a breeze. Simply tell Lumen the URIs it should respond to
| and give it the Closure to call when that URI is requested.
|
*/
$router->get('/', function () use ($router) {
    return $router->app->version();
});

$router->group(['prefix' => 'auth', 'middleware' => 'cors'], function() use ($router) {
    $router->post('login', ['uses' => 'AuthController@login']);
});


$middleware = ['cors','key-api', 'jwt'];

$router->group(['prefix' => 'personal', 'middleware' => $middleware], function () use ($router) {

    $router->get('search', ['uses' => 'PersonalController@find']);
    $router->get('{personalId}/detail', ['uses' => 'PersonalController@detail']);
    
});