<?php

namespace App\Http\Controllers;

use App\Http\Models\PersonalModel;
use Laravel\Lumen\Http\Request;
use Illuminate\Support\Facades\Input;

class PersonalController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    protected $model;
    public function __construct(PersonalModel $model)
    {
        $this->model = $model;
    }

    public function find(Request $request, PersonalModel $personal) {

        
        $params = [
            'search'    => Input::get('q'),
            'per_page'  => Input::get('per_page'),
        ];
        
        $data = $personal->find($params);
    
        return response()->json($data);
    }

    public function detail(PersonalModel $personal, $personalId) {
        
        $data = $personal->detail($personalId);

        if($data) {
            
            $response['status']     = true;
            $response['message']    = '';
            $response['data']       = $data;
            $code                   = 200;
        } else {
            
            $response['status']     = false;
            $response['message']    = '';
            $response['data']       = $data;
            $code                   = 201;
        }

        return response()->json($response, $code);
    }
}
