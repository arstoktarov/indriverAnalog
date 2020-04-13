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
        $types = ['Экскаватр', 'Экскаватор на автомобильном шасси', 'Колесный экскаватор',
            'Экскаватор погрузчик', 'Экскаватор планировщик', 'Экскаватор -бульдозер',
            'Самосвал', 'Бортовая машина', 'Бортовой автомобиль', 'Ассенизаторская машина',
        ];
        foreach ($types as $type) {
            DB::table('t_categories')->insert(['title' => $type]);
        }

        $types = DB::table('t_categories')->get();

        foreach ($types as $type) {
            for ($i = 0; $i < 5; $i++) {
                $technic = new Technic();
                $technic->category_id = $type->id;
                $technic->model = 'ASD-'.($i*10);
                $technic->specification = 'asd';
                $technic->image = null;
                $technic->save();
            }
        }

        $charac_types = ['Объем ковша', 'Емкость', 'Габариты', 'Масса груза'];

        foreach ($charac_types as $charac_type) {
            DB::table('characteristic_types')->insert(['title' => $charac_type]);
        }


        $technics = Technic::all();

        foreach ($technics as $technic) {
            DB::table('t_characteristics')
                ->insert([
                    'type_id' => 1,
                    'technic_id' => $technic->id,
                    'title' => 'Некая характеристика',
                    'value' => 25,
                    'unit' => 'кг',
                ]);
        }


    }
}
