<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <title>Login</title>
</head>
<body>
    <h2>Electric Billing Login</h2>

    <?php if (session()->getFlashdata('error')): ?>
        <p style="color:red;"><?= esc(session()->getFlashdata('error')) ?></p>
    <?php endif; ?>

    <form method="post" action="/login">
        <div>
            <label>Username</label>
            <input type="text" name="username" required>
        </div>

        <div>
            <label>Password</label>
            <input type="password" name="password" required>
        </div>

        <button type="submit">Login</button>
    </form>
</body>
</html>