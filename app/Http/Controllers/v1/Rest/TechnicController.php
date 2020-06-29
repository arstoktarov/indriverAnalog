<?php

namespace App\Http\Controllers\v1\Rest;

use App\Http\Controllers\Controller;
use App\Http\Resources\UserTechnicResource;
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

    public function show($id, Request $request) {
        $user = $request['user'];
        $technic = Technic::find($id);
        if (!$technic) return $this->Result(404, null, 'Technic not found');

        $userTechnic = $user->technics()->where('technic_id', $technic->id)
            ->with('type')
            ->withPivot('image', 'description', 'model', 'technic_id')
            ->first();

        if (!$userTechnic) return $this->Result(404, null, "You don't have this technic");

        return response()->json(new UserTechnicResource($userTechnic));
    }

    public function addTechnic(Request $request) {
        $rules = [
            'technic_id' => 'required|exists:technics,id',
            'image' => 'image',
            'description' => 'string|max:255',
            'model' => 'string|max:255'
        ];
        $validator = Validator::make($request->all(), $rules);
        if ($validator->fails())
            return $this->Result(400, null, $validator->errors());

        $user = $request['user'];
        $technic = Technic::find($request['technic_id']);

        $userTechnic = UserTechnic::where('user_id', $user->id)
            ->where('technic_id', $technic->id)->first();

        if (!$userTechnic) $userTechnic = new UserTechnic();

        $userTechnic->user_id = $user->id;
        $userTechnic->fill($request->all());
        $userTechnic->save();

        $tecnicReloaded = $user->technics()->where('technic_id', $technic->id)
            ->with('type')
            ->withPivot('image', 'description', 'model', 'technic_id')
            ->first();

        //$userTechnic = UserTechnic::firstOrCreate([
        //    'technic_id' => $request['technic_id'],
        //    'user_id' => $user->id,
        //]);
        //$userTechnic->image = $request['image'];
        //$userTechnic->description = $request['description'];

        return response()->json(new UserTechnicResource($tecnicReloaded));
    }

    public function editTechnic($id, Request $request) {
        $rules = [
            'image' => 'image',
            'description' => 'string|max:255',
            'model' => 'string|max:255'
        ];
        $validator = Validator::make($request->all(), $rules);
        if ($validator->fails())
            return $this->Result(400, null, $validator->errors());

        $user = $request['user'];
        $technic = Technic::find($id);

        $userTechnic = UserTechnic::where('user_id', $user->id)
            ->where('technic_id', $technic->id)->first();

        if (!$userTechnic) return $this->Result(404, null, "You don't have this technic");

        $userTechnic->user_id = $user->id;
        $userTechnic->fill($request->except('technic_id'));
        $userTechnic->save();

        $tecnicReloaded = $user->technics()
            ->where('technic_id', $technic->id)
            ->withPivot('image', 'description', 'model', 'technic_id')
            ->with('type')
            ->first();

        //$userTechnic = UserTechnic::firstOrCreate([
        //    'technic_id' => $request['technic_id'],
        //    'user_id' => $user->id,
        //]);
        //$userTechnic->image = $request['image'];
        //$userTechnic->description = $request['description'];

        return response()->json(new UserTechnicResource($tecnicReloaded));
    }

    public function deleteTechnic($id, Request $request) {
        $user = $request['user'];

        $user->technics()->detach($id);

    }

    public function userTechnics(Request $request) {
        $user = $request['user'];
        $technics = $user->technics()
            ->with('type')
            ->withPivot('image', 'description', 'model')
            ->select()
            ->get();
        return response()->json(UserTechnicResource::collection($technics));
    }

    public function types(Request $request) {
        return TechnicType::paginate(self::PAGINATE_COUNT);
    }
}
