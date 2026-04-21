<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <title>Admin - Users</title>
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

        .sub {
            font-size: 13px;
            color: #5f6a56;
        }
    </style>
</head>
<body>
    <div class="box">
    <h2>Admin User Management</h2>
    <p class="sub">Basic admin list view</p>

    <p>Logged in as: <?= esc($currentUser) ?></p>

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

    <p class="top-links">
        <a href="<?= site_url('admin/users/create') ?>">Create New User</a>
        <a href="<?= site_url('admin/audit-logs') ?>">View Audit Logs</a>
        <a href="<?= site_url('logout') ?>">Logout</a>
    </p>

    <table border="1" cellpadding="8" cellspacing="0">
        <thead>
            <tr>
                <th>ID</th>
                <th>Name</th>
                <th>Username</th>
                <th>Email</th>
                <th>Role</th>
                <th>Active</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php if (empty($users)): ?>
                <tr>
                    <td colspan="7">No users found.</td>
                </tr>
            <?php else: ?>
                <?php foreach ($users as $user): ?>
                    <tr>
                        <td><?= esc((string) $user['id']) ?></td>
                        <td><?= esc($user['full_name']) ?></td>
                        <td><?= esc($user['username']) ?></td>
                        <td><?= esc($user['email']) ?></td>
                        <td><?= esc($user['role_name']) ?></td>
                        <td><?= (int) $user['is_active'] === 1 ? 'Yes' : 'No' ?></td>
                        <td>
                            <a href="<?= site_url('admin/users/edit/' . $user['id']) ?>">Edit</a>
                            &nbsp;|&nbsp;
                            <?php if ((int) $user['id'] !== (int) session()->get('user_id')): ?>
                                <a href="<?= site_url('admin/users/delete/' . $user['id']) ?>" style="color: red;">Delete</a>
                            <?php else: ?>
                                <span style="color: #aaa;">Delete</span>
                            <?php endif; ?>
                        </td>
                    </tr>
                <?php endforeach; ?>
            <?php endif; ?>
        </tbody>
    </table>
    </div>
</body>
</html>
