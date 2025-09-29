<style>
    body {
        min-height: 100vh;
        align-content: center;
    }
    .login-container {
        background-color: white;
        padding: 40px;
        border-radius: 12px;
        box-shadow: 0 4px 20px rgba(0, 0, 0, 0.2);
        width: 100%;
        max-width: 400px;
        text-align: center;
        margin: auto;
    }

    .login-container h1 {
        margin-bottom: 24px;
        color: #4e54c8;
    }

    .login-container label {
        display: block;
        text-align: left;
        margin-bottom: 6px;
        font-weight: bold;
    }

    .login-container input[type="text"],
    .login-container input[type="password"] {
        width: 100%;
        padding: 12px;
        margin-bottom: 20px;
        border: 1px solid #ccc;
        border-radius: 6px;
        box-sizing: border-box;
    }

    .login-container input[type="submit"] {
        background-color: #4e54c8;
        color: white;
        padding: 12px 20px;
        border: none;
        border-radius: 6px;
        cursor: pointer;
        font-weight: bold;
        transition: background-color 0.3s;
    }

    .login-container input[type="submit"]:hover {
        background-color: #3c40a0;
    }
</style>

<div class="login-container">
    <h1>Login Page</h1>
    <form method="POST" action="./logic/auth/login.php">
        <label for="username">Username</label>
        <input type="text" id="username" name="username" required>

        <label for="password">Password</label>
        <input type="password" id="password" name="password" required>

        <input type="submit" value="Login">
    </form>
</div>