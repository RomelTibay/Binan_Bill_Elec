<?php

namespace App\Models;

use CodeIgniter\Model;

class BillModel extends Model
{
    protected $table         = 'bills';
    protected $primaryKey    = 'id';
    protected $returnType    = 'array';
    protected $useTimestamps = true;

    protected $allowedFields = [
        'client_id',
        'computed_by',
        'billing_date',
        'total_kw',
        'total_amount',
    ];

    public function getHistoryForUser(int $userId): array
    {
        return $this->select('bills.id, bills.client_id, bills.billing_date, bills.total_kw, bills.total_amount, clients.account_no, clients.full_name AS client_name')
            ->join('clients', 'clients.id = bills.client_id', 'inner')
            ->where('bills.computed_by', $userId)
            ->orderBy('bills.id', 'DESC')
            ->findAll();
    }

    public function getBillForUser(int $billId, int $userId): ?array
    {
        $row = $this->select('bills.id, bills.client_id, bills.computed_by, bills.billing_date, bills.total_kw, bills.total_amount, clients.account_no, clients.full_name AS client_name, clients.address, clients.meter_number')
            ->join('clients', 'clients.id = bills.client_id', 'inner')
            ->where('bills.id', $billId)
            ->where('bills.computed_by', $userId)
            ->first();

        return $row ?: null;
    }
}
