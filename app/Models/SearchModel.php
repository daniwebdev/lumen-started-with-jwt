<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Redis;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;

class SearchModel extends Model {

    // protected $table = 'personal';
    public $storage;

    function __construct()
    {
        $this->storage = Redis::connection();
    }

    function find($query='') {
        $currentPage = isset($_GET['page']) ? (int)$_GET['page'] : 1;

        $result = Cache::remember('result_search_'.$currentPage, 2*60, function() {

            $paginator = DB::table('personal')
            ->select([
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
                "personal.tanggal_ubah",
                "personal.user_buat",
                "personal.keterangan",
                'personal_file_dokumen.gambar_ktp',
                'personal_file_dokumen.gambar_profil',
                ])
            ->join('personal_file_dokumen', 'personal_id', '=', 'personal.id')
            ->paginate(20);
            $paginator->getCollection()->transform(function ($value) {
                $alamat = DB::table('personal_alamat')->where('personal_id', $value->id);
                $alamat->select([
                    'status_rumah_id',
                    'alamat',
                    'detail_lokasi',
                    'koordinat_lokasi',
                    'provinsi_id',
                    'kota_id',
                    'kecamatan_id',
                    'kelurahan_id',
                    'status',
                    'keterangan',
                ]);

                $value->alamat = $alamat->get();

                return $value;
            });

            return $paginator;
        });

        return $result;
    }

}