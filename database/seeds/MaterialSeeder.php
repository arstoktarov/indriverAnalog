<?php

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class MaterialSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('m_types')->delete();
        DB::table('materials')->delete();
        $this->seedTypes();
        $this->seedMaterials(5);
    }

    public function seedTypes() {
        $types = [
            [
                'title' => 'Кирпич',
                'description' => 'Кирпич простой красного цвета',
                'image' => null,
                'charac_title' => 'Объем',
                'charac_unit' => 'м3',
            ],
            [
                'title' => 'Ракушняк',
                'description' => 'Ракушняк простой беловатого цвета',
                'image' => null,
                'charac_title' => 'Объем',
                'charac_unit' => 'м3',
            ],
            [
                'title' => 'Цемент',
                'description' => 'Цемент марки ProfiCem',
                'image' => null,
                'charac_title' => 'Вес на мешок',
                'charac_unit' => 'кг',
            ],
        ];

        foreach ($types as $type) {
            DB::table('m_types')->insert($type);
        }
    }

    public function seedMaterials($count) {
        $types = DB::table('m_types')->get();
        foreach ($types as $type) {
            for ($i = 0; $i < $count; $i++) {
                $material = new \App\Models\Material();
                $material->type_id = $type->id;
                $material->charac_value = 5.5;
                $material->save();
            }
        }
    }
}
