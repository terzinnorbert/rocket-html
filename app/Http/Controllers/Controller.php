<?php

namespace App\Http\Controllers;

use App\Helper\Export;
use Illuminate\Http\Request;
use Laravel\Lumen\Routing\Controller as BaseController;

class Controller extends BaseController
{
    public function index()
    {
        return view('index');
    }

    public function download(Export $export, Request $request)
    {
        set_time_limit(0);
        try {
            $path = $export->downloadAsZip(
                $request->input('url'),
                $request->input('username'),
                $request->input('password')
            );
        } catch (\Exception $exception) {
            return view('error', compact('exception'));
        }

        return response()->download($path, 'export.zip')->deleteFileAfterSend(true);
    }
}
