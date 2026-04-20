<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateBillLinesTable extends Migration
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
            'bill_id' => [
                'type'       => 'BIGINT',
                'constraint' => 20,
                'unsigned'   => true,
            ],
            'tier_id' => [
                'type'       => 'BIGINT',
                'constraint' => 20,
                'unsigned'   => true,
            ],
            'kw_used' => [
                'type'       => 'INT',
                'constraint' => 11,
                'unsigned'   => true,
            ],
            'rate_per_kw' => [
                'type'       => 'DECIMAL',
                'constraint' => '10,2',
            ],
            'line_total' => [
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
        $this->forge->addKey('bill_id');
        $this->forge->addKey('tier_id');
        $this->forge->addForeignKey('bill_id', 'bills', 'id', 'CASCADE', 'CASCADE');
        $this->forge->addForeignKey('tier_id', 'rate_tiers', 'id', 'RESTRICT', 'CASCADE');
        $this->forge->createTable('bill_lines', true);
    }

    public function down()
    {
        $this->forge->dropTable('bill_lines', true);
    }
}
