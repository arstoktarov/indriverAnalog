<?php

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('users')->delete();
        $users = [
            [
                'type' => 1,
                'name' => 'Arsen Toktarov',
                'phone' => '87476527708',
                'phone_verified_at' => \Carbon\Carbon::now(),
                'city_id' => DB::table('cities')->first()->id,
                'password' => '123456',
                'token' => 'HQYJDYABBqchOByNehkPnhYSGjjr0q'
            ],
        ];

        foreach ($users as $userModel) {
            DB::table('users')->insert($userModel);
        }
    }
}
