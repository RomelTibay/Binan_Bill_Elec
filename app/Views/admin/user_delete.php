<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <title>Admin - Delete User</title>
</head>
<body>
    <h2>Delete User</h2>

    <p>Logged in as: <?= esc($currentUser) ?></p>
    <p>
        <a href="<?= site_url('admin/users') ?>">Back to User List</a>
        |
        <a href="<?= site_url('logout') ?>">Logout</a>
    </p>

    <div style="border: 1px solid red; padding: 16px; max-width: 480px;">
        <p><strong>Are you sure you want to delete this user?</strong></p>
        <p>Name: <?= esc($user['full_name']) ?></p>
        <p>Username: <?= esc($user['username']) ?></p>
        <p>Email: <?= esc($user['email']) ?></p>
        <p style="color: red;">This action cannot be undone.</p>

        <form method="post" action="<?= site_url('admin/users/delete/' . $user['id']) ?>">
            <?= csrf_field() ?>
            <button type="submit" style="color: white; background: red; padding: 8px 16px; border: none; cursor: pointer;">
                Yes, Delete This User
            </button>
            &nbsp;
            <a href="<?= site_url('admin/users') ?>">Cancel</a>
        </form>
    </div>
</body>
</html>