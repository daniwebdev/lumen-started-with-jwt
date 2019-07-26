<?php

namespace App\Http\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Redis;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;

class AuthModel extends Model {

    // protected $table = 'personal';
    public    $storage;
    protected $cache_expire = 2*60;

    protected $table = 'users';

    function __construct() {
        // $this->storage = Redis::connection();
    }

    function check($username) {
        $user = DB::table('users');
        $user->select([
            "id",
            "id_firebase_token",
            "id_firebase",
            "id_karyawan",
            "id_personal",
            "username",
            "fullname",
            "email",
            "password",
            "id_relasi_cabang",
            "unitbisnis_level_id",
            "ip_address",
            "nama_panggilan",
            "status_aktif",
            "last_login",
            "status",
        ])->where('username', '=', $username);
        $user = $user->first();
        
        return $user;
    }

}