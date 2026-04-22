<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <title>My Action Trails</title>
    <style>
        body {
            margin: 0;
            padding: 18px;
            background: #eaf4ff;
            font-family: Verdana, "Trebuchet MS", sans-serif;
            color: #1f1f1f;
        }

        .box {
            background: #fff;
            border: 3px double #6f93b8;
            padding: 14px;
            border-radius: 6px;
            max-width: 1080px;
        }

        h2 {
            margin-top: 0;
            color: #1f4f82;
            letter-spacing: 0.3px;
        }

        .top-links a {
            display: inline-block;
            text-decoration: none;
            color: #143a5f;
            background: #d8ebff;
            border: 1px solid #6f93b8;
            border-radius: 4px;
            padding: 5px 8px;
            margin-right: 6px;
            margin-bottom: 6px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            border: 2px solid #86a6c7;
            background: #fff;
        }

        th {
            background: #cde3fb;
            border: 1px solid #8baccf;
            text-align: left;
            padding: 8px;
        }

        td {
            border: 1px solid #b8cee4;
            padding: 8px;
        }

        tr:nth-child(even) {
            background: #f5f9ff;
        }

        a {
            color: #1d4f7f;
        }
    </style>
</head>
<body>
    <div class="box">
    <h2>My Action Trails</h2>

    <p>Logged in as: <?= esc($currentUser) ?></p>
    <p class="top-links">
        <a href="<?= site_url('billing/dashboard') ?>">Back to Dashboard</a>
        <a href="<?= site_url('billing/history') ?>">My Billing History</a>
        <a href="<?= site_url('logout') ?>">Logout</a>
    </p>

    <table border="1" cellpadding="8" cellspacing="0">
        <thead>
            <tr>
                <th>ID</th>
                <th>Action</th>
                <th>Description</th>
                <th>Date</th>
            </tr>
        </thead>
        <tbody>
            <?php if (empty($logs)): ?>
                <tr>
                    <td colspan="4">No action trails found.</td>
                </tr>
            <?php else: ?>
                <?php foreach ($logs as $log): ?>
                    <tr>
                        <td><?= esc((string) $log['id']) ?></td>
                        <td><?= esc((string) $log['action']) ?></td>
                        <td><?= esc((string) ($log['description'] ?? 'Action recorded.')) ?></td>
                        <td><?= esc((string) ($log['created_at'] ?? '-')) ?></td>
                    </tr>
                <?php endforeach; ?>
            <?php endif; ?>
        </tbody>
    </table>
    </div>
</body>
</html>
