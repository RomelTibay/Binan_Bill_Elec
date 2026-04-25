<?php

namespace App\Controllers;

use App\Models\AuditLogModel;
use App\Models\UserModel;
use Config\Database;

class AdminUsers extends BaseController
{
    /**
     * Displays the list of users for the admin dashboard.
     * Connects to: UserModel (Database) and view: app/Views/admin/users.php
     */
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

    /**
     * Displays the form to create a new user.
     * Connects to: Database (roles table) and view: app/Views/admin/user_create.php
     */
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

    /**
     * Handles the submission of the new user form.
     * Connects to: UserModel (Database), Audit Helper, and routes (/admin/users)
     */
    public function store()
    {
        $isAjax = $this->request->isAJAX();

        if (session()->get('role_name') !== 'ADMIN') {
            if ($isAjax) {
                return $this->response
                    ->setStatusCode(403)
                    ->setJSON([
                        'ok'      => false,
                        'message' => 'Unauthorized request.',
                    ]);
            }

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
            if ($isAjax) {
                return $this->response
                    ->setStatusCode(422)
                    ->setJSON([
                        'ok'      => false,
                        'message' => 'Please fix the validation errors.',
                        'errors'  => $this->validator->getErrors(),
                    ]);
            }

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
            if ($isAjax) {
                return $this->response
                    ->setStatusCode(500)
                    ->setJSON([
                        'ok'      => false,
                        'message' => 'Failed to create user.',
                        'errors'  => $users->errors(),
                    ]);
            }

            return redirect()->back()->withInput()->with('errors', $users->errors());
        }

        helper('audit');
        $newUserId = (int) $users->getInsertID();
        log_action(
            'CREATE',
            'ADMIN_USERS',
            'users',
            $newUserId,
            sprintf('Created user %s (%s).', $userData['username'], $userData['email'])
        );

        if ($isAjax) {
            return $this->response->setJSON([
                'ok'       => true,
                'message'  => 'User created successfully.',
                'redirect' => site_url('admin/users'),
            ]);
        }

        return redirect()->to('/admin/users')->with('success', 'User created successfully.');
    }

    /**
     * Displays the form to edit an existing user.
     * Connects to: UserModel, Database (roles table), and view: app/Views/admin/user_edit.php
     */
    public function edit(int $id)
    {
        if (session()->get('role_name') !== 'ADMIN') {
            return redirect()->to('/billing');
        }

        $users = new UserModel();
        $user  = $users->find($id);

        if (! $user) {
            return redirect()->to('/admin/users')->with('errors', ['User not found.']);
        }

        $roles = Database::connect()
            ->table('roles')
            ->select('id, name')
            ->orderBy('name', 'ASC')
            ->get()
            ->getResultArray();

        return view('admin/user_edit', [
            'user'        => $user,
            'roles'       => $roles,
            'currentUser' => (string) session()->get('full_name'),
        ]);
    }

    /**
     * Handles the submission to update an existing user.
     * Connects to: UserModel (Database), Audit Helper, and routes (/admin/users)
     */
    public function update(int $id)
    {
        $isAjax = $this->request->isAJAX();

        if (session()->get('role_name') !== 'ADMIN') {
            if ($isAjax) {
                return $this->response
                    ->setStatusCode(403)
                    ->setJSON([
                        'ok'      => false,
                        'message' => 'Unauthorized request.',
                    ]);
            }

            return redirect()->to('/billing');
        }

        $users = new UserModel();
        $user  = $users->find($id);

        if (! $user) {
            if ($isAjax) {
                return $this->response
                    ->setStatusCode(404)
                    ->setJSON([
                        'ok'      => false,
                        'message' => 'User not found.',
                    ]);
            }

            return redirect()->to('/admin/users')->with('errors', ['User not found.']);
        }

        $rules = [
            'full_name' => 'required|min_length[3]|max_length[100]',
            'username'  => 'required|min_length[3]|max_length[50]',
            'email'     => 'required|valid_email|max_length[120]',
            'role_id'   => 'required|is_natural_no_zero',
        ];

        if (! $this->validate($rules)) {
            if ($isAjax) {
                return $this->response
                    ->setStatusCode(422)
                    ->setJSON([
                        'ok'      => false,
                        'message' => 'Please fix the validation errors.',
                        'errors'  => $this->validator->getErrors(),
                    ]);
            }

            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $username = trim((string) $this->request->getPost('username'));
        $email    = trim((string) $this->request->getPost('email'));

        $usernameConflict = (new UserModel())
            ->where('username', $username)
            ->where('id !=', $id)
            ->first();

        $emailConflict = (new UserModel())
            ->where('email', $email)
            ->where('id !=', $id)
            ->first();

        $errors = [];

        if ($usernameConflict) {
            $errors['username'] = 'Username already exists.';
        }

        if ($emailConflict) {
            $errors['email'] = 'Email already exists.';
        }

        if (! empty($errors)) {
            if ($isAjax) {
                return $this->response
                    ->setStatusCode(422)
                    ->setJSON([
                        'ok'      => false,
                        'message' => 'Please fix the validation errors.',
                        'errors'  => $errors,
                    ]);
            }

            return redirect()->back()->withInput()->with('errors', $errors);
        }

        $updateData = [
            'role_id'   => (int) $this->request->getPost('role_id'),
            'full_name' => trim((string) $this->request->getPost('full_name')),
            'username'  => $username,
            'email'     => $email,
            'is_active' => $this->request->getPost('is_active') ? 1 : 0,
        ];

        if (! $users->update($id, $updateData)) {
            if ($isAjax) {
                return $this->response
                    ->setStatusCode(500)
                    ->setJSON([
                        'ok'      => false,
                        'message' => 'Failed to update user.',
                        'errors'  => $users->errors(),
                    ]);
            }

            return redirect()->back()->withInput()->with('errors', $users->errors());
        }

        helper('audit');
        log_action(
            'UPDATE',
            'ADMIN_USERS',
            'users',
            $id,
            sprintf('Updated user %s (%s).', $updateData['username'], $updateData['email'])
        );

        if ($isAjax) {
            return $this->response->setJSON([
                'ok'       => true,
                'message'  => 'User updated successfully.',
                'redirect' => site_url('admin/users'),
            ]);
        }

        return redirect()->to('/admin/users')->with('success', 'User updated successfully.');
    }
    public function confirmDelete(int $id)
    {
        if (session()->get('role_name') !== 'ADMIN') {
            return redirect()->to('/billing');
        }

        $users = new UserModel();
        $user  = $users->find($id);

        if (! $user) {
            return redirect()->to('/admin/users')->with('errors', ['User not found.']);
        }

        // Prevent admin from deleting their own account
        if ((int) $id === (int) session()->get('user_id')) {
            return redirect()->to('/admin/users')->with('errors', ['You cannot delete your own account.']);
        }

        return view('admin/user_delete', [
            'user'        => $user,
            'currentUser' => (string) session()->get('full_name'),
        ]);
    }

    /**
     * Handles the actual deletion of a user.
     * Connects to: UserModel (Database), Audit Helper, and routes (/admin/users)
     */
    public function destroy(int $id)
    {
        $isAjax = $this->request->isAJAX();

        if (session()->get('role_name') !== 'ADMIN') {
            if ($isAjax) {
                return $this->response
                    ->setStatusCode(403)
                    ->setJSON([
                        'ok'      => false,
                        'message' => 'Unauthorized request.',
                    ]);
            }

            return redirect()->to('/billing');
        }

        if ((int) $id === (int) session()->get('user_id')) {
            if ($isAjax) {
                return $this->response
                    ->setStatusCode(422)
                    ->setJSON([
                        'ok'      => false,
                        'message' => 'You cannot delete your own account.',
                    ]);
            }

            return redirect()->to('/admin/users')->with('errors', ['You cannot delete your own account.']);
        }

        $users = new UserModel();
        $user  = $users->find($id);

        if (! $user) {
            if ($isAjax) {
                return $this->response
                    ->setStatusCode(404)
                    ->setJSON([
                        'ok'      => false,
                        'message' => 'User not found.',
                    ]);
            }

            return redirect()->to('/admin/users')->with('errors', ['User not found.']);
        }

        if (! $users->delete($id)) {
            if ($isAjax) {
                return $this->response
                    ->setStatusCode(500)
                    ->setJSON([
                        'ok'      => false,
                        'message' => 'Failed to delete user.',
                    ]);
            }

            return redirect()->to('/admin/users')->with('errors', ['Failed to delete user.']);
        }

        helper('audit');
        log_action(
            'DELETE',
            'ADMIN_USERS',
            'users',
            $id,
            sprintf('Deleted user %s (%s).', (string) $user['username'], (string) $user['email'])
        );

        if ($isAjax) {
            return $this->response->setJSON([
                'ok'       => true,
                'message'  => 'User deleted successfully.',
                'redirect' => site_url('admin/users'),
            ]);
        }

        return redirect()->to('/admin/users')->with('success', 'User deleted successfully.');
    }

    /**
     * Displays the confirmation page for deleting a user.
     * Connects to: UserModel (Database) and view: app/Views/admin/user_delete.php
     */
    public function auditLogs()
    {
        if (session()->get('role_name') !== 'ADMIN') {
            return redirect()->to('/billing');
        }

        $logs = new AuditLogModel();

        return view('admin/audit_logs', [
            'logs'        => $logs->getLogsForAdmin(),
            'currentUser' => (string) session()->get('full_name'),
        ]);
    }
}
