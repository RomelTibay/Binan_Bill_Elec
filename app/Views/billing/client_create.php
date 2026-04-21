<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <title>Add Client</title>
</head>
<body>
    <h2>Add New Client</h2>

    <p>Logged in as: <?= esc($currentUser) ?></p>
    <p>
        <a href="<?= site_url('billing/dashboard') ?>">Back to Dashboard</a>
        |
        <a href="<?= site_url('logout') ?>">Logout</a>
    </p>

    <?php if (session()->getFlashdata('errors')): ?>
        <div style="color:red;">
            <ul>
                <?php foreach ((array) session()->getFlashdata('errors') as $error): ?>
                    <li><?= esc($error) ?></li>
                <?php endforeach; ?>
            </ul>
        </div>
    <?php endif; ?>

    <form method="post" action="<?= site_url('billing/clients') ?>">
        <?= csrf_field() ?>

        <div>
            <label for="account_no">Account Number</label><br>
            <input id="account_no" type="text" name="account_no" value="<?= esc((string) old('account_no')) ?>" required>
        </div>

        <div>
            <label for="full_name">Client Full Name</label><br>
            <input id="full_name" type="text" name="full_name" value="<?= esc((string) old('full_name')) ?>" required>
        </div>

        <div>
            <label for="address">Address</label><br>
            <input id="address" type="text" name="address" value="<?= esc((string) old('address')) ?>" required>
        </div>

        <div>
            <label for="meter_number">Meter Number</label><br>
            <input id="meter_number" type="text" name="meter_number" value="<?= esc((string) old('meter_number')) ?>" required>
        </div>

        <br>
        <button type="submit">Save Client</button>
    </form>
</body>
</html>