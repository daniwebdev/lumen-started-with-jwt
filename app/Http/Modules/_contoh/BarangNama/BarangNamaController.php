<?php

namespace App\Http\Modules\Master\BarangNama;

use App\Http\Controllers\Controller;
use App\Http\Modules\Master\BarangNama\BarangNamaModel;

class BarangNamaController extends Controller
{

    public function __construct()
    {

    }

    public function index(BarangNamaModel $model) {
        return $model->test();
    }

}
