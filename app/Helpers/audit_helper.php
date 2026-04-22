<?php

use App\Models\AuditLogModel;

if (! function_exists('log_action')) {
    function log_action(string $action, string $module, ?string $targetType = null, ?int $targetId = null, ?string $description = null): void
    {
        try {
            $model = new AuditLogModel();
            $ipAddress = service('request')->getIPAddress();

            $model->insert([
                'user_id'     => session()->get('user_id') ? (int) session()->get('user_id') : null,
                'action'      => strtoupper($action),
                'module'      => $module,
                'target_type' => $targetType,
                'target_id'   => $targetId,
                'details'     => $description,
                'ip_address'  => is_string($ipAddress) ? $ipAddress : null,
                'created_at'  => date('Y-m-d H:i:s'),
            ]);
        } catch (\Throwable $exception) {
            log_message('error', 'Failed to write audit log: {error}', ['error' => $exception->getMessage()]);
        }
    }
}
