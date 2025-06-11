<?php

namespace App\Controllers;

class Dasbor extends BaseController
{
    public function index(): string
    {
        return view('dasbor');
    }
}
