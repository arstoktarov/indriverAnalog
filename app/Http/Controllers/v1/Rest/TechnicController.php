<?php

namespace App\Http\Controllers\v1\Rest;

use App\Http\Controllers\Controller;
use App\Models\Technic;
use Illuminate\Http\Request;

class TechnicController extends Controller
{
    public function index() {
        $technics = Technic::with(['category', 'characteristics', 'characteristics.type'])
            ->paginate(self::PAGINATE_COUNT);

        return $technics;
    }

    public function addTechnic(Request $request) {
        $user = $request['user'];

    }
}
