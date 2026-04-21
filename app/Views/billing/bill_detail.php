<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <title>Bill Detail</title>
</head>
<body>
    <h2>Bill Detail</h2>

    <p>Logged in as: <?= esc($currentUser) ?></p>
    <p>
        <a href="<?= site_url('billing/history') ?>">Back to My Billing History</a>
        |
        <a href="<?= site_url('billing/dashboard') ?>">Dashboard</a>
        |
        <a href="<?= site_url('logout') ?>">Logout</a>
    </p>

    <h3>Bill Summary</h3>
    <p>Bill ID: <strong><?= esc((string) $bill['id']) ?></strong></p>
    <p>Billing Date: <strong><?= esc((string) $bill['billing_date']) ?></strong></p>
    <p>Client: <strong><?= esc((string) $bill['client_name']) ?></strong></p>
    <p>Account No: <strong><?= esc((string) $bill['account_no']) ?></strong></p>
    <p>Meter No: <strong><?= esc((string) $bill['meter_number']) ?></strong></p>
    <p>Address: <strong><?= esc((string) $bill['address']) ?></strong></p>
    <p>Total Consumption: <strong><?= esc((string) $bill['total_kw']) ?> kW</strong></p>
    <p>Total Amount: <strong><?= esc(number_format((float) $bill['total_amount'], 2)) ?></strong></p>

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
</body>
</html>
