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

$router->group(['middleware' => 'cors'], function() use ($router) {
    
    $router->get('/test', function () use ($router) {
        return response()->json([
            'status'=> 'ok'
        ]);
    });

} );


$middleware = ['cors','key-api', 'jwt'];

$router->group(['prefix' => 'utillity', 'middleware' => $middleware], function () use ($router) {

    $router->get('remove_file_tmp', ['uses' => "UtilityController@remove_file_tmp"]);

});