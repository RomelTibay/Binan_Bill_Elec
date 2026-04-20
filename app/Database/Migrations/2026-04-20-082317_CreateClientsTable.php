<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateClientsTable extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'id' => [
                'type'           => 'BIGINT',
                'constraint'     => 20,
                'unsigned'       => true,
                'auto_increment' => true,
            ],
            'account_no' => [
                'type'       => 'VARCHAR',
                'constraint' => 30,
            ],
            'full_name' => [
                'type'       => 'VARCHAR',
                'constraint' => 120,
            ],
            'address' => [
                'type'       => 'VARCHAR',
                'constraint' => 255,
            ],
            'meter_number' => [
                'type'       => 'VARCHAR',
                'constraint' => 50,
            ],
            'created_by' => [
                'type'       => 'BIGINT',
                'constraint' => 20,
                'unsigned'   => true,
            ],
            'created_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
            'updated_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
        ]);

        $this->forge->addKey('id', true);
        $this->forge->addKey('created_by');
        $this->forge->addUniqueKey('account_no');
        $this->forge->addUniqueKey('meter_number');
        $this->forge->addForeignKey('created_by', 'users', 'id', 'RESTRICT', 'CASCADE');
        $this->forge->createTable('clients', true);
    }

    public function down()
    {
        $this->forge->dropTable('clients', true);
    }
}
