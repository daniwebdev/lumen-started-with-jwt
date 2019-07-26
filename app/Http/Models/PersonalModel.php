<?php

namespace App\Http\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Redis;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;

class PersonalModel extends Model {

    public    $storage;    
                            //  Sec   Min      
    protected $cache_expire     =  (1*60) *10; //satuan detik
    protected $per_page         = 10;

    function __construct()
    {
        //redis connection
        $this->storage = Redis::connection();
    }

    function find($params=[]) {

        //create key for redis
        $key = isset($_GET['page']) ? (int)$_GET['page'] : 1;
        
        //search key for redis
        if(isset($params['search'])) {
            $key = 'search_'.str_replace(' ', '_', $params['search']).'_'.$key;
        }

        //database caching
        $result = Cache::remember('result_'.$key, $this->cache_expire, function() use ($params) {
            return $this->find_query($params);
        });

        return $result;
    }

    function find_query($params) {

        $select   = $this->field_selected();
        
        $select[] = DB::raw('(SELECT COUNT(kmo.id) FROM karyawan_mutasi_organisasi kmo WHERE kmo.personal_id = personal.id AND kmo.status_karyawan_mutasi = 1) as related_kmo');
        $select[] = DB::raw('(SELECT COUNT(users.id) FROM users WHERE id_karyawan =  (SELECT TOP 1 kmo.id FROM karyawan_mutasi_organisasi kmo WHERE kmo.personal_id = personal.id AND kmo.status_karyawan_mutasi = 1)) as related_user');

        $paginator = DB::table('personal')
                         ->select($select);
                        //  ->join('personal_file_dokumen', 'personal_id', '=', 'personal.id');
        
        if(isset($params['search'])) {
            $s = $params['search'];
            $paginator->where('personal.nama_lengkap', 'LIKE', "%$s%");
        }
        
        $paginator = $paginator->paginate($this->per_page);

        $paginator->getCollection()->transform(function ($value) {
            $value->alamat = $this->alamat_personal($value->id);
            return $value;
        });

        return $paginator;
    }

    function detail($personalId) {
        
        $this->cache_expire = $this->cache_expire*3;

        $key    = 'det_personal_'.$personalId;
        $result = Cache::remember($key, $this->cache_expire, function() use($personalId) {

            $select   = $this->field_selected();

            $query = DB::table('personal')
                    ->select($select)
                    ->where('personal.id', '=', $personalId)
                    ->join('personal_file_dokumen', 'personal_id', '=', 'personal.id');

            $personal             = $query->first();

            $output['personal']   = $personal;
            $output['alamat']     = $this->alamat_personal($personal->id);
            $output['karyawan']   = $this->karyawan_personal($personal->id);
            $output['konsumen']   = $this->konsumen_personal($personal->id);

            return $output;
        });
        return $result;
    }

    function field_selected() {
        $select = [
            "personal.id",
            "personal.tipe_personal",
            "personal.no_ktp",
            "personal.gambar",
            "personal.nama_lengkap",
            "personal.nama_panggilan",
            "personal.tempat_lahir",
            "personal.tgl_lahir",
            "personal.jenis_kelamin_id",
            "personal.agama_id",
            "personal.golongan_darah_id",
            "personal.kewarganegaraan_id",
            "personal.daerahasal_suku",
            "personal.telepon_1",
            "personal.telepon_2",
            "personal.email_1",
            "personal.email_2",
            "personal.status",
            "personal.tanggal_buat",
            "personal.user_buat",
            "personal.keterangan",
            // 'personal_file_dokumen.gambar_ktp',
            // 'personal_file_dokumen.gambar_profil'
        ];

        return $select;
    }

    function alamat_personal($personalId) {
        $alamat = DB::table('personal_alamat AS alamat')->where('alamat.personal_id', $personalId);
        $alamat->select([
            'alamat.status_rumah_id',
            'alamat.alamat',
            'alamat.detail_lokasi',
            'alamat.koordinat_lokasi',
            'alamat.provinsi_id',
            'alamat.kota_id',
            'alamat.kecamatan_id',
            'alamat.kelurahan_id',
            'alamat.status',
            'alamat.keterangan',
            DB::raw('(SELECT CONCAT(kota.kota_kab, \'. \', kota.nama_kota) FROM regional_kota kota WHERE kota.id = alamat.kota_id ) as nama_kota'),
            DB::raw('(SELECT prov.nama_provinsi FROM regional_provinsi prov WHERE prov.id = alamat.provinsi_id ) as nama_provinsi'),
        ]);
        $alamat->orderBy('status');
        return $alamat->get();
    }

    function karyawan_personal($personalId) {
        $select = [
            'kmo.id',
            'kmo.karyawan_id',
            'kmo.tipe_mutasi_id',
            'kmo.kd_karyawan',
            'kmo.unitbisnis_mutasi_id',
            DB::raw('(SELECT nama_unitbisnis FROM unitbisnis_nama WHERE id = kmo.unitbisnis_mutasi_id) as nama_unitbisnis_mutasi'),
            'kmo.departemen_id',
            'kmo.jabatan_id',
            DB::raw('convert(varchar, kmo.tanggal_mutasi, 20) as tanggal_mutasi'),
            'kmo.keterangan_mutasi',
            'kmo.tanggal_resign',
            'kmo.keterangan_resign',
            'kmo.status_karyawan_mutasi',
            'kmo.acc_piutang_usaha_id',
            'kmo.acc_piutang_kas_id',
        ];

        $select[] = DB::raw('(SELECT nama FROM karyawan_struktur_organisasi kso WHERE kso.id = kmo.jabatan_id) as nama_jabatan');
            
        $karyawan = DB::table('karyawan_mutasi_organisasi AS kmo');
        $karyawan->select($select);
        $karyawan->where('kmo.personal_id', '=', $personalId);
        $karyawan->where('kmo.status_karyawan_mutasi', '=', '1');

        return $karyawan->first();

    }

    function konsumen_personal($personalId) {
        $select = [
            'kons.id',
            'kons.tgl_input',
            'kons.sales_id',
            'kons.konsumen_prospek_id',
            'kons.personal_id',
            'kons.type_konsumen_id',
            'kons.unitbisnis_id',
            'kons.kd_konsumen',
            'kons.kd_konsumen_str',
            'kons.pdmpg_id',
            DB::raw('(SELECT CONCAT(nama_lengkap, \'|\', telepon_1) as data_pendamping FROM personal WHERE id = kons.pdmpg_id) as nama_pendamping'),

            'kons.hubungan_pendamping_id',
            DB::raw('(SELECT nama_administratif FROM informasi_administratif WHERE id = kons.hubungan_pendamping_id) as hubungan_pendamping'),
            
            'kons.keterangan',
            'kons.status',
            'kons.tanggal_buat',
            'kons.is_trash',
            'kons.sales_personal_id',
            'kons.status_bebas_order',
            'kons.masa_bebas_order',
        ];

        $konsumen = DB::table('konsumen AS kons');
        $konsumen->select($select);
        $konsumen->where('personal_id', '=', $personalId);

        return $konsumen->get();
    }

}