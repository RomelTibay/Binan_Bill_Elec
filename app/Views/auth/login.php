<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <title>Login</title>
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
            max-width: 460px;
        }

        h2 {
            margin-top: 0;
            color: #1f4f82;
            letter-spacing: 0.3px;
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

        input[type="text"],
        input[type="password"] {
            width: 100%;
            border: 1px solid #86a6c7;
            border-radius: 4px;
            padding: 7px;
            font: inherit;
            background: #f9fcff;
            box-sizing: border-box;
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
    </style>
</head>
<body>
    <div class="box">
    <h2>Electric Billing Login</h2>

    <?php if (session()->getFlashdata('error')): ?>
        <p class="err"><?= esc(session()->getFlashdata('error')) ?></p>
    <?php endif; ?>

    <form method="post" action="/login">
        <div class="field">
            <label>Username</label>
            <input type="text" name="username" required>
        </div>

        <div class="field">
            <label>Password</label>
            <input type="password" name="password" required>
        </div>

        <button type="submit">Login</button>
    </form>
    </div>
</body>
</html>