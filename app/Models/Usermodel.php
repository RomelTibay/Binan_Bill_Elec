<?php

namespace App\Models;

use CodeIgniter\Model;

class UserModel extends Model
{
    protected $table = 'users';
    protected $primaryKey = 'id';
    protected $returnType = 'array';
    protected $useTimestamps = true;

    protected $allowedFields = [
        'role_id',
        'full_name',
        'username',
        'email',
        'password_hash',
        'is_active',
    ];

    public function findActiveByUsername(string $username): ?array
    {
        $row = $this->select('users.id, users.username, users.full_name, users.password_hash, roles.name AS role_name')
            ->join('roles', 'roles.id = users.role_id', 'inner')
            ->where('users.username', $username)
            ->where('users.is_active', 1)
            ->first();

        return $row ?: null;
    }
}