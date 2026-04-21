<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <title>My Billing History</title>
</head>
<body>
    <h2>My Billing History</h2>

    <p>Logged in as: <?= esc($currentUser) ?></p>
    <p>
        <a href="<?= site_url('billing/dashboard') ?>">Back to Dashboard</a>
        |
        <a href="<?= site_url('billing/action-trails') ?>">My Action Trails</a>
        |
        <a href="<?= site_url('logout') ?>">Logout</a>
    </p>

    <?php if (session()->getFlashdata('success')): ?>
        <p style="color:green;"><?= esc(session()->getFlashdata('success')) ?></p>
    <?php endif; ?>

    <?php if (session()->getFlashdata('errors')): ?>
        <div style="color:red;">
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
</body>
</html>
