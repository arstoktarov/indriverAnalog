<?php

use App\Models\City;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class CitySeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('cities')->delete();
        $city = new City();
        $city->title = 'Almaty';
        $city->lat = 43.238949;
        $city->long = 76.889709;
        $city->save();

        $city = new City();
        $city->title = 'Aktau';
        $city->lat = 43.693695;
        $city->long = 51.260834;
        $city->save();

        $city = new City();
        $city->title = 'Nur-Sultan';
        $city->lat = 51.169392;
        $city->long = 71.449074;
        $city->save();

        $city = new City();
        $city->title = 'Karagandy';
        $city->lat = 49.838322;
        $city->long = 73.115601;
        $city->save();
    }
}
