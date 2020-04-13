<?php

namespace App\Http\Controllers\v1\Rest;

use App\Http\Controllers\Controller;
use App\Models\Technic;
use Illuminate\Http\Request;

class TechnicController extends Controller
{
    public function index() {
        $technics = Technic::with(['category', 'characteristics', 'characteristics.type'])->get();

        return $technics;
    }
}
