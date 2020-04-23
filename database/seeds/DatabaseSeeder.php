<?php

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
         $this->call(CitySeeder::class);
         $this->call(MaterialSeeder::class);
         $this->call(TechnicSeeder::class);
         $this->call(UserSeeder::class);
    }
}
