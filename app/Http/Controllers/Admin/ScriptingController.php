<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class ScriptingController extends Controller
{
    public function index()
    {
        return view('admin.scriptings.index');
    }
}
