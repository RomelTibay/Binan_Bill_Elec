<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class RateTierSeeder extends Seeder
{
    public function run()
    {
        $now = date('Y-m-d H:i:s');
        $table = $this->db->table('rate_tiers');

        // Keep tier setup deterministic across repeated seeding.
        $this->db->table('rate_tiers')->truncate();

        $data = [
            [
                'min_kw'      => 1,
                'max_kw'      => 200,
                'rate_per_kw' => 10.00,
                'created_at'  => $now,
                'updated_at'  => $now,
            ],
            [
                'min_kw'      => 201,
                'max_kw'      => 500,
                'rate_per_kw' => 13.00,
                'created_at'  => $now,
                'updated_at'  => $now,
            ],
            [
                'min_kw'      => 501,
                'max_kw'      => null,
                'rate_per_kw' => 15.00,
                'created_at'  => $now,
                'updated_at'  => $now,
            ],
        ];

        $table->insertBatch($data);
    }
}
