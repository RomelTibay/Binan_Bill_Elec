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
        'details',
        'ip_address',
        'created_at',
    ];

    public function getLogsForAdmin(int $limit = 200): array
    {
        return $this->select('audit_logs.id, audit_logs.user_id, audit_logs.action, audit_logs.module, audit_logs.target_type, audit_logs.target_id, audit_logs.details, audit_logs.created_at, users.username')
            ->join('users', 'users.id = audit_logs.user_id', 'left')
            ->orderBy('audit_logs.id', 'DESC')
            ->findAll($limit);
    }

    public function getLogsForUser(int $userId, int $limit = 200): array
    {
        return $this->select('audit_logs.id, audit_logs.user_id, audit_logs.action, audit_logs.module, audit_logs.target_type, audit_logs.target_id, audit_logs.details, audit_logs.created_at, users.username')
            ->join('users', 'users.id = audit_logs.user_id', 'left')
            ->where('audit_logs.user_id', $userId)
            ->orderBy('audit_logs.id', 'DESC')
            ->findAll($limit);
    }

    public function getBillingLogsForUser(int $userId, int $limit = 200): array
    {
        $logs = $this->getLogsForUser($userId, $limit);

        foreach ($logs as &$log) {
            $details = isset($log['details']) ? trim((string) $log['details']) : '';
            $log['description'] = $details !== ''
                ? $details
                : $this->buildDescriptionFallback($log);
        }

        unset($log);

        return $logs;
    }

    private function buildDescriptionFallback(array $log): string
    {
        $action = strtoupper((string) ($log['action'] ?? 'ACTION'));
        $targetType = (string) ($log['target_type'] ?? 'record');
        $targetId = $log['target_id'] ?? null;

        if ($action === 'LOGIN') {
            return 'Logged in to the system.';
        }

        if ($action === 'LOGOUT') {
            return 'Logged out from the system.';
        }

        if ($action === 'COMPUTE') {
            return $targetId !== null
                ? 'Computed billing record #' . $targetId . '.'
                : 'Computed a billing record.';
        }

        if ($targetId !== null && $targetType !== '') {
            return ucfirst(strtolower($action)) . ' ' . $targetType . ' #' . $targetId . '.';
        }

        return ucfirst(strtolower($action)) . ' action recorded.';
    }
}
