<?php

namespace App\Controllers;

use App\Models\UserModel;

class Auth extends BaseController
{
    public function loginForm()
    {
        return view('auth/login');
    }

    public function login()
    {
        helper('audit');

        $username = trim((string) $this->request->getPost('username'));
        $password = (string) $this->request->getPost('password');

        if ($username === '' || $password === '') {
            return redirect()->back()->with('error', 'Username and password are required.');
        }

        $users = new UserModel();
        $user = $users->findActiveByUsername($username);

        if (! $user || ! password_verify($password, $user['password_hash'])) {
            return redirect()->back()->with('error', 'Invalid credentials.');
        }

        session()->regenerate();
        session()->set([
            'is_logged_in' => true,
            'user_id'      => (int) $user['id'],
            'username'     => $user['username'],
            'full_name'    => $user['full_name'],
            'role_name'    => $user['role_name'],
        ]);

        log_action('LOGIN', 'AUTH', 'users', (int) $user['id']);

        if ($user['role_name'] === 'ADMIN') {
            return redirect()->to('/admin');
        }

        return redirect()->to('/billing');
    }

    public function logout()
    {
        helper('audit');

        $userId = session()->get('user_id') ? (int) session()->get('user_id') : null;
        if ($userId !== null) {
            log_action('LOGOUT', 'AUTH', 'users', $userId);
        }

        session()->destroy();
        return redirect()->to('/login');
    }
}