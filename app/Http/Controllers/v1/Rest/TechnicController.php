<?php

namespace App\Http\Controllers\v1\Rest;

use App\Http\Controllers\Controller;
use App\Models\CharacteristicType;
use App\Models\Technic;
use App\Models\TechnicCategory;
use App\Models\TechnicType;
use App\Models\UserTechnic;
use DemeterChain\C;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class TechnicController extends Controller
{
    public function index(Request $request) {
        $rules = [
            'type_id' => 'required|exists:t_types,id'
        ];
        $validator = Validator::make($request->all(), $rules);
        if ($validator->fails())
            return $this->Result(400, null, $validator->errors());

        $type = TechnicType::with('technics')->find($request['type_id']);
        return $type;
    }

    public function addTechnic(Request $request) {
        $user = $request['user'];
        $rules = [
            'technic_id' => 'required|exists:technics,id',
            'image' => '',
            'description' => 'string|max:255',
        ];
        $validator = Validator::make($request->all(), $rules);
        if ($validator->fails())
            return $this->Result(400, null, $validator->errors());

        $user->technics()->syncWithoutDetaching([
            $request['technic_id'] => [
                'image' => $request['image'],
                'description' => $request['description'],
            ]
        ]);

        //$userTechnic = UserTechnic::firstOrCreate([
        //    'technic_id' => $request['technic_id'],
        //    'user_id' => $user->id,
        //]);
        //$userTechnic->image = $request['image'];
        //$userTechnic->description = $request['description'];

        return response()->json();
    }

    public function deleteTechnic($id, Request $request) {
        $user = $request['user'];

        $user->technics()->detach($id);

    }

    public function userTechnics(Request $request) {
        $user = $request['user'];
        $technics = $user->technics()
            ->with('type')
            ->select()
            ->get();
        return $technics;
    }

    public function types(Request $request) {
        return TechnicType::paginate(self::PAGINATE_COUNT);
    }
}
