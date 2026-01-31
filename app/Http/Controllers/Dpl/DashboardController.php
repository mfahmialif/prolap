<?php

namespace App\Http\Controllers\Dpl;

use App\Http\Controllers\Controller;
use App\Models\DPL;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index()
    {
        $dpl = \Auth::user()->dpl;
        return view('dpl.dashboard.index', compact('dpl'));
    }
}
