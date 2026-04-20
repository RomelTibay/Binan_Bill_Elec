<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class RoleSeeder extends Seeder
{
    public function run()
    {
        $now = date('Y-m-d H:i:s');

        $data = [
            [
                'name'       => 'ADMIN',
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'name'       => 'NORMAL_USER',
                'created_at' => $now,
                'updated_at' => $now,
            ],
        ];

        $this->db->table('roles')->insertBatch($data);
    }
}
