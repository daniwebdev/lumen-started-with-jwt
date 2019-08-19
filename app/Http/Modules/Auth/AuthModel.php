<?php

namespace App\Http\Modules\Auth;

use Illuminate\Database\Eloquent\Model;


class AuthModel extends Model {

    public    $storage;
    protected $cache_expire = 2*60;

    protected $table = 'users';

    protected $hidden = [
        'password',
    ];

    function __construct() {

    }

    function check($username) {
        $user = $this->where('username', '=', $username)->first();
        return $user;
    }

}