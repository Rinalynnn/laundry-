<?php
session_start();

// Redirect if already logged in
if (isset($_SESSION['login_id'])) {
    $redirects = [
        1 => 'index.php',    // Admin
        2 => 'index1.php',   // Staff 1
        3 => 'index2.php',   // Staff 2
        4 => 'index3.php'    // Staff 3
    ];
    header("Location: " . $redirects[$_SESSION['login_id']]);
    exit;
}

// Hardcoded user credentials
$users = [
    'admin' => ['email' => 'admin@gmail.com', 'password' => 'admin123', 'id' => 1],
    'staff1' => ['email' => 'staff1@gmail.com', 'password' => 'staff123', 'id' => 2],
    'staff2' => ['email' => 'staff2@gmail.com', 'password' => 'staff234', 'id' => 3],
    'staff3' => ['email' => 'staff3@gmail.com', 'password' => 'staff345', 'id' => 4]
];

// Handle login
$error = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = $_POST['username'] ?? '';
    $password = $_POST['password'] ?? '';

    foreach ($users as $user) {
        if ($username === $user['email'] && $password === $user['password']) {
            // Set session
            $_SESSION['login_id'] = $user['id'];
            $_SESSION['login_name'] = $username;

            // Redirect based on user
            $redirects = [
                1 => 'index.php',    // Admin
                2 => 'index1.php',   // Staff 1
                3 => 'index2.php',   // Staff 2
                4 => 'index3.php'    // Staff 3
            ];
            header("Location: " . $redirects[$user['id']]);
            exit;
        }
    }

    $error = "Username or password is incorrect.";
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login | Habit Laba Laundry</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        body {
            font-family: 'Arial', sans-serif;
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
            background: #f0f0f0;
            background-image: url('image/bg.jpg'); /* Background Image */
            background-size: cover;
            background-position: center;
            color: #333;
        }
        .login-container {
            background: rgba(255, 255, 255, 0.8); /* Semi-transparent background */
            padding: 40px;
            border-radius: 8px;
            box-shadow: 0px 4px 10px rgba(0, 0, 0, 0.1);
            width: 100%;
            max-width: 400px;
            text-align: center;
        }
        .logo img {
            width: 120px; /* Adjust the size of the logo */
            height: auto;
            margin-bottom: 30px;
        }
        .quote {
            font-size: 1.2rem;
            font-style: italic;
            color: #555;
            margin-bottom: 20px;
        }
        .form-group {
            margin-bottom: 20px;
        }
        .form-group input {
            width: 100%;
            padding: 12px;
            font-size: 16px;
            border-radius: 5px;
            border: 1px solid #ccc;
            outline: none;
            transition: border-color 0.3s;
        }
        .form-group input:focus {
            border-color: #4a8bc2;
        }
        .btn-primary {
            width: 100%;
            padding: 14px;
            background-color: #4a8bc2;
            border: none;
            color: white;
            font-size: 16px;
            border-radius: 5px;
            cursor: pointer;
            transition: background-color 0.3s;
        }
        .btn-primary:hover {
            background-color: #3b6b94;
        }
        .alert-danger {
            color: red;
            font-size: 14px;
            margin-bottom: 15px;
        }
    </style>
</head>
<body>
    <div class="login-container">
        <div class="quote">
            <p>"Clean clothes, happy life!"</p>
        </div>
        <div class="logo">
            <img src="image/logo.jpg" alt="Habit Laba Laundry Logo"> <!-- Logo -->
        </div>
        <?php if ($error): ?>
            <div class="alert-danger"><?php echo $error; ?></div>
        <?php endif; ?>
        <form method="POST">
            <div class="form-group">
                <label for="username">Username</label>
                <input type="text" id="username" name="username" class="form-control" required>
            </div>
            <div class="form-group">
                <label for="password">Password</label>
                <input type="password" id="password" name="password" class="form-control" required>
            </div>
            <button type="submit" class="btn-primary">Login</button>
        </form>
    </div>
</body>
</html>
