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

    <div id="ajax-message" style="display:none;"></div>
    <ul id="ajax-errors" style="display:none; color:red;"></ul>

    <form id="create-user-form" method="post" action="<?= site_url('admin/users') ?>">
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

    <script>
    (function () {
        const form = document.getElementById('create-user-form');
        if (!form) {
            return;
        }

        const messageBox = document.getElementById('ajax-message');
        const errorList = document.getElementById('ajax-errors');
        const submitButton = form.querySelector('button[type="submit"]');

        function clearFeedback() {
            messageBox.style.display = 'none';
            messageBox.textContent = '';
            errorList.style.display = 'none';
            errorList.innerHTML = '';
        }

        function showErrors(errors) {
            if (!errors || typeof errors !== 'object') {
                return;
            }

            Object.keys(errors).forEach(function (key) {
                const li = document.createElement('li');
                li.textContent = errors[key];
                errorList.appendChild(li);
            });

            if (errorList.children.length > 0) {
                errorList.style.display = 'block';
            }
        }

        form.addEventListener('submit', async function (event) {
            event.preventDefault();
            clearFeedback();

            submitButton.disabled = true;

            try {
                const response = await fetch(form.action, {
                    method: 'POST',
                    body: new FormData(form),
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest'
                    }
                });

                let result = {};
                try {
                    result = await response.json();
                } catch (error) {
                    result = {};
                }

                if (response.ok && result.ok) {
                    messageBox.style.display = 'block';
                    messageBox.style.color = 'green';
                    messageBox.textContent = result.message || 'User created successfully.';

                    form.reset();
                    const isActiveField = form.querySelector('input[name="is_active"]');
                    if (isActiveField) {
                        isActiveField.checked = true;
                    }

                    if (result.redirect) {
                        window.setTimeout(function () {
                            window.location.href = result.redirect;
                        }, 1000);
                    }

                    return;
                }

                messageBox.style.display = 'block';
                messageBox.style.color = 'red';
                messageBox.textContent = result.message || 'Could not create user.';
                showErrors(result.errors);
            } catch (error) {
                messageBox.style.display = 'block';
                messageBox.style.color = 'red';
                messageBox.textContent = 'Request failed. Please try again.';
            } finally {
                submitButton.disabled = false;
            }
        });
    })();
    </script>
</body>
</html>
