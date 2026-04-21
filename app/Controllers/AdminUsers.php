<?php

namespace App\Controllers;

use App\Models\UserModel;
use Config\Database;

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

    public function create()
    {
        if (session()->get('role_name') !== 'ADMIN') {
            return redirect()->to('/billing');
        }

        $roles = Database::connect()
            ->table('roles')
            ->select('id, name')
            ->orderBy('name', 'ASC')
            ->get()
            ->getResultArray();

        return view('admin/user_create', [
            'roles'       => $roles,
            'currentUser' => (string) session()->get('full_name'),
        ]);
    }

    public function store()
    {
        if (session()->get('role_name') !== 'ADMIN') {
            return redirect()->to('/billing');
        }

        $rules = [
            'full_name' => 'required|min_length[3]|max_length[100]',
            'username'  => 'required|min_length[3]|max_length[50]|is_unique[users.username]',
            'email'     => 'required|valid_email|max_length[120]|is_unique[users.email]',
            'role_id'   => 'required|is_natural_no_zero',
            'password'  => 'required|min_length[8]|max_length[255]',
        ];

        if (! $this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $users = new UserModel();

        $userData = [
            'role_id'       => (int) $this->request->getPost('role_id'),
            'full_name'     => trim((string) $this->request->getPost('full_name')),
            'username'      => trim((string) $this->request->getPost('username')),
            'email'         => trim((string) $this->request->getPost('email')),
            'password_hash' => password_hash((string) $this->request->getPost('password'), PASSWORD_DEFAULT),
            'is_active'     => $this->request->getPost('is_active') ? 1 : 0,
        ];

        if (! $users->insert($userData)) {
            return redirect()->back()->withInput()->with('errors', $users->errors());
        }

        return redirect()->to('/admin/users')->with('success', 'User created successfully.');
    }
}
