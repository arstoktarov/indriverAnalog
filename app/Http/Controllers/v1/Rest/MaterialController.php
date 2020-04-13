<?php

namespace App\Http\Controllers\v1\Rest;

use App\Http\Controllers\Controller;
use App\Models\Material;
use Illuminate\Http\Request;

class MaterialController extends Controller
{
    public function index(Request $request) {
        $materials = Material::paginate(self::PAGINATE_COUNT);
        return $materials;
    }


}
