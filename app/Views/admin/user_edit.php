<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <title>Admin - Edit User</title>
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
            max-width: 760px;
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

        .top-links a:hover {
            background: #cde6b6;
        }

        .err {
            color: #8f1f1f;
            background: #ffdcdc;
            border: 1px solid #be5a5a;
            padding: 6px;
            margin-bottom: 10px;
        }

        .field {
            margin-bottom: 10px;
        }

        input[type="text"],
        input[type="email"],
        select {
            width: 360px;
            max-width: 100%;
            border: 1px solid #8c9d7a;
            border-radius: 4px;
            padding: 7px;
            font: inherit;
            background: #fcfef9;
        }

        .check {
            margin-top: 2px;
            margin-bottom: 12px;
        }

        button {
            border: 1px solid #6f8b56;
            background: #d9efc5;
            color: #203d15;
            padding: 7px 12px;
            border-radius: 4px;
            font: inherit;
            cursor: pointer;
        }

        button:disabled {
            opacity: 0.7;
            cursor: default;
        }

        .ajax-message {
            display: none;
            border: 1px solid transparent;
            padding: 6px;
            margin-bottom: 10px;
        }

        .ajax-message.ok {
            color: #1d6d1d;
            background: #ddf5dd;
            border-color: #66a066;
        }

        .ajax-message.err {
            color: #8f1f1f;
            background: #ffdcdc;
            border-color: #be5a5a;
        }

        .ajax-errors {
            display: none;
            color: #8f1f1f;
            margin-top: 0;
            margin-bottom: 10px;
        }
    </style>
</head>
<body>
    <?php helper('form'); ?>
    <?php $errors = session()->getFlashdata('errors') ?? []; ?>

    <div class="box">
    <h2>Edit User</h2>

    <p>Logged in as: <?= esc($currentUser) ?></p>
    <p class="top-links">
        <a href="<?= site_url('admin/users') ?>">Back to User List</a>
        <a href="<?= site_url('logout') ?>">Logout</a>
    </p>

    <?php if (! empty($errors)): ?>
        <div class="err">
            <p>Please fix the following errors:</p>
            <ul>
                <?php foreach ($errors as $error): ?>
                    <li><?= esc($error) ?></li>
                <?php endforeach; ?>
            </ul>
        </div>
    <?php endif; ?>

    <div id="ajax-message" class="ajax-message"></div>
    <ul id="ajax-errors" class="ajax-errors"></ul>

    <form id="edit-user-form" method="post" action="<?= site_url('admin/users/update/' . $user['id']) ?>">
        <?= csrf_field() ?>

        <div class="field">
            <label for="full_name">Full Name</label><br>
            <input id="full_name" type="text" name="full_name" value="<?= esc((string) old('full_name', $user['full_name'])) ?>" required>
        </div>

        <div class="field">
            <label for="username">Username</label><br>
            <input id="username" type="text" name="username" value="<?= esc((string) old('username', $user['username'])) ?>" required>
        </div>

        <div class="field">
            <label for="email">Email</label><br>
            <input id="email" type="email" name="email" value="<?= esc((string) old('email', $user['email'])) ?>" required>
        </div>

        <div class="field">
            <label for="role_id">Role</label><br>
            <?php $selectedRole = (string) old('role_id', (string) $user['role_id']); ?>
            <select id="role_id" name="role_id" required>
                <option value="">-- Select Role --</option>
                <?php foreach ($roles as $role): ?>
                    <option value="<?= esc((string) $role['id']) ?>" <?= $selectedRole === (string) $role['id'] ? 'selected' : '' ?>>
                        <?= esc($role['name']) ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>

        <div class="check">
            <?php
                $oldIsActive = old('is_active');
                $isChecked = $oldIsActive !== null
                    ? $oldIsActive === '1'
                    : (int) $user['is_active'] === 1;
            ?>
            <label>
                <input type="checkbox" name="is_active" value="1" <?= $isChecked ? 'checked' : '' ?>>
                Active
            </label>
        </div>

        <button type="submit">Update User</button>
    </form>

    <script>
    (function () {
        const form = document.getElementById('edit-user-form');
        if (!form) {
            return;
        }

        const messageBox = document.getElementById('ajax-message');
        const errorList = document.getElementById('ajax-errors');
        const submitButton = form.querySelector('button[type="submit"]');

        function clearFeedback() {
            messageBox.className = 'ajax-message';
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
                    messageBox.className = 'ajax-message ok';
                    messageBox.style.display = 'block';
                    messageBox.textContent = result.message || 'User updated successfully.';

                    if (result.redirect) {
                        window.setTimeout(function () {
                            window.location.href = result.redirect;
                        }, 1000);
                    }

                    return;
                }

                messageBox.className = 'ajax-message err';
                messageBox.style.display = 'block';
                messageBox.textContent = result.message || 'Could not update user.';
                showErrors(result.errors);
            } catch (error) {
                messageBox.className = 'ajax-message err';
                messageBox.style.display = 'block';
                messageBox.textContent = 'Request failed. Please try again.';
            } finally {
                submitButton.disabled = false;
            }
        });
    })();
    </script>
    </div>
</body>
</html>
