<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Input;

class UtilityController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {

    }

    public function remove_file_tmp() {
        $file_name = Input::get('file_name');
        if(file_exists('./tmp/'.$file_name)) {
            unlink('./tmp/'.$file_name);
            return response()->json([
                'status' => true,
                'message' => "File berhasil di hapus."
            ]);
        } else {
            return response()->json([
                'status' => true,
                'message' => "File $file_name tidak ditemukan."
            ]);
        }

    }
}
