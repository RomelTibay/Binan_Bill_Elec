<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class AppSeeder extends Seeder
{
    public function run()
    {
        $this->call('RoleSeeder');
        $this->call('RateTierSeeder');
        $this->call('UserSeeder');
    }
}
