<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <title>My Billing History</title>
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

        .ok {
            color: #1d6d1d;
            background: #ddf5dd;
            border: 1px solid #66a066;
            padding: 6px;
        }

        .err {
            color: #8f1f1f;
            background: #ffdcdc;
            border: 1px solid #be5a5a;
            padding: 6px;
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
    <h2>My Billing History</h2>

    <p>Logged in as: <?= esc($currentUser) ?></p>
    <p class="top-links">
        <a href="<?= site_url('billing/dashboard') ?>">Back to Dashboard</a>
        <a href="<?= site_url('billing/action-trails') ?>">My Action Trails</a>
        <a href="<?= site_url('logout') ?>">Logout</a>
    </p>

    <?php if (session()->getFlashdata('success')): ?>
        <p class="ok"><?= esc(session()->getFlashdata('success')) ?></p>
    <?php endif; ?>

    <?php if (session()->getFlashdata('errors')): ?>
        <div class="err">
            <ul>
                <?php foreach ((array) session()->getFlashdata('errors') as $error): ?>
                    <li><?= esc($error) ?></li>
                <?php endforeach; ?>
            </ul>
        </div>
    <?php endif; ?>

    <table border="1" cellpadding="8" cellspacing="0">
        <thead>
            <tr>
                <th>Bill ID</th>
                <th>Date</th>
                <th>Client</th>
                <th>Account No</th>
                <th>Total kW</th>
                <th>Total Amount</th>
                <th>Action</th>
            </tr>
        </thead>
        <tbody>
            <?php if (empty($bills)): ?>
                <tr>
                    <td colspan="7">No billing history yet.</td>
                </tr>
            <?php else: ?>
                <?php foreach ($bills as $bill): ?>
                    <tr>
                        <td><?= esc((string) $bill['id']) ?></td>
                        <td><?= esc((string) $bill['billing_date']) ?></td>
                        <td><?= esc((string) $bill['client_name']) ?></td>
                        <td><?= esc((string) $bill['account_no']) ?></td>
                        <td><?= esc((string) $bill['total_kw']) ?></td>
                        <td><?= esc(number_format((float) $bill['total_amount'], 2)) ?></td>
                        <td>
                            <a href="<?= site_url('billing/history/' . $bill['id']) ?>">View Details</a>
                        </td>
                    </tr>
                <?php endforeach; ?>
            <?php endif; ?>
        </tbody>
    </table>
    </div>
</body>
</html>
