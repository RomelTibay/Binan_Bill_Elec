<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <title>Admin - Delete User</title>
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

        .danger-box {
            border: 1px solid #be5a5a;
            background: #fff4f4;
            padding: 16px;
            max-width: 520px;
            border-radius: 6px;
        }

        .danger-text {
            color: #8f1f1f;
        }

        .danger-btn {
            color: #fff;
            background: #b03939;
            border: 1px solid #8b2e2e;
            padding: 8px 16px;
            border-radius: 4px;
            cursor: pointer;
            font: inherit;
        }

        .danger-btn:disabled {
            opacity: 0.75;
            cursor: default;
        }

        .cancel-link {
            color: #2d5a1f;
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
    </style>
</head>
<body>
    <div class="box">
    <h2>Delete User</h2>

    <p>Logged in as: <?= esc($currentUser) ?></p>
    <p class="top-links">
        <a href="<?= site_url('admin/users') ?>">Back to User List</a>
        <a href="<?= site_url('logout') ?>">Logout</a>
    </p>

    <div class="danger-box">
        <p><strong>Are you sure you want to delete this user?</strong></p>
        <p>Name: <?= esc($user['full_name']) ?></p>
        <p>Username: <?= esc($user['username']) ?></p>
        <p>Email: <?= esc($user['email']) ?></p>
        <p class="danger-text">This action cannot be undone.</p>

        <div id="ajax-message" class="ajax-message"></div>

        <form id="delete-user-form" method="post" action="<?= site_url('admin/users/delete/' . $user['id']) ?>">
            <?= csrf_field() ?>
            <button class="danger-btn" type="submit">
                Yes, Delete This User
            </button>
            &nbsp;
            <a class="cancel-link" href="<?= site_url('admin/users') ?>">Cancel</a>
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

            messageBox.className = 'ajax-message';
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
                    messageBox.className = 'ajax-message ok';
                    messageBox.style.display = 'block';
                    messageBox.textContent = result.message || 'User deleted successfully.';

                    if (result.redirect) {
                        window.setTimeout(function () {
                            window.location.href = result.redirect;
                        }, 800);
                    }

                    return;
                }

                messageBox.className = 'ajax-message err';
                messageBox.style.display = 'block';
                messageBox.textContent = result.message || 'Could not delete user.';
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