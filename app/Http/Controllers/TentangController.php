<?php

namespace App\Http\Controllers;

class TentangController extends Controller
{
    public function index()
    {
        return view('tentang', [
            'nama'  => 'Alfina Dwi Hidayati',
            'nim'   => '607062500026',
            'kelas' => 'D3IF-49-01',
    ]);
}
}
