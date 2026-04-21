<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <title>Admin - Audit Logs</title>
</head>
<body>
    <h2>Audit Logs</h2>

    <p>Logged in as: <?= esc($currentUser) ?></p>
    <p>
        <a href="<?= site_url('admin/users') ?>">Back to User List</a>
        |
        <a href="<?= site_url('logout') ?>">Logout</a>
    </p>

    <table border="1" cellpadding="8" cellspacing="0">
        <thead>
            <tr>
                <th>ID</th>
                <th>Date</th>
                <th>Actor</th>
                <th>Action</th>
                <th>Module</th>
                <th>Target</th>
                <th>IP</th>
                <th>Details</th>
            </tr>
        </thead>
        <tbody>
            <?php if (empty($logs)): ?>
                <tr>
                    <td colspan="8">No audit logs found.</td>
                </tr>
            <?php else: ?>
                <?php foreach ($logs as $log): ?>
                    <?php
                        $target = '-';
                        if (! empty($log['target_type'])) {
                            $target = $log['target_type'];
                            if (! empty($log['target_id'])) {
                                $target .= '#' . $log['target_id'];
                            }
                        }
                    ?>
                    <tr>
                        <td><?= esc((string) $log['id']) ?></td>
                        <td><?= esc((string) ($log['created_at'] ?? '-')) ?></td>
                        <td><?= esc((string) ($log['username'] ?? 'SYSTEM')) ?></td>
                        <td><?= esc((string) $log['action']) ?></td>
                        <td><?= esc((string) $log['module']) ?></td>
                        <td><?= esc($target) ?></td>
                        <td><?= esc((string) ($log['ip_address'] ?? '-')) ?></td>
                        <td><?= esc((string) ($log['details'] ?? '-')) ?></td>
                    </tr>
                <?php endforeach; ?>
            <?php endif; ?>
        </tbody>
    </table>
</body>
</html>
