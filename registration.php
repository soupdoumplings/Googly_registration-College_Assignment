<?php
$errors = [];
$success = "";

// Handle form submission
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    
    $name = trim($_POST["name"]);
    $email = trim($_POST["email"]);
    $password = $_POST["password"];
    $confirm = $_POST["confirm_password"];

    // Validate required fields
    if (empty($name)) $errors['name'] = "Full Name is required.";
    
    // Validate email format
    if (empty($email)) {
        $errors['email'] = "Email is required.";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors['email'] = "Invalid email format.";
    }

    // Validate password rules
    if (empty($password)) {
        $errors['password'] = "Password is required.";
    } elseif (strlen($password) < 8) {
        $errors['password'] = "Password must be 8+ chars.";
    } elseif (!preg_match('/[\W_]/', $password)) {
        $errors['password'] = "Must contain a special char.";
    }

    // Check passwords match
    if ($password !== $confirm) $errors['confirm_password'] = "Passwords do not match.";

    // Check email uniqueness
    if (empty($errors)) {
        if (!file_exists("users.json")) {
            file_put_contents("users.json", "[]");
        }
        $users = json_decode(file_get_contents("users.json"), true) ?? [];
        
        foreach ($users as $user) {
            if ($user['email'] === $email) {
                $errors['email'] = "Email already registered.";
                break;
            }
        }
    }

    // Process valid registration
    if (empty($errors)) {
        // Hash the password
        $hashed = password_hash($password, PASSWORD_DEFAULT);

        $users[] = [
            "name" => $name,
            "email" => $email,
            "password" => $hashed
        ];

        // Save to JSON
        if (file_put_contents("users.json", json_encode($users, JSON_PRETTY_PRINT))) {
            $success = "Registration successful! Welcome, " . htmlspecialchars($name) . ".";
            $name = $email = ""; 
        } else {
            $errors['general'] = "Error saving data.";
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Googly Registration</title>
    <link rel="stylesheet" href="CSS/style.css">
</head>
<body>

<div class="container">
    <!-- Eye animation heading -->
    <h1>f<span class="eye"><span class="pupil"></span></span><span class="eye"><span class="pupil"></span></span>rm</h1>
    <p class="subtitle">Fill till your hearts content</p>

    <?php if ($success): ?>
        <div class="message success"><?= $success ?></div>
    <?php endif; ?>

    <?php if (isset($errors['general'])): ?>
        <div class="message error"><?= $errors['general'] ?></div>
    <?php endif; ?>

    <!-- Registration form start -->
    <form method="POST" autocomplete="off">
        <div class="input-group">
            <label for="name">Full Name</label>
            <input type="text" id="name" name="name" value="<?= htmlspecialchars($name ?? '') ?>">
            <?php if (isset($errors['name'])): ?>
                <span class="error-text"><?= $errors['name'] ?></span>
            <?php endif; ?>
        </div>

        <div class="input-group">
            <label for="email">Email Address</label>
            <input type="email" id="email" name="email" value="<?= htmlspecialchars($email ?? '') ?>">
            <?php if (isset($errors['email'])): ?>
                <span class="error-text"><?= $errors['email'] ?></span>
            <?php endif; ?>
        </div>

        <div class="input-group">
            <label for="password">Password</label>
            <input type="password" id="password" name="password">
            <small class="help-text">Must be 8+ chars with a special symbol.</small>
            <?php if (isset($errors['password'])): ?>
                <span class="error-text"><?= $errors['password'] ?></span>
            <?php endif; ?>
        </div>

        <div class="input-group">
            <label for="confirm_password">Confirm Password</label>
            <input type="password" id="confirm_password" name="confirm_password">
            <?php if (isset($errors['confirm_password'])): ?>
                <span class="error-text"><?= $errors['confirm_password'] ?></span>
            <?php endif; ?>
        </div>

        <button type="submit">Register</button>
    </form>
</div>

<script src="Javascript/script.js"></script>
</body>
</html>
