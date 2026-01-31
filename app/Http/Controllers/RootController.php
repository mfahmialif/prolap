<?php

namespace App\Http\Controllers;

use App\Models\Jadwal;
use App\Models\Pedoman;
use App\Models\Tahun;

class RootController extends Controller
{
    public function root()
    {
        $tahun = Tahun::aktif();


        $pedoman = Pedoman::all();
        return view(
            'root',
            compact(
                'pedoman'
            )
        );
    }

    // Home Admin
    public function home()
    {
        if (\Auth::user()->role_id == 2) {
            return redirect()->route('peserta.dashboard');
        }

        if (\Auth::user()->role->nama == 'dpl') {
            return redirect()->route('dpl.dashboard');
        }

        if (\Auth::user()->role->nama == 'pengawas') {
            return redirect()->route('pengawas.dashboard');
        }

        if (\Auth::user()->role->nama == 'pamong') {
            return redirect()->route('pamong.dashboard');
        }

        return redirect()->route('admin.dashboard');
    }

    public function pengembangan()
    {
        return view('pengembangan');
    }
}

