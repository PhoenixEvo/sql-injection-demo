<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Defense - Prepared Statement Demo</title>
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
        .code-block {
            background-color: #f4f4f4;
            padding: 15px;
            border-radius: 4px;
            margin: 15px 0;
            overflow-x: auto;
        }
        .code-block code {
            font-family: 'Courier New', monospace;
            font-size: 14px;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>Defense - Prepared Statement Demo</h1>
        <div class="nav">
            <a href="index.php">Login (Vulnerable)</a>
            <a href="search.php">Search Products</a>
            <a href="profile.php">Edit Profile</a>
            <a href="defense.php">Defense (Secure)</a>
        </div>

        <h2>Secure Login with Prepared Statement</h2>
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

                // Hash password before comparison (password is stored as SHA1 hash in database)
                $password_hash = sha1($password);

                // SECURE CODE: Using Prepared Statement to prevent SQL Injection
                $stmt = $conn->prepare("SELECT * FROM users WHERE username=? AND password=?");
                $stmt->bind_param("ss", $username, $password_hash);
                $stmt->execute();
                $result = $stmt->get_result();

                echo "<div class='message info'>";
                echo "<strong>SQL Query (Prepared Statement):</strong><br>";
                echo "<div class='code-block'>";
                echo "<code>SELECT * FROM users WHERE username=? AND password=?</code><br>";
                echo "<code>Parameters: username='" . htmlspecialchars($username) . "', password='" . htmlspecialchars($password_hash) . "' (SHA1 hash)</code>";
                echo "</div>";
                echo "<p><strong>Note:</strong> The query structure is fixed. User input is treated as data only, not as SQL code. Password is hashed before comparison.</p>";
                echo "</div>";

                if ($result && $result->num_rows > 0) {
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

                $stmt->close();
                $conn->close();
            } catch (Exception $e) {
                echo "<div class='message error'>Error: " . htmlspecialchars($e->getMessage()) . "</div>";
            }
        }
        ?>

        <div class="message info">
            <h3>How Prepared Statement Prevents SQL Injection:</h3>
            <p><strong>1. Query Structure is Fixed:</strong> The SQL query structure is compiled first with placeholders (?).</p>
            <p><strong>2. Data is Bound Separately:</strong> User input is bound to placeholders as data, not as SQL code.</p>
            <p><strong>3. No Code Execution:</strong> Even if user input contains SQL code (like <code>' OR '1'='1</code>), it will be treated as literal string data.</p>
            <p><strong>4. Try SQL Injection:</strong> Try the same payloads from the vulnerable login page. They won't work here!</p>
            <p><strong>Example payloads that won't work:</strong></p>
            <ul>
                <li>Username: <code>admin' #</code> - Will search for username literally "admin' #"</li>
                <li>Username: <code>' OR '1'='1</code> - Will search for username literally "' OR '1'='1"</li>
            </ul>
        </div>
    </div>
</body>
</html>

