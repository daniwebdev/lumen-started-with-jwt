<?php

namespace App\Http\Modules\Master\BarangNama;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Redis;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;

class BarangNamaModel extends Model {

    public    $storage;    
                                //  Sec   Min      
    protected $cache_expire     =  (1*60) *10; //satuan detik
    protected $per_page         = 10;

    protected $table = 'barang_nama';

    protected $redis_module_key = 'barang_nama';

    function __construct()
    {
        $this->storage = Redis::connection();
    }

    function field_selected() {
        $select = [
            'bn.id',
            'bn.nama_barang',
            'bn.sku_supplier',
            'bn.spesifikasi',
            'bn.deskripsi',
            'bn.status',
            'bn.gambar_id',
            'bn.produk_id',
            'bn.merek_id',
            'bn.kategori_id',
            'bn.kategori_web_id',
            'bn.type',
            'bn.status_sku_supplier',
            'bn.status_serial_number',
            DB::raw('(SELECT gambar_1       FROM barang_gambar bg        WHERE bg.id = bn.gambar_id)     as image'),
            DB::raw('(SELECT nama_produk    FROM barang_produk bp        WHERE bp.id = bn.produk_id)     as nama_produk'),
            DB::raw('(SELECT nama_merek     FROM barang_merek bm         WHERE bm.id = bn.merek_id)       as nama_merek'),
            DB::raw('(SELECT nama_kategori  FROM barang_kategori bk      WHERE bk.id = bn.kategori_id) as nama_kategori'),
            DB::raw('(SELECT nama_kategori_web  FROM barang_kategori_web bkw WHERE bkw.id = bn.kategori_web_id) as nama_kategori_web'),
        ];

        return $select;
    }

    function get_all($params) {
         //create key for redis
        $key = isset($_GET['page']) ? (int)$_GET['page'] : 1;
        
        //search key for redis
        if(isset($params['search'])) {
            $key = $this->redis_module_key.':'.str_replace(' ', '_', $params['search']).'_'.$key;
        }

        //database caching key = get:nama_module_(params)_(page)
        $result = Cache::remember('get:'.$key, $this->cache_expire, function() use ($params) {
            return $this->get_query($params);
        });

        return $result;
    }

    function get_query() {
        
        $db = DB::table('barang_nama AS bn');

        $db->select($this->field_selected());
        $db->where('bn.is_trash', '=', '0');
        $paginator = $db->paginate($this->per_page);

        $paginator->getCollection()->transform(function ($value) {
            $value->image = env('FILES_URL').'barang_nama/small/'.$value->image;
            return $value;
        });

        return $paginator;
    }

    function get_detail($id) {

        $key = 'barang_nama:'.$id;
        $result = Cache::remember($key, $this->cache_expire, function() use ($id) {
            $data = DB::table($this->table.' AS bn')->select($this->field_selected())
            ->where('id', '=', $id);
            $result = $data->first();

            $result->images = $this->get_gambar($result->gambar_id);

            return $result;
        });

        return $result;
    }

    function get_relation() {
        $output['merek']            = $this->get_merek();
        $output['produk']           = $this->get_produk();
        $output['kategori']         = $this->get_kategori();
        $output['kategori_web']     = $this->get_kategori_web();


        return $output;
    }

    function get_merek() {
        
        return Cache::remember('get_merek', $this->cache_expire, function() {
            $merek = DB::table('barang_merek');
            $merek->select([
                'id',
                'nama_merek AS text',
            ]);
            $merek->orderBy('nama_merek');
            return $merek->get();
        });

    }

    function get_produk() {
        
        return Cache::remember('get_produk', $this->cache_expire, function() {
            $produk = DB::table('barang_produk');
            $produk->select([
                'id',
                'nama_produk AS text',
            ]);
            $produk->orderBy('nama_produk');
            return $produk->get();
        });
    }

    function get_kategori() {
        
        return Cache::remember('get_kategori', $this->cache_expire, function() {
            $produk = DB::table('barang_kategori');
            $produk->select([
                'id',
                'nama_kategori AS text',
            ]);
            $produk->orderBy('nama_kategori');
            return $produk->get();
        });
    }

    function get_kategori_web() {
        
        return Cache::remember('get_kategori_web', $this->cache_expire, function() {
            $produk = DB::table('barang_kategori_web');
            $produk->select([
                'id',
                'nama_kategori_web AS text',
            ]);
            $produk->orderBy('nama_kategori_web');
            return $produk->get();
        });
    }

    function get_gambar($id) {
        $gambar = DB::table('barang_gambar AS gb')
        ->select([
            'gb.gambar_1',
            'gb.gambar_2',
            'gb.gambar_3',
            'gb.gambar_4',
        ])
        ->where('id', '=', $id);
        
        $output = [];
        foreach($gambar->first() as $key => $value) {
            $output[$key] = env('FILES_URL').'barang_nama/small/'.$value;
        }

        return $output;
    }

    function save_data($request) {
        $data = $request;
        $save = DB::table($this->table);
        
        unset($data['key']);
        unset($data['auth']);

        if(isset($data['id'])) {
            $id = $data['id'];
            unset($data['id']);
            
            $save->where('id', '=', $id)->update($data);
            return $id;

        } else {
            return $save->insertGetId($data);
        }
    }
}