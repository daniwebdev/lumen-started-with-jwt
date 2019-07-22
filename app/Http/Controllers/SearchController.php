<?php

namespace App\Http\Controllers;
use App\Models\SearchModel;
use Illuminate\Support\Facades\DB;


class SearchController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    public function find(SearchModel $search) {
        
        DB::connection()->enableQueryLog();
        $data = $search->find();
        
        $q =DB::getQueryLog();

        // return response()->json($q);
        return response()->json($data);
        // return count($data);
    }

    //
}
