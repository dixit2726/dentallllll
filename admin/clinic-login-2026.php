<?php
require_once 'includes/db.php';
require_once 'includes/auth.php';

redirectIfLoggedIn();

$error = "";

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = $conn->real_escape_string($_POST['username']);
    $password = $_POST['password'];

    $stmt = $conn->prepare("SELECT id, username, password, full_name FROM admins WHERE username = ?");
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows == 1) {
        $user = $result->fetch_assoc();
        if (password_verify($password, $user['password'])) {
            $_SESSION['admin_id'] = $user['id'];
            $_SESSION['admin_name'] = $user['full_name'];
            
            // Update last login
            $conn->query("UPDATE admins SET last_login = NOW() WHERE id = " . $user['id']);
            logActivity($conn, $user['id'], "Login", "Admin logged in successfully");

            header("Location: index.php");
            exit();
        } else {
            $error = "Invalid password.";
        }
    } else {
        $error = "Admin account not found.";
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login | Vijaya Dental Admin Panel</title>
    <link rel="stylesheet" href="assets/css/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        body {
            display: flex;
            align-items: center;
            justify-content: center;
            height: 100vh;
            background: linear-gradient(135deg, #eff6ff 0%, #dbeafe 100%);
        }
        .login-card {
            width: 100%;
            max-width: 400px;
            padding: 40px;
            background: #fff;
            border-radius: 20px;
            box-shadow: 0 10px 25px -5px rgba(0,0,0,0.1);
        }
        .login-header {
            text-align: center;
            margin-bottom: 30px;
        }
        .login-header i {
            font-size: 3rem;
            color: var(--primary-color);
            margin-bottom: 15px;
        }
        .form-group {
            margin-bottom: 20px;
        }
        .form-group label {
            display: block;
            margin-bottom: 8px;
            font-weight: 600;
            font-size: 0.9rem;
        }
        .form-control {
            width: 100%;
            padding: 12px 16px;
            border: 1px solid var(--border-color);
            border-radius: 10px;
            outline: none;
            transition: border-color 0.2s;
        }
        .form-control:focus {
            border-color: var(--primary-color);
        }
        .btn-login {
            width: 100%;
            padding: 12px;
            background-color: var(--primary-color);
            color: #fff;
            border: none;
            border-radius: 10px;
            font-weight: 700;
            cursor: pointer;
            transition: background 0.2s;
        }
        .btn-login:hover {
            background-color: #1d4ed8;
        }
        .alert {
            padding: 12px;
            border-radius: 8px;
            margin-bottom: 20px;
            font-size: 0.85rem;
            background-color: #fee2e2;
            color: #b91c1c;
            border: 1px solid #fecaca;
        }
    </style>
</head>
<body>

<div class="login-card">
    <div class="login-header">
        <i class="fas fa-tooth"></i>
        <h2>Vijaya Dental</h2>
        <p style="color: #64748b;">Admin Panel v2.0</p>
    </div>

    <?php if ($error): ?>
        <div class="alert"><?php echo $error; ?></div>
    <?php endif; ?>

    <form method="POST">
        <div class="form-group">
            <label>Username</label>
            <input type="text" name="username" class="form-control" placeholder="Enter username" required>
        </div>
        <div class="form-group">
            <label>Password</label>
            <input type="password" name="password" class="form-control" placeholder="••••••••" required>
        </div>
        <button type="submit" class="btn-login">Sign In</button>
    </form>
</div>

</body>
</html>
