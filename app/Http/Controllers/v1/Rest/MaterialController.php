<?php

namespace App\Http\Controllers\v1\Rest;

use App\Http\Controllers\Controller;
use App\Http\Resources\UserMaterialResource;
use App\Models\Material;
use App\Models\MaterialType;
use App\Models\User;
use App\Models\UserMaterials;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
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

    public function show($id, Request $request) {

        $material = Material::find($id);
        if (!$material) return $this->Result(400, null, 'Material not found'); // TODO add localized answer

        $user = $request['user'];

        $userMaterial = $user->materials()
            ->where('material_id', $material->id)
            ->withPivot('image', 'description', 'material_id')
            ->with('type')
            //->select('user_materials.id', 'user_materials.material_id', 'description', 'image', 'type_id')
            //->select()
            ->first();

        if (!$userMaterial) return $this->Result(400, null, "You don't have this material");

        return response()->json(new UserMaterialResource($userMaterial));
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
            'image' => 'image',
            'description' => 'string|max:255'
        ];
        $validator = Validator::make($request->all(), $rules);
        if ($validator->fails()) return $this->Result(400, null, $validator->errors()->first());

        $material = Material::find($request['material_id']);
        if (!$material) return $this->Result(400, null, 'Material not found'); // TODO add localized answer

        $userMaterial = UserMaterials::where('user_id', $user->id)->where('material_id', $material->id)->first();

        if (!$userMaterial) $userMaterial = new UserMaterials();

        $userMaterial->user_id = $user->id;
        $userMaterial->fill($request->all());
        $userMaterial->save();

        $materialReloaded = $user->materials()
            ->where('material_id', $material->id)
            ->withPivot('image', 'description')
            ->with('type')
            //->select('user_materials.id', 'user_materials.material_id', 'description', 'image', 'type_id')
            ->select()
            ->first();

        return response()->json(new UserMaterialResource($materialReloaded));
    }

    public function editMaterial($id, Request $request) {
        $rules = [
            'image' => 'image',
            'description' => 'string|max:255'
        ];
        $validator = Validator::make($request->all(), $rules);
        if ($validator->fails()) return $this->Result(400, null, $validator->errors()->first());

        $material = Material::find($id);
        if (!$material) return $this->Result(400, null, 'Material not found'); // TODO add localized answer

        $user = $request['user'];

        $userMaterial = UserMaterials::where('user_id', $user->id)->where('material_id', $material->id)->first();

        if (!$userMaterial) return $this->Result(400, null, "You don't have this material");

        $userMaterial->fill($request->except('material_id'));
        $userMaterial->save();

        $materialReloaded = $user->materials()
            ->where('material_id', $material->id)
            ->withPivot('image', 'description', 'material_id')
            ->with('type')
            //->select('user_materials.id', 'user_materials.material_id', 'description', 'image', 'type_id')
            //->select()
            ->first();

        return response()->json(new UserMaterialResource($materialReloaded));
    }

    public function deleteMaterial($id, Request $request) {
        $user = $request['user'];

        $user->materials()->detach($id);
    }

    public function userMaterials(Request $request) {
        $user = $request['user'];

        $materials = $user->materials()
            ->with('type')
            ->withPivot('image', 'description', 'material_id')
            ->get()
            ->makeHidden('pivot');

        return response()->json(UserMaterialResource::collection($materials));
    }
}
