<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;

class UserController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return array
     */
    public function index(Request $request)
    {
        return [
            'status' => true,
            'user' => $request->user()->load('role', 'dpl', 'peserta', 'pengawas', 'pamong')
        ];
    }
}
