<?php

namespace App\Http\Controllers\Pamong;

use App\Http\Controllers\Controller;
use App\Models\DPL;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index()
    {
        $pamong = \Auth::user()->pamong;
        return view('pamong.dashboard.index', compact('pamong'));
    }
}
