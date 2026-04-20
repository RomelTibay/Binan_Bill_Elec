<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;
use RuntimeException;

class UserSeeder extends Seeder
{
    public function run()
    {
        $roles = $this->db->table('roles')
            ->select('id, name')
            ->get()
            ->getResultArray();

        $roleMap = [];

        foreach ($roles as $role) {
            $roleMap[$role['name']] = (int) $role['id'];
        }

        if (! isset($roleMap['ADMIN'], $roleMap['NORMAL_USER'])) {
            throw new RuntimeException('Required roles are missing. Seed roles first.');
        }

        $now = date('Y-m-d H:i:s');

        $data = [
            [
                'role_id'       => $roleMap['ADMIN'],
                'full_name'     => 'System Administrator',
                'username'      => 'admin',
                'email'         => 'admin@ebilling.local',
                'password_hash' => password_hash('Admin1234!', PASSWORD_DEFAULT),
                'is_active'     => 1,
                'created_at'    => $now,
                'updated_at'    => $now,
            ],
            [
                'role_id'       => $roleMap['NORMAL_USER'],
                'full_name'     => 'Billing Staff User',
                'username'      => 'normaluser',
                'email'         => 'user@ebilling.local',
                'password_hash' => password_hash('User1234!', PASSWORD_DEFAULT),
                'is_active'     => 1,
                'created_at'    => $now,
                'updated_at'    => $now,
            ],
        ];

        $this->db->table('users')->insertBatch($data);
    }
}
