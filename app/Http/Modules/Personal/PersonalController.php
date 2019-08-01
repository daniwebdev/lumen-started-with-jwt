<?php

namespace App\Http\Modules\Personal;

use App\Http\Controllers\Controller;

use App\Http\Modules\Personal\PersonalModel;
use Illuminate\Http\Request;
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

    public function get_all(Request $request, PersonalModel $personal) {

        $params = [
            'search'    => Input::get('q'),
            'per_page'  => Input::get('per_page'),
            'filter'    => $request->input('filter')
        ];
        
        $data = $personal->get_all($params);
    
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
