<?php

namespace App\Http\Controllers\Pengawas;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index()
    {
        $pengawas = \Auth::user()->pengawas;
        return view('pengawas.dashboard.index', compact('pengawas'));
    }
}
