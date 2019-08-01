<?php

namespace App\Http\Modules\Master\BarangNama;

use App\Http\Controllers\Controller;

use App\Http\Modules\Master\BarangNama\BarangNamaModel;
use Illuminate\Support\Facades\Input;
use Illuminate\Http\Request;

class BarangNamaController extends Controller
{

    public function __construct()
    {

    }

    public function get_all(BarangNamaModel $model) {
        $params = [
            'search'    => Input::get('q'),
            'per_page'  => Input::get('per_page'),
        ];
        
        $data = $model->get_all($params);
    
        return response()->json($data);
    }

    public function get_relation(BarangNamaModel $model) { //fill related field
        $data_relation = $model->get_relation();

        $out['status']  = true;
        $out['message'] = 'Menampilkan data.';
        $out['data']    = $data_relation;

        return response()->json($out);
    }

    public function detail(Request $request, BarangNamaModel $model, $barangNamaId) {
        $data = $model->get_detail($barangNamaId);

        $out['status']  = true;
        $out['message'] = 'Menampilkan data detail.';
        $out['data']    = $data;

        return response()->json($out);

    }

    public function upload(Request $request) {
        $image = $request->file('image');
        $extension     = $image->getClientOriginalExtension();
        $originalName  = $image->getClientOriginalName();

        $name           = time().'--'.str_random(32).'.'.$extension;

        if ($image->move('./tmp', $name)) {
            return response()->json([
                'status'   => true,
                'message'  => 'File berhasil di upload.',
                'filename' => $name
            ], 200);
        } else {
            return response()->json([
                'status' => false,
                'message' => 'File gagal di upload.'
            ], 200);
        }
    }

    public function save(Request $request, BarangNamaModel $model) {
        $save = $model->save_data($request->input());
        
        if($save) {
            $out['status']  = true;
            $out['message'] = 'Berhasil disimpan.';
            $out['data']   = [
                'id' => $save
            ];
        } else {
            $out['status']  = false;
            $out['message'] = 'Gagal disimpan.';
        }
        
        return response()->json($out);
    }

}
