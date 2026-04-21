<?php

namespace App\Models;

use CodeIgniter\Model;

class RateTierModel extends Model
{
    protected $table         = 'rate_tiers';
    protected $primaryKey    = 'id';
    protected $returnType    = 'array';
    protected $useTimestamps = true;

    protected $allowedFields = [
        'min_kw',
        'max_kw',
        'rate_per_kw',
    ];

    public function getOrderedTiers(): array
    {
        return $this->orderBy('min_kw', 'ASC')->findAll();
    }
}
