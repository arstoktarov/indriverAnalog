<?php

namespace App\Http\Controllers\v1\Rest;

use App\Http\Controllers\Controller;
use App\Models\Material;
use App\Models\MaterialType;
use App\UserMaterials;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class MaterialController extends Controller
{
    public function index(Request $request) {
        $rules = [
            'type_id' => 'required|exists:m_types,id'
        ];
        $validator = Validator::make($request->all(), $rules);
        if ($validator->fails()) return $this->Result(400, null, $validator->errors());

        $materials = Material::query();

        if ($request['type_id']) {
            //$materials->where('type_id', $request['type_id']);
            $type = MaterialType::with([
                'materials' => function($query) {
                    $query->withoutGlobalScope('typeJoined');
                }
            ])->find($request['type_id']);
            return response()->json($type);
        }

        return response()->json($materials->paginate(self::PAGINATE_COUNT));
    }

    public function types(Request $request) {
        $types = MaterialType::paginate(self::PAGINATE_COUNT);
        return $types;
    }

    public function toggle(Request $request) {
        $user = $request['user'];
        $rules = [
            'material_id' => 'required|exists:materials,id'
        ];
        $validator = Validator::make($request->all(), $rules);

        if ($validator->fails()) return $this->Result(400, null, $validator->errors()->first());

        $material = Material::find($request['material_id']);
        if (!$material) return $this->Result(400, null, 'Material not found'); // TODO add localized answer

        $user->materials()->toggle($material);
        return response()->json($material);
    }

    public function addMaterial(Request $request) {
        $user = $request['user'];
        $rules = [
            'material_id' => 'required|exists:materials,id',
            'description' => 'string|max:255'
        ];
        $validator = Validator::make($request->all(), $rules);

        if ($validator->fails()) return $this->Result(400, null, $validator->errors()->first());

        $material = Material::find($request['material_id']);
        if (!$material) return $this->Result(400, null, 'Material not found'); // TODO add localized answer

        $user->materials()->syncWithoutDetaching([
            $request['material_id'] => [
                'description' => $request['description']
            ],
        ]);

        return response()->json();


    }

    public function deleteMaterial($id, Request $request) {
        $user = $request['user'];

        $user->materials()->detach($id);
    }

    public function userMaterials(Request $request) {
        $user = $request['user'];

        $materials = $user->materials()->with('type')->select()->get()->makeHidden('pivot');

        return response()->json($materials);
    }
}
