<?php

namespace App\Models;

use CodeIgniter\Model;

class AuditLogModel extends Model
{
    protected $table = 'audit_logs';
    protected $primaryKey = 'id';
    protected $returnType = 'array';
    protected $useTimestamps = false;

    protected $allowedFields = [
        'user_id',
        'action',
        'module',
        'target_type',
        'target_id',
        'created_at',
    ];

    public function getLogsForAdmin(int $limit = 200): array
    {
        return $this->select('audit_logs.id, audit_logs.user_id, audit_logs.action, audit_logs.module, audit_logs.target_type, audit_logs.target_id, audit_logs.created_at, users.username')
            ->join('users', 'users.id = audit_logs.user_id', 'left')
            ->orderBy('audit_logs.id', 'DESC')
            ->findAll($limit);
    }

    public function getLogsForUser(int $userId, int $limit = 200): array
    {
        return $this->select('audit_logs.id, audit_logs.user_id, audit_logs.action, audit_logs.module, audit_logs.target_type, audit_logs.target_id, audit_logs.created_at, users.username')
            ->join('users', 'users.id = audit_logs.user_id', 'left')
            ->where('audit_logs.user_id', $userId)
            ->orderBy('audit_logs.id', 'DESC')
            ->findAll($limit);
    }
}
