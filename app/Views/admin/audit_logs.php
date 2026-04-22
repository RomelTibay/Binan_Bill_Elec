<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <title>Admin - Audit Logs</title>
    <style>
        body {
            margin: 0;
            padding: 18px;
            background: #eef2e4;
            font-family: Verdana, "Trebuchet MS", sans-serif;
            color: #1f1f1f;
        }

        .box {
            background: #fff;
            border: 3px double #6f7c58;
            padding: 14px;
            border-radius: 6px;
            max-width: 1080px;
        }

        h2 {
            margin-top: 0;
            color: #364b2d;
            letter-spacing: 0.3px;
        }

        .top-links a {
            display: inline-block;
            text-decoration: none;
            color: #203d15;
            background: #d9efc5;
            border: 1px solid #6f8b56;
            border-radius: 4px;
            padding: 5px 8px;
            margin-right: 6px;
            margin-bottom: 6px;
        }

        .top-links a:hover {
            background: #cde6b6;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            border: 2px solid #8c9d7a;
            background: #fff;
        }

        th {
            background: #cfe0bf;
            border: 1px solid #8da77a;
            text-align: left;
            padding: 8px;
        }

        td {
            border: 1px solid #b9c6ad;
            padding: 8px;
        }

        tr:nth-child(even) {
            background: #f8fbf4;
        }

        a {
            color: #2d5a1f;
        }
    </style>
</head>
<body>
    <div class="box">
    <h2>Audit Logs</h2>

    <p>Logged in as: <?= esc($currentUser) ?></p>
    <p class="top-links">
        <a href="<?= site_url('admin/users') ?>">Back to User List</a>
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
            </tr>
        </thead>
        <tbody>
            <?php if (empty($logs)): ?>
                <tr>
                    <td colspan="6">No audit logs found.</td>
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
                    </tr>
                <?php endforeach; ?>
            <?php endif; ?>
        </tbody>
    </table>
    </div>
</body>
</html>
