<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class AdminWebController extends Controller
{
    public function login()
    {
        return view('admin.login');
    }

    public function dashboard()
    {
        return view('admin.dashboard');
    }

    public function usersIndex()
    {
        return view('admin.users.index');
    }

    public function usersCreate()
    {
        return view('admin.users.form');
    }

    public function usersShow(int $id)
    {
        return view('admin.users.show', ['id' => $id]);
    }

    public function usersEdit(int $id)
    {
        return view('admin.users.form', ['id' => $id]);
    }

    public function locationsIndex()
    {
        return view('admin.locations.index');
    }

    public function locationsCreate()
    {
        return view('admin.locations.form');
    }

    public function locationsEdit(int $id)
    {
        return view('admin.locations.form', ['id' => $id]);
    }
}
