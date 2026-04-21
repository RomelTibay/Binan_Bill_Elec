<?php

namespace App\Models;

use CodeIgniter\Model;

class ClientModel extends Model
{
    protected $table         = 'clients';
    protected $primaryKey    = 'id';
    protected $returnType    = 'array';
    protected $useTimestamps = true;

    protected $allowedFields = [
        'account_no',
        'full_name',
        'address',
        'meter_number',
        'created_by',
    ];

    public function getClientsForList(): array
    {
        return $this->select('clients.id, clients.account_no, clients.full_name, clients.address, clients.meter_number, users.full_name AS created_by_name')
            ->join('users', 'users.id = clients.created_by', 'left')
            ->orderBy('clients.id', 'DESC')
            ->findAll();
    }
}
