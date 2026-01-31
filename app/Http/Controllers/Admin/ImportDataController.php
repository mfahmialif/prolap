<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Imports\ImportAll;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;

class ImportDataController extends Controller
{
    function index()
    {
        return view('admin.import-all.index');
    }

    function store(Request $request)
    {
        try {
            $request->validate([
                'file' => 'required|mimes:xlsx,xls'
            ]);
            $file = $request->file('file');


            $import = new ImportAll();
            Excel::import($import, $file);

            $results = count($import->results) > 0 ? ". Tapi ada error: (" . count($import->results) . ")" . json_encode($import->results) : "";
            return response()->json([
                'title' => 'Berhasil',
                'message' => 'Data Berhasil ditambahkan' . $results,
                'type' => 'success',
                'import' => $import
            ]);
        } catch (\Throwable $th) {
            return response()->json([
                'status' => false,
                'message' => 'Proses import dihentikan. ' . $th->getMessage(),
                'type' => 'error'
            ]);
        }
    }
}
