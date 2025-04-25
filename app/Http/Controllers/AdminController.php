<?php

namespace App\Http\Controllers;

use App\Models\Divisi;
use Illuminate\Http\Request;

class AdminController extends Controller
{
    public function index()
    {
        $divisiList = Divisi::all();
        return view('dashboard.admin', compact('divisiList'));
        // return view('dashboard.admin');
    }
}
