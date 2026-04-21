<?php

namespace App\Controllers;

use App\Models\UserModel;

class AdminUsers extends BaseController
{
    public function index()
    {
        if (session()->get('role_name') !== 'ADMIN') {
            return redirect()->to('/billing');
        }

        $users = new UserModel();

        return view('admin/users', [
            'users'       => $users->getUsersForAdminList(),
            'currentUser' => (string) session()->get('full_name'),
        ]);
    }
}
