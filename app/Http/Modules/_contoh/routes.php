<?php
/* 
    $middleware from routes/web.php
*/

$router->group(['prefix' => 'master', 'middleware' => $middleware], function () use ($router) {
    
    $module     = '\App\Http\Modules\Master';
    
    $router->group(['prefix' => 'barang_nama'], function () use ($router, $module) {
        
        $controller = $module.'\BarangNama\BarangNamaController';
        $router->get('get', ['uses' => "$controller@index"]);

    });


});