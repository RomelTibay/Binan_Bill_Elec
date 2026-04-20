<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateBillsTable extends Migration
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
            'client_id' => [
                'type'       => 'BIGINT',
                'constraint' => 20,
                'unsigned'   => true,
            ],
            'computed_by' => [
                'type'       => 'BIGINT',
                'constraint' => 20,
                'unsigned'   => true,
            ],
            'billing_date' => [
                'type' => 'DATETIME',
            ],
            'total_kw' => [
                'type'       => 'INT',
                'constraint' => 11,
                'unsigned'   => true,
            ],
            'total_amount' => [
                'type'       => 'DECIMAL',
                'constraint' => '12,2',
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
        $this->forge->addKey('client_id');
        $this->forge->addKey('computed_by');
        $this->forge->addKey('billing_date');
        $this->forge->addForeignKey('client_id', 'clients', 'id', 'RESTRICT', 'CASCADE');
        $this->forge->addForeignKey('computed_by', 'users', 'id', 'RESTRICT', 'CASCADE');
        $this->forge->createTable('bills', true);
    }

    public function down()
    {
        $this->forge->dropTable('bills', true);
    }
}
