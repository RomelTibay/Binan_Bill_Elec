<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <title>Admin - Create User</title>
</head>
<body>
    <?php helper('form'); ?>
    <?php $errors = session()->getFlashdata('errors') ?? []; ?>

    <h2>Create User</h2>

    <p>Logged in as: <?= esc($currentUser) ?></p>
    <p>
        <a href="<?= site_url('admin/users') ?>">Back to User List</a>
        |
        <a href="<?= site_url('logout') ?>">Logout</a>
    </p>

    <?php if (! empty($errors)): ?>
        <div style="color:red;">
            <p>Please fix the following errors:</p>
            <ul>
                <?php foreach ($errors as $error): ?>
                    <li><?= esc($error) ?></li>
                <?php endforeach; ?>
            </ul>
        </div>
    <?php endif; ?>

    <form method="post" action="<?= site_url('admin/users') ?>">
        <?= csrf_field() ?>

        <div>
            <label for="full_name">Full Name</label><br>
            <input id="full_name" type="text" name="full_name" value="<?= esc((string) old('full_name')) ?>" required>
        </div>

        <div>
            <label for="username">Username</label><br>
            <input id="username" type="text" name="username" value="<?= esc((string) old('username')) ?>" required>
        </div>

        <div>
            <label for="email">Email</label><br>
            <input id="email" type="email" name="email" value="<?= esc((string) old('email')) ?>" required>
        </div>

        <div>
            <label for="role_id">Role</label><br>
            <select id="role_id" name="role_id" required>
                <option value="">-- Select Role --</option>
                <?php foreach ($roles as $role): ?>
                    <?php $isSelected = old('role_id') === (string) $role['id']; ?>
                    <option value="<?= esc((string) $role['id']) ?>" <?= $isSelected ? 'selected' : '' ?>>
                        <?= esc($role['name']) ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>

        <div>
            <label for="password">Password</label><br>
            <input id="password" type="password" name="password" required>
        </div>

        <div>
            <?php $isActiveOld = old('is_active'); ?>
            <label>
                <input type="checkbox" name="is_active" value="1" <?= $isActiveOld === null || $isActiveOld === '1' ? 'checked' : '' ?>>
                Active
            </label>
        </div>

        <button type="submit">Create User</button>
    </form>
</body>
</html>
