<?php

namespace App\Models;

use CodeIgniter\Model;

class BillLineModel extends Model
{
    protected $table         = 'bill_lines';
    protected $primaryKey    = 'id';
    protected $returnType    = 'array';
    protected $useTimestamps = true;

    protected $allowedFields = [
        'bill_id',
        'tier_id',
        'kw_used',
        'rate_per_kw',
        'line_total',
    ];

    public function getLinesForBill(int $billId): array
    {
        return $this->select('bill_lines.id, bill_lines.bill_id, bill_lines.tier_id, bill_lines.kw_used, bill_lines.rate_per_kw, bill_lines.line_total, rate_tiers.min_kw, rate_tiers.max_kw')
            ->join('rate_tiers', 'rate_tiers.id = bill_lines.tier_id', 'inner')
            ->where('bill_lines.bill_id', $billId)
            ->orderBy('bill_lines.id', 'ASC')
            ->findAll();
    }
}
