<?php

use App\Models\Technic;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class TechnicSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('t_types')->delete();
        DB::table('technics')->delete();

        $this->seedTypes();
        $this->seedTechnics(5);

    }

    public function seedTypes() {
        $types = [
            [
                'title' => 'Экскаватр',
                'description' => 'Экскаватр',
                'image' => null,
                'charac_title' => 'Объем ковша',
                'charac_unit' => 'м3',
            ],
            [
                'title' => 'Экскаватор на автомобильном шасси',
                'description' => 'Экскаватор на автомобильном шасси',
                'image' => null,
                'charac_title' => 'Объем ковша',
                'charac_unit' => 'м3',
            ],
            [
                'title' => 'Колесный экскаватор',
                'description' => 'Колесный экскаватор',
                'image' => null,
                'charac_title' => 'Объем ковша',
                'charac_unit' => 'м3',
            ],
            [
                'title' => 'Экскаватор погрузчик',
                'description' => 'Экскаватор погрузчик',
                'image' => null,
                'charac_title' => 'Объем ковша',
                'charac_unit' => 'м3',
            ],
            [
                'title' => 'Экскаватор планировщик',
                'description' => 'Экскаватор планировщик',
                'image' => null,
                'charac_title' => 'Объем ковша',
                'charac_unit' => 'м3',
            ],
            [
                'title' => 'Экскаватор-бульдозер',
                'description' => 'Экскаватор-бульдозер',
                'image' => null,
                'charac_title' => 'Объем ковша',
                'charac_unit' => 'м3',
            ],
            [
                'title' => 'Самосвал',
                'description' => 'Самосвал',
                'image' => null,
                'charac_title' => 'Объем ковша',
                'charac_unit' => 'м3',
            ],
            [
                'title' => 'Бортовая машина',
                'description' => 'Бортовая машина',
                'image' => null,
                'charac_title' => 'Объем ковша',
                'charac_unit' => 'м3',
            ],
            [
                'title' => 'Бортовой автомобиль',
                'description' => 'Бортовой автомобиль',
                'image' => null,
                'charac_title' => 'Объем ковша',
                'charac_unit' => 'м3',
            ],
        ];
        foreach ($types as $type) {
            DB::table('t_types')->insert($type);
        }
    }

    public function seedTechnics($count) {
        $types = \App\Models\TechnicType::all();
        foreach ($types as $type) {
            for ($i = 0; $i < $count; $i++) {
                DB::table('technics')->insert([
                    'type_id' => $type->id,
                    'charac_value' => $i + 0.5,
                ]);
            }
        }
    }
}
