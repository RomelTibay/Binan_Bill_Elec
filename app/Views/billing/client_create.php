<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <title>Add Client</title>
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
            max-width: 760px;
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

        .err {
            color: #8f1f1f;
            background: #ffdcdc;
            border: 1px solid #be5a5a;
            padding: 6px;
        }

        .field {
            margin-bottom: 10px;
        }

        input[type="text"] {
            width: 360px;
            max-width: 100%;
            border: 1px solid #86a6c7;
            border-radius: 4px;
            padding: 7px;
            font: inherit;
            background: #f9fcff;
        }

        button {
            border: 1px solid #537ea8;
            background: #cfe7ff;
            color: #143a5f;
            padding: 7px 12px;
            border-radius: 4px;
            font: inherit;
            cursor: pointer;
        }

        a {
            color: #1d4f7f;
        }
    </style>
</head>
<body>
    <div class="box">
    <h2>Add New Client</h2>

    <p>Logged in as: <?= esc($currentUser) ?></p>
    <p class="top-links">
        <a href="<?= site_url('billing/dashboard') ?>">Back to Dashboard</a>
        <a href="<?= site_url('logout') ?>">Logout</a>
    </p>

    <?php if (session()->getFlashdata('errors')): ?>
        <div class="err">
            <ul>
                <?php foreach ((array) session()->getFlashdata('errors') as $error): ?>
                    <li><?= esc($error) ?></li>
                <?php endforeach; ?>
            </ul>
        </div>
    <?php endif; ?>

    <form method="post" action="<?= site_url('billing/clients') ?>">
        <?= csrf_field() ?>

        <div class="field">
            <label for="account_no">Account Number</label><br>
            <input id="account_no" type="text" name="account_no" value="<?= esc((string) old('account_no')) ?>" required>
        </div>

        <div class="field">
            <label for="full_name">Client Full Name</label><br>
            <input id="full_name" type="text" name="full_name" value="<?= esc((string) old('full_name')) ?>" required>
        </div>

        <div class="field">
            <label for="address">Address</label><br>
            <input id="address" type="text" name="address" value="<?= esc((string) old('address')) ?>" required>
        </div>

        <div class="field">
            <label for="meter_number">Meter Number</label><br>
            <input id="meter_number" type="text" name="meter_number" value="<?= esc((string) old('meter_number')) ?>" required>
        </div>

        <br>
        <button type="submit">Save Client</button>
    </form>
    </div>
</body>
</html>