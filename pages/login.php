<?php include("../components/header.php"); ?>
<h1>Login Page abc</h1>
<form method="POST" action="../logic/auth/login.php"></form>
    <label for="username">Username:</label>
    <input type="text" id="username" name="username" required><br><br>
    <label for="password">Password:</label>
    <input type="password" id="password" name="password" required><br><br>
    <input type="submit" value="Login">
</form>
<?php include("../components/footer.php"); ?>