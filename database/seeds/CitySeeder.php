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
        $city = new City();
        $city->title = 'Almaty';
        $city->save();

        $city = new City();
        $city->title = 'Aktobe';
        $city->save();

        $city = new City();
        $city->title = 'Nur-Sultan';
        $city->save();

        $city = new City();
        $city->title = 'Karagandy';
        $city->save();
    }
}
