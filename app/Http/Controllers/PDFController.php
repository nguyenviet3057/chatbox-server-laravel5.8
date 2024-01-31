<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class PDFController extends Controller
{
    public function index() {
        // return view('pdf.index');
        return $this->responseMessage(1, "", [
            "abc" => "abc",
            // "def" => "456"
            "host" => url('/')
        ]);
    }
}
