<?php

namespace App\Http\Controllers;

use Firebase\JWT\JWT;
use Illuminate\Support\Facades\Hash;
use App\Http\Models\AuthModel;
use Illuminate\Http\Request;

class AuthController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {

    }

    public function login(AuthModel $model, Request $request) {

        $username     = $request->username;
        $password     = $request->password;

        $user         = $model->check($username);
        
        $passwordHash = isset($user->password) ? $user->password:'';
        $check        = Hash::check($password, $passwordHash);
        
        if($check) {

            unset($user->password);
            unset($user->id_firebase);
            unset($user->id_firebase_token);

            $output['status']   = true;
            $output['message']  = 'Authentication Successfull.';
            $output['key']      = $request->key;
            $output['token']    = encrypt($this->jwt($user));
            $output['user']     = $user;
        } else {

            $output['status']   = false;
            $output['message']  = 'Authentication Error.';

        }

        return response()->json($output);
    }

    protected function jwt($user) {
        $payload = [
            'iss'   => "lumen-jwt",     // Issuer of the token
            'iat'   => time(),          // Time when JWT was issued. 
            'exp'   => time() + 60*60,  // Expiration time
            'user'  => [
                'id_user'           => $user->id,
                'id_karyawan'       => $user->id_karyawan,
                'id_personal'       => $user->id_personal,
                'username'          => $user->username,
                'id_relasi_cabang'  => $user->id_relasi_cabang,
            ],
        ];
        
        // As you can see we are passing `JWT_SECRET` as the second parameter that will 
        // be used to decode the token in the future.
        return JWT::encode($payload, env('JWT_SECRET'));
    } 

}
