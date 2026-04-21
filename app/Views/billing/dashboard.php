<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <title>Billing Dashboard</title>
    <style>
        body {
            margin: 0;
            padding: 18px;
            background: #f7f4e8;
            font-family: "Comic Sans MS", "Trebuchet MS", sans-serif;
            color: #1f1f1f;
        }

        .box {
            background: #fffef7;
            border: 3px dashed #7b5e3f;
            border-radius: 10px;
            padding: 14px;
            box-shadow: 2px 2px 0 #d0c1a7;
            max-width: 980px;
        }

        h2, h3 {
            margin-top: 0;
            color: #31588e;
            text-decoration: underline;
        }

        .top-links a {
            background: #ffe08a;
            border: 1px solid #775f1d;
            color: #3a2d00;
            text-decoration: none;
            padding: 4px 8px;
            border-radius: 4px;
            margin-right: 6px;
            display: inline-block;
            margin-bottom: 6px;
        }

        .ok {
            color: #1d6d1d;
            background: #dff5df;
            border: 1px solid #4c954c;
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
            background: #ffffff;
            border: 2px solid #8e8e8e;
        }

        th {
            background: #cfdff3;
            border: 1px solid #8a9cb3;
            padding: 8px;
            text-align: left;
        }

        td {
            border: 1px solid #b9b9b9;
            padding: 8px;
        }

        tr:nth-child(even) {
            background: #f3f7ff;
        }

        .muted {
            color: #6d5f4b;
            font-size: 13px;
        }
    </style>
</head>
<body>
    <div class="box">
    <h2>Billing Dashboard</h2>

    <p>Logged in as: <?= esc($currentUser) ?></p>
    <p class="top-links">
        <a href="<?= site_url('billing/compute') ?>">Compute KW (Live)</a>
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
    <p class="muted">Simple dashboard layout for quick use.</p>
    </div>
</body>
</html>