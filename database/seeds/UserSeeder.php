<?php

use Illuminate\Database\Seeder;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $users = [
            [
                'type' => 1,
                'name' => 'Arsen Toktarov',
                'phone' => '87476527708',
                'phone_verified_at' => \Carbon\Carbon::now(),
                'city_id' => 1,
                'password' => '123456',
                'token' => 'HQYJDYABBqchOByNehkPnhYSGjjr0q'
            ],
        ];

        foreach ($users as $userModel) {
            $user = new \App\Models\User();
            $user->type = $userModel['type'];
            $user->name = $userModel['name'];
            $user->phone = $userModel['phone'];
            $user->phone_verified_at = $userModel['phone_verified_at'];
            $user->city_id = $userModel['city_id'];
            $user->password = $userModel['password'];
            $user->token = $userModel['token'];
            $user->save();
        }
    }
}
