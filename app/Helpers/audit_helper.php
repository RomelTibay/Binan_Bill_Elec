<?php

use App\Models\AuditLogModel;

if (! function_exists('log_action')) {
    function log_action(string $action, string $module, ?string $targetType = null, ?int $targetId = null, array $details = []): void
    {
        try {
            $model = new AuditLogModel();
            $request = service('request');

            $detailsJson = null;
            if (! empty($details)) {
                $encoded = json_encode($details, JSON_UNESCAPED_SLASHES);
                if ($encoded !== false) {
                    $detailsJson = $encoded;
                }
            }

            $model->insert([
                'user_id'     => session()->get('user_id') ? (int) session()->get('user_id') : null,
                'action'      => strtoupper($action),
                'module'      => $module,
                'target_type' => $targetType,
                'target_id'   => $targetId,
                'details'     => $detailsJson,
                'ip_address'  => method_exists($request, 'getIPAddress') ? $request->getIPAddress() : null,
                'created_at'  => date('Y-m-d H:i:s'),
            ]);
        } catch (\Throwable $exception) {
            log_message('error', 'Failed to write audit log: {error}', ['error' => $exception->getMessage()]);
        }
    }
}
