<?php

namespace App\Controllers;

class Home extends BaseController
{
    public function index(): string
    {
        return view('welcome_message');
    }

    public function admin()
    {
        if (session()->get('role_name') !== 'ADMIN') {
            return redirect()->to('/billing');
        }

        return 'Admin dashboard placeholder';
    }

    public function billing()
    {
        if (session()->get('role_name') !== 'NORMAL_USER') {
            return redirect()->to('/admin');
        }

        return 'Billing dashboard placeholder';
    }
}
