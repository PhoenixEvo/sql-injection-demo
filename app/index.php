<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SQL Injection Demo</title>
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
        input[type="text"], input[type="password"] {
            width: 100%;
            padding: 10px;
            border: 1px solid #ddd;
            border-radius: 4px;
            box-sizing: border-box;
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
        .message {
            margin-top: 20px;
            padding: 15px;
            border-radius: 4px;
        }
        .success {
            background-color: #d4edda;
            color: #155724;
            border: 1px solid #c3e6cb;
        }
        .error {
            background-color: #f8d7da;
            color: #721c24;
            border: 1px solid #f5c6cb;
        }
        .info {
            background-color: #d1ecf1;
            color: #0c5460;
            border: 1px solid #bee5eb;
            margin-top: 20px;
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
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }
        th, td {
            padding: 12px;
            text-align: left;
            border-bottom: 1px solid #ddd;
        }
        th {
            background-color: #4CAF50;
            color: white;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>SQL Injection Demo</h1>
        <div class="nav">
            <a href="index.php">Login</a>
            <a href="search.php">Search Products</a>
        </div>

        <h2>Login Form</h2>
        <form method="POST" action="">
            <div class="form-group">
                <label for="username">Username:</label>
                <input type="text" id="username" name="username" required>
            </div>
            <div class="form-group">
                <label for="password">Password:</label>
                <input type="password" id="password" name="password" required>
            </div>
            <button type="submit">Login</button>
        </form>

        <?php
        // Database configuration
        $db_host = getenv('DB_HOST') ?: 'db';
        $db_name = getenv('DB_NAME') ?: 'sqli_demo';
        $db_user = getenv('DB_USER') ?: 'demo_user';
        $db_pass = getenv('DB_PASS') ?: 'demo_pass';

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $username = $_POST['username'] ?? '';
            $password = $_POST['password'] ?? '';

            try {
                $conn = new mysqli($db_host, $db_user, $db_pass, $db_name);
                
                if ($conn->connect_error) {
                    die("<div class='message error'>Connection failed: " . $conn->connect_error . "</div>");
                }

                // VULNERABLE CODE: Direct string concatenation - SQL Injection vulnerability!
                $sql = "SELECT * FROM users WHERE username='$username' AND password='$password'";
                
                echo "<div class='message info'><strong>SQL Query:</strong> " . htmlspecialchars($sql) . "</div>";
                
                $result = $conn->query($sql);

                // Check for SQL errors
                if (!$result) {
                    echo "<div class='message error'>";
                    echo "<h3>SQL Error:</h3>";
                    echo "<p>" . htmlspecialchars($conn->error) . "</p>";
                    echo "</div>";
                } elseif ($result->num_rows > 0) {
                    $user = $result->fetch_assoc();
                    echo "<div class='message success'>";
                    echo "<h3>Login Successful!</h3>";
                    echo "<p><strong>Welcome, " . htmlspecialchars($user['username']) . "!</strong></p>";
                    echo "<p>Email: " . htmlspecialchars($user['email']) . "</p>";
                    echo "<p>Role: " . htmlspecialchars($user['role']) . "</p>";
                    echo "</div>";
                } else {
                    echo "<div class='message error'>Invalid username or password!</div>";
                }

                $conn->close();
            } catch (Exception $e) {
                echo "<div class='message error'>Error: " . htmlspecialchars($e->getMessage()) . "</div>";
            }
        }
        ?>

        <div class="message info">
            <h3>Demo Instructions:</h3>
            <p><strong>Normal login:</strong> username: <code>admin</code>, password: <code>admin123</code></p>
            <p><strong>SQL Injection attack (Method 1 - Comment with #):</strong><br>
            username: <code>admin' #</code>, password: (leave empty or any value)</p>
            <p><strong>SQL Injection attack (Method 1b - Comment with --):</strong><br>
            username: <code>admin' -- </code> (note: space after --), password: (leave empty or any value)</p>
            <p><strong>SQL Injection attack (Method 2 - OR condition):</strong><br>
            username: <code>' OR '1'='1</code>, password: <code>' OR '1'='1</code></p>
            <p><strong>Note:</strong> Method 1 with <code>#</code> is simplest. The comment will ignore the password check.</p>
        </div>
    </div>
</body>
</html>

