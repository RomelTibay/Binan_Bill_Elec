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

        <div id="ajax-message" style="display:none;"></div>

        <form id="delete-user-form" method="post" action="<?= site_url('admin/users/delete/' . $user['id']) ?>">
            <?= csrf_field() ?>
            <button type="submit" style="color: white; background: red; padding: 8px 16px; border: none; cursor: pointer;">
                Yes, Delete This User
            </button>
            &nbsp;
            <a href="<?= site_url('admin/users') ?>">Cancel</a>
        </form>
    </div>

    <script>
    (function () {
        const form = document.getElementById('delete-user-form');
        if (!form) {
            return;
        }

        const messageBox = document.getElementById('ajax-message');
        const submitButton = form.querySelector('button[type="submit"]');

        form.addEventListener('submit', async function (event) {
            event.preventDefault();

            messageBox.style.display = 'none';
            messageBox.textContent = '';
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
                    messageBox.textContent = result.message || 'User deleted successfully.';

                    if (result.redirect) {
                        window.setTimeout(function () {
                            window.location.href = result.redirect;
                        }, 800);
                    }

                    return;
                }

                messageBox.style.display = 'block';
                messageBox.style.color = 'red';
                messageBox.textContent = result.message || 'Could not delete user.';
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