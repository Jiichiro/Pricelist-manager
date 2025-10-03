<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Login</title>
  <style>
    body {
      font-family: Arial, sans-serif;
      background: #f4f6f9;
      margin: 0;
      display: flex;
      justify-content: center;
      align-items: center;
      min-height: 100vh;
    }

    .login-card {
      background: #fff;
      padding: 30px 25px;
      border-radius: 8px;
      box-shadow: 0 2px 8px rgba(0,0,0,0.1);
      width: 100%;
      max-width: 360px;
      text-align: center;
      margin: 10px 20px 10px 20px;
    }

    .login-card h2 {
      margin-bottom: 20px;
      color: #333;
    }

    .login-card input {
      width: 90%;
      padding: 10px;
      margin: 10px 0;
      border: 1px solid #ccc;
      border-radius: 4px;
      font-size: 14px;
    }

    .login-card input:focus {
      border-color: #1abc9c;
      box-shadow: 0 0 4px rgba(26, 188, 156, 0.3);
      outline: none;
    }

    .login-card button {
      width: 100%;
      padding: 10px;
      margin-top: 15px;
      background: #1abc9c;
      color: #fff;
      font-weight: bold;
      border: none;
      border-radius: 4px;
      cursor: pointer;
      transition: background 0.2s ease;
    }

    .login-card button:hover {
      background: #16a085;
    }

    .login-card a {
      display: block;
      margin-top: 12px;
      font-size: 0.9rem;
      color: #3498db;
      text-decoration: none;
    }

    .login-card a:hover {
      text-decoration: underline;
    }

    /* Responsive */
    @media (max-width: 480px) {
      .login-card {
        padding: 20px 15px;
      }
    }
  </style>
</head>
<body>
  <div class="login-card">
    <h2>Login</h2>
    <form method="POST" action="logic/auth/login.php">
      <input type="text" name="username" placeholder="Username" required>
      <input type="password" name="password" placeholder="Password" required>
      <button type="submit">Sign In</button>
    </form>
    <a href="#">Forgot password?</a>
  </div>
</body>
</html>
