<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <title>Admin - Users</title>
</head>
<body>
    <h2>Admin User Management</h2>

    <p>Logged in as: <?= esc($currentUser) ?></p>

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

    <p>
        <a href="<?= site_url('admin/users/create') ?>">Create New User</a>
        |
        <a href="<?= site_url('admin/audit-logs') ?>">View Audit Logs</a>
        |
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
</body>
</html>
