<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <title>Billing Dashboard</title>
</head>
<body>
    <h2>Billing Dashboard</h2>

    <p>Logged in as: <?= esc($currentUser) ?></p>
    <p>
        <a href="<?= site_url('billing/compute') ?>">Compute KW (Live)</a>
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

    <h3>Clients</h3>

    <table border="1" cellpadding="8" cellspacing="0">
        <thead>
            <tr>
                <th>Account No</th>
                <th>Name</th>
                <th>Address</th>
                <th>Meter No</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php if (empty($clients)): ?>
                <tr>
                    <td colspan="5">No clients found. You can still use Compute KW (Live) above for instant bill calculation.</td>
                </tr>
            <?php else: ?>
                <?php foreach ($clients as $client): ?>
                    <tr>
                        <td><?= esc($client['account_no']) ?></td>
                        <td><?= esc($client['full_name']) ?></td>
                        <td><?= esc($client['address']) ?></td>
                        <td><?= esc($client['meter_number']) ?></td>
                        <td>
                            <a href="<?= site_url('billing/compute/' . $client['id']) ?>">Compute Bill</a>
                        </td>
                    </tr>
                <?php endforeach; ?>
            <?php endif; ?>
        </tbody>
    </table>
</body>
</html>