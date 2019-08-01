<?php

namespace  App\Http\Modules\Master\BarangNama;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Redis;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;

class BarangNamaModel extends Model {

    public    $storage;    
                                //  Sec   Min      
    protected $cache_expire     =  (1*60) *10; //satuan detik
    protected $per_page         = 10;

    function __construct()
    {
        //redis connection
        $this->storage = Redis::connection();
    }

    function test() {
        return "teset model";
    }
}