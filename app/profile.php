<?php
session_start();

// Database configuration
$db_host = getenv('DB_HOST') ?: 'db';
$db_name = getenv('DB_NAME') ?: 'sqli_demo';
$db_user = getenv('DB_USER') ?: 'demo_user';
$db_pass = getenv('DB_PASS') ?: 'demo_pass';

// Simple session check (for demo purposes)
$current_user = $_SESSION['username'] ?? 'alice';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nickname = $_POST['nickname'] ?? '';
    $email = $_POST['email'] ?? '';
    $address = $_POST['address'] ?? '';
    $phone = $_POST['phone'] ?? '';
    $password = $_POST['password'] ?? '';
    
    try {
        $conn = new mysqli($db_host, $db_user, $db_pass, $db_name);
        
        if ($conn->connect_error) {
            die("Connection failed: " . $conn->connect_error);
        }
        
        // VULNERABLE CODE: Direct string concatenation in UPDATE - SQL Injection vulnerability!
        $sql = "UPDATE users SET nickname='$nickname', email='$email', address='$address', phone='$phone'";
        
        if (!empty($password)) {
            $password_hash = sha1($password);
            $sql .= ", password='$password_hash'";
        }
        
        $sql .= " WHERE username='$current_user'";
        
        echo "<div style='background: #d1ecf1; padding: 15px; margin: 20px 0; border-radius: 4px;'><strong>SQL Query:</strong> " . htmlspecialchars($sql) . "</div>";
        
        $result = $conn->query($sql);
        
        if ($result) {
            echo "<div style='background: #d4edda; color: #155724; padding: 15px; margin: 20px 0; border-radius: 4px;'>Profile updated successfully!</div>";
        } else {
            echo "<div style='background: #f8d7da; color: #721c24; padding: 15px; margin: 20px 0; border-radius: 4px;'>Error: " . htmlspecialchars($conn->error) . "</div>";
        }
        
        $conn->close();
    } catch (Exception $e) {
        echo "<div style='background: #f8d7da; color: #721c24; padding: 15px; margin: 20px 0; border-radius: 4px;'>Error: " . htmlspecialchars($e->getMessage()) . "</div>";
    }
}

// Get current user info
try {
    $conn = new mysqli($db_host, $db_user, $db_pass, $db_name);
    $sql = "SELECT * FROM users WHERE username='$current_user'";
    $result = $conn->query($sql);
    $user = $result->fetch_assoc();
    $conn->close();
} catch (Exception $e) {
    $user = null;
}
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Profile - SQL Injection Demo</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            max-width: 800px;
            margin: 50px auto;
            padding: 20px;
            background-color: #f5f5f5;
        }
        .container {
            background: white;
            padding: 30px;
            border-radius: 8px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }
        h1 {
            color: #333;
            text-align: center;
        }
        .form-group {
            margin-bottom: 20px;
        }
        label {
            display: block;
            margin-bottom: 5px;
            font-weight: bold;
        }
        input[type="text"], input[type="email"], input[type="password"], textarea {
            width: 100%;
            padding: 10px;
            border: 1px solid #ddd;
            border-radius: 4px;
            box-sizing: border-box;
        }
        textarea {
            resize: vertical;
        }
        button {
            background-color: #4CAF50;
            color: white;
            padding: 10px 20px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            width: 100%;
            font-size: 16px;
        }
        button:hover {
            background-color: #45a049;
        }
        .nav {
            text-align: center;
            margin-bottom: 30px;
        }
        .nav a {
            margin: 0 10px;
            padding: 10px 20px;
            background-color: #007bff;
            color: white;
            text-decoration: none;
            border-radius: 4px;
            display: inline-block;
        }
        .nav a:hover {
            background-color: #0056b3;
        }
        .info-box {
            background-color: #fff3cd;
            border: 1px solid #ffc107;
            padding: 15px;
            border-radius: 4px;
            margin-top: 20px;
        }
        .salary-info {
            background-color: #e7f3ff;
            padding: 10px;
            border-radius: 4px;
            margin-bottom: 20px;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>Edit Profile</h1>
        <div class="nav">
            <a href="index.php">Login</a>
            <a href="search.php">Search Products</a>
            <a href="profile.php">Edit Profile</a>
        </div>

        <?php if ($user): ?>
        <div class="salary-info">
            <strong>Current User:</strong> <?php echo htmlspecialchars($user['username']); ?><br>
            <strong>Current Salary:</strong> $<?php echo number_format($user['salary'], 2); ?> (Read-only)
        </div>

        <form method="POST" action="">
            <div class="form-group">
                <label for="nickname">Nickname:</label>
                <input type="text" id="nickname" name="nickname" value="<?php echo htmlspecialchars($user['nickname'] ?? ''); ?>" required>
            </div>
            <div class="form-group">
                <label for="email">Email:</label>
                <input type="email" id="email" name="email" value="<?php echo htmlspecialchars($user['email'] ?? ''); ?>" required>
            </div>
            <div class="form-group">
                <label for="address">Address:</label>
                <textarea id="address" name="address" rows="3"><?php echo htmlspecialchars($user['address'] ?? ''); ?></textarea>
            </div>
            <div class="form-group">
                <label for="phone">Phone:</label>
                <input type="text" id="phone" name="phone" value="<?php echo htmlspecialchars($user['phone'] ?? ''); ?>">
            </div>
            <div class="form-group">
                <label for="password">New Password (leave blank to keep current):</label>
                <input type="password" id="password" name="password" placeholder="Enter new password">
            </div>
            <button type="submit">Update Profile</button>
        </form>

        <div class="info-box">
            <h3>SQL Injection Demo - UPDATE Statement:</h3>
            <p><strong>Task 1 - Modify your own salary:</strong></p>
            <p>In the nickname field, enter: <code>', salary=99999.00 WHERE username='alice</code></p>
            <p>This will update your salary to $99999.00</p>
            <p><strong>Task 2 - Modify other people's salary:</strong></p>
            <p>In the nickname field, enter: <code>', salary=1.00 WHERE username='boby' -- </code></p>
            <p>This will set Boby's salary to $1.00</p>
            <p><strong>Task 3 - Modify other people's password:</strong></p>
            <p>In the nickname field, enter: <code>', password='<?php echo sha1('hacked123'); ?>' WHERE username='boby' -- </code></p>
            <p>Then login as boby with password: <code>hacked123</code></p>
        </div>
        <?php else: ?>
        <div style="background: #f8d7da; color: #721c24; padding: 15px; border-radius: 4px;">
            User not found. Please login first.
        </div>
        <?php endif; ?>
    </div>
</body>
</html>

