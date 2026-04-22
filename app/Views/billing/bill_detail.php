<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <title>Bill Detail</title>
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

        h2, h3 {
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

        .summary p {
            margin: 7px 0;
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
    <h2>Bill Detail</h2>

    <p>Logged in as: <?= esc($currentUser) ?></p>
    <p class="top-links">
        <a href="<?= site_url('billing/history') ?>">Back to My Billing History</a>
        <a href="<?= site_url('billing/dashboard') ?>">Dashboard</a>
        <a href="<?= site_url('logout') ?>">Logout</a>
    </p>

    <h3>Bill Summary</h3>
    <div class="summary">
    <p>Bill ID: <strong><?= esc((string) $bill['id']) ?></strong></p>
    <p>Billing Date: <strong><?= esc((string) $bill['billing_date']) ?></strong></p>
    <p>Client: <strong><?= esc((string) $bill['client_name']) ?></strong></p>
    <p>Account No: <strong><?= esc((string) $bill['account_no']) ?></strong></p>
    <p>Meter No: <strong><?= esc((string) $bill['meter_number']) ?></strong></p>
    <p>Address: <strong><?= esc((string) $bill['address']) ?></strong></p>
    <p>Total Consumption: <strong><?= esc((string) $bill['total_kw']) ?> kW</strong></p>
    <p>Total Amount: <strong><?= esc(number_format((float) $bill['total_amount'], 2)) ?></strong></p>
    </div>

    <h3>Tier Breakdown</h3>
    <table border="1" cellpadding="8" cellspacing="0">
        <thead>
            <tr>
                <th>Tier Range</th>
                <th>kW Used</th>
                <th>Rate per kW</th>
                <th>Line Total</th>
            </tr>
        </thead>
        <tbody>
            <?php if (empty($lines)): ?>
                <tr>
                    <td colspan="4">No line items found.</td>
                </tr>
            <?php else: ?>
                <?php foreach ($lines as $line): ?>
                    <?php
                        $rangeText = (string) $line['min_kw'] . ' - ' . ((string) ($line['max_kw'] ?? 'above'));
                    ?>
                    <tr>
                        <td><?= esc($rangeText) ?></td>
                        <td><?= esc((string) $line['kw_used']) ?></td>
                        <td><?= esc(number_format((float) $line['rate_per_kw'], 2)) ?></td>
                        <td><?= esc(number_format((float) $line['line_total'], 2)) ?></td>
                    </tr>
                <?php endforeach; ?>
            <?php endif; ?>
        </tbody>
    </table>
    </div>
</body>
</html>
