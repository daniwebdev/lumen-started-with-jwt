<?php

$router->group(['prefix' => 'personal', 'middleware' => $middleware], function () use ($router) {
    
    $module     = '\App\Http\Modules';
    $controller = $module.'\Personal\PersonalController';

    $router->post('find', ['uses' => "$controller@get_all"]);
    $router->get('{personalId}/detail', ['uses' => "$controller@detail"]);

});