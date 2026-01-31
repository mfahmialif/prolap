<?php

namespace App\Http\Controllers\Operasi;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class ThemeController extends Controller
{
    public function index(Request $request)
    {
        $request->validate([
            'theme' => 'required'
        ]);

        \Helper::setTheme($request->theme);
        return redirect()->back()->withInput();
    }
}
