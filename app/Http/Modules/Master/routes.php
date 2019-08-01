<?php
/* 
    $middleware from routes/web.php
*/

$router->group(['prefix' => 'master', 'middleware' => $middleware], function () use ($router) {
    
    $module     = '\App\Http\Modules\Master';
    
    $router->group(['prefix' => 'barang_nama'], function () use ($router, $module) {
        
        $controller = $module.'\BarangNama\BarangNamaController';

        $router->post('find',           ['uses' => "$controller@get_all"]);
        $router->post('upload',         ['uses' => "$controller@upload"]);
        $router->post('save',           ['uses' => "$controller@save"]);
        
        $router->get('get_relation',    ['uses' => "$controller@get_relation"]);
        $router->get('detail/{barangNamaId}',     ['uses' => "$controller@detail"]);

    });

});