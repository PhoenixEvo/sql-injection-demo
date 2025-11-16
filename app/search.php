<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Product Search - SQL Injection Demo</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            max-width: 1000px;
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
        input[type="text"] {
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
        <h1>Product Search</h1>
        <div class="nav">
            <a href="index.php">Login</a>
            <a href="search.php">Search Products</a>
        </div>

        <h2>Search Products</h2>
        <form method="GET" action="">
            <div class="form-group">
                <label for="keyword">Search keyword:</label>
                <input type="text" id="keyword" name="keyword" value="<?php echo htmlspecialchars($_GET['keyword'] ?? ''); ?>" placeholder="Enter product name">
            </div>
            <button type="submit">Search</button>
        </form>

        <?php
        // Database configuration
        $db_host = getenv('DB_HOST') ?: 'db';
        $db_name = getenv('DB_NAME') ?: 'sqli_demo';
        $db_user = getenv('DB_USER') ?: 'demo_user';
        $db_pass = getenv('DB_PASS') ?: 'demo_pass';

        if (isset($_GET['keyword']) && $_GET['keyword'] !== '') {
            $keyword = $_GET['keyword'];

            try {
                $conn = new mysqli($db_host, $db_user, $db_pass, $db_name);
                
                if ($conn->connect_error) {
                    die("<div class='message error'>Connection failed: " . $conn->connect_error . "</div>");
                }

                // VULNERABLE CODE: Direct string concatenation - SQL Injection vulnerability!
                $sql = "SELECT * FROM products WHERE name LIKE '%$keyword%'";
                
                echo "<div class='message info'><strong>SQL Query:</strong> " . htmlspecialchars($sql) . "</div>";
                
                $result = $conn->query($sql);

                // Check for SQL errors
                if (!$result) {
                    echo "<div class='message error'>";
                    echo "<h3>SQL Error:</h3>";
                    echo "<p>" . htmlspecialchars($conn->error) . "</p>";
                    echo "</div>";
                } elseif ($result->num_rows > 0) {
                    echo "<table>";
                    echo "<tr><th>ID</th><th>Name</th><th>Price</th><th>Description</th></tr>";
                    while ($row = $result->fetch_assoc()) {
                        echo "<tr>";
                        echo "<td>" . htmlspecialchars($row['id']) . "</td>";
                        echo "<td>" . htmlspecialchars($row['name']) . "</td>";
                        echo "<td>$" . htmlspecialchars($row['price']) . "</td>";
                        echo "<td>" . htmlspecialchars($row['description']) . "</td>";
                        echo "</tr>";
                    }
                    echo "</table>";
                    echo "<p><strong>Found " . $result->num_rows . " product(s)</strong></p>";
                } else {
                    echo "<div class='message info'>No products found.</div>";
                }

                $conn->close();
            } catch (Exception $e) {
                echo "<div class='message error'>Error: " . htmlspecialchars($e->getMessage()) . "</div>";
            }
        } else {
            echo "<div class='message info'>Enter a keyword to search for products.</div>";
        }
        ?>

        <div class="message info">
            <h3>SQL Injection Demo:</h3>
            <p><strong>Normal search:</strong> <code>Laptop</code></p>
            <p><strong>Show all products:</strong> <code>' OR '1'='1</code></p>
            <p><strong>Extract database name:</strong> <code>' UNION SELECT 1, database(), 3, 4 --</code></p>
        </div>
    </div>
</body>
</html>

