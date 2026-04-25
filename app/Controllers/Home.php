<?php

namespace App\Controllers;

class Home extends BaseController
{
    /**
     * Displays the default welcome message.
     * Connects to: view: app/Views/welcome_message.php
     */
    public function index(): string
    {
        return view('welcome_message');
    }

    /**
     * Redirects to the admin users dashboard if the user is an ADMIN.
     * Connects to: routes (/admin/users or /billing) based on Role.
     */
    public function admin()
    {
        if (session()->get('role_name') !== 'ADMIN') {
            return redirect()->to('/billing');
        }

        return redirect()->to('/admin/users');
    }

    /**
     * Redirects to the billing dashboard if the user is a NORMAL_USER.
     * Connects to: routes (/billing/dashboard or /admin) based on Role.
     */
    public function billing()
    {
        if (session()->get('role_name') !== 'NORMAL_USER') {
            return redirect()->to('/admin');
        }

        return redirect()->to('/billing/dashboard');
    }
}
