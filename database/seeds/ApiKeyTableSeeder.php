<?php

use Illuminate\Database\Seeder;
use App\ApiKey;

class ApiKeyTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $key            = new ApiKey();
        $key->key_name  = 'API-TEST';
        $key->secret    = str_random(16);
        $key->save();
    }
}
