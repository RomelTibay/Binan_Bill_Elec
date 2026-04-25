<?php

namespace App\Controllers;

use App\Models\UserModel;

class Auth extends BaseController
{
    /**
     * Displays the login form.
     * Connects to the view: app/Views/auth/login.php
     */
    public function loginForm()
    {
        return view('auth/login');
    }

    /**
     * Handles the login submission.
     * Connects to: UserModel (Database), Session, Audit Helper, and routes (/admin or /billing)
     */
    public function login()
    {
        // Load the custom audit helper to log user actions
        helper('audit');

        // Retrieve username and password from the POST request (from auth/login.php form)
        $username = trim((string) $this->request->getPost('username'));
        $password = (string) $this->request->getPost('password');

        if ($username === '' || $password === '') {
            return redirect()->back()->with('error', 'Username and password are required.');
        }

        // Check the database for an active user with the provided username using UserModel
        $users = new UserModel();
        $user = $users->findActiveByUsername($username);

        // Verify if the user exists and if the provided password matches the stored hash
        if (! $user || ! password_verify($password, $user['password_hash'])) {
            return redirect()->back()->with('error', 'Invalid credentials.');
        }

        // Regenerate session ID for security and store user details in the session
        session()->regenerate();
        session()->set([
            'is_logged_in' => true,
            'user_id'      => (int) $user['id'],
            'username'     => $user['username'],
            'full_name'    => $user['full_name'],
            'role_name'    => $user['role_name'],
        ]);

        // Log the successful login action into the audit_logs table
        log_action(
            'LOGIN',
            'AUTH',
            'users',
            (int) $user['id'],
            sprintf('User %s logged in.', (string) $user['username'])
        );

        // Redirect based on the user's role (connects to Home::admin or Home::billing)
        if ($user['role_name'] === 'ADMIN') {
            return redirect()->to('/admin');
        }

        return redirect()->to('/billing');
    }

    /**
     * Handles user logout.
     * Connects to: Session, Audit Helper, and routes (/login)
     */
    public function logout()
    {
        // Load the custom audit helper to log the logout action
        helper('audit');

        $userId = session()->get('user_id') ? (int) session()->get('user_id') : null;
        if ($userId !== null) {
            $username = (string) (session()->get('username') ?? 'unknown');
            log_action(
                'LOGOUT',
                'AUTH',
                'users',
                $userId,
                sprintf('User %s logged out.', $username)
            );
        }

        // Destroy all session data to securely log the user out
        session()->destroy();
        // Redirect back to the login page (connects to Auth::loginForm)
        return redirect()->to('/login');
    }
}