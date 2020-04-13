<?php

use Illuminate\Database\Seeder;

class MaterialSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        for ($i = 0; $i < 10; $i++) {
            $material = new \App\Models\Material();
            $material->type_id = 5;
            $material->title = 'Material #'.$i;
            $material->avatar = null;
            $material->brand = 'Brand';
            $material->description = 'description';
            $material->save();
        }
    }
}
