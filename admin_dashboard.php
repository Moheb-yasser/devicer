<?php
session_start();

// Check if admin is logged in
if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    header('Location: admin_login.php');
    exit;
}

$username = $_SESSION['admin_username'] ?? 'Admin';

// Database connection
$conn = mysqli_connect('localhost', 'root', '', 'warehouse_db');

if (!$conn) {
    die("Database connection failed: " . mysqli_connect_error());
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body {
            font-family: Arial, sans-serif;
            background: #f5f7fa;
            color: #333;
            line-height: 1.6;
            padding: 20px;
        }
        .container {
            max-width: 1200px;
            margin: 0 auto;
        }
        header {
            background: #4CAF50;
            color: white;
            padding: 20px;
            border-radius: 8px;
            margin-bottom: 30px;
        }
        h1 { margin-bottom: 10px; }
        h2 {
            color: #333;
            margin: 30px 0 15px;
            padding-bottom: 10px;
            border-bottom: 2px solid #eee;
        }
        
        .status-box {
            background: #e8f5e9;
            padding: 15px;
            border-radius: 5px;
            margin-bottom: 20px;
            border-left: 5px solid #4CAF50;
        }
        
        table {
            width: 100%;
            border-collapse: collapse;
            background: white;
            box-shadow: 0 2px 5px rgba(0,0,0,0.1);
            border-radius: 8px;
            overflow: hidden;
            margin-bottom: 30px;
        }
        th {
            background: #4CAF50;
            color: white;
            padding: 15px;
            text-align: left;
        }
        td {
            padding: 12px 15px;
            border-bottom: 1px solid #eee;
        }
        tr:hover {
            background: #f9f9f9;
        }
        
        .search-box {
            background: white;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 2px 5px rgba(0,0,0,0.1);
            margin-bottom: 30px;
        }
        input[type="number"] {
            padding: 10px;
            border: 1px solid #ddd;
            border-radius: 4px;
            width: 200px;
        }
        button {
            padding: 10px 20px;
            background: #4CAF50;
            color: white;
            border: none;
            border-radius: 4px;
            cursor: pointer;
        }
        button:hover {
            background: #45a049;
        }
        
        .update-form {
            background: #e3f2fd;
            padding: 20px;
            border-radius: 8px;
            margin-top: 20px;
        }
        
        .message {
            padding: 15px;
            border-radius: 5px;
            margin: 15px 0;
        }
        .success {
            background: #d4edda;
            color: #155724;
            border: 1px solid #c3e6cb;
        }
        .error {
            background: #f8d7da;
            color: #721c24;
            border: 1px solid #f5c6cb;
        }
        
        .nav-links {
            margin-top: 30px;
            display: flex;
            gap: 15px;
        }
        .nav-links a {
            padding: 10px 20px;
            background: #666;
            color: white;
            text-decoration: none;
            border-radius: 4px;
        }
        .nav-links a:hover {
            opacity: 0.9;
        }
        .logout {
            background: #f44336 !important;
        }
    </style>
</head>
<body>
    <div class="container">
        <header>
            <h1>Admin Dashboard</h1>
            <p>Welcome, <strong><?php echo htmlspecialchars($username); ?></strong>! Manage your warehouse products below.</p>
        </header>

        <?php
        echo '<div class="status-box">';
        echo 'Connected to database: <strong>warehouse_db</strong><br>';
        echo 'Logged in as: <strong>' . htmlspecialchars($username) . '</strong>';
        echo '</div>';
        ?>

        <section class="search-box">
            <h2>Search Product by ID</h2>
            <form method="GET" action="">
                <input type="number" name="search_id" placeholder="Enter product ID" required>
                <button type="submit">Search</button>
            </form>
        </section>

        <?php
        // Handle product search
        if (isset($_GET['search_id']) && !empty($_GET['search_id'])) {
            $search_id = intval($_GET['search_id']);
            
            $search_sql = "SELECT * FROM items WHERE id = ?";
            $stmt = $conn->prepare($search_sql);
            $stmt->bind_param("i", $search_id);
            $stmt->execute();
            $search_result = $stmt->get_result();
            
            if ($search_result->num_rows > 0) {
                $product = $search_result->fetch_assoc();
                
                echo '<div class="search-box">';
                echo '<h3>Search Result:</h3>';
                echo '<p><strong>Product Found:</strong></p>';
                echo '<p>ID: ' . $product['id'] . '</p>';
                echo '<p>Name: ' . $product['name'] . '</p>';
                echo '<p>Current Quantity: <strong>' . $product['quantity'] . '</strong></p>';
                
                echo '<div class="update-form">';
                echo '<form method="POST" action="">';
                echo '<input type="hidden" name="update_id" value="' . $product['id'] . '">';
                echo '<input type="hidden" name="current_quantity" value="' . $product['quantity'] . '">';
                echo '<label>New Quantity (must be > ' . $product['quantity'] . '):</label><br>';
                echo '<input type="number" name="new_quantity" min="' . ($product['quantity'] + 1) . '" required>';
                echo '<button type="submit" name="update_btn">Update</button>';
                echo '</form>';
                echo '</div>';
                
                echo '</div>';
            } else {
                echo '<div class="message error">';
                echo 'No product found with ID: ' . $search_id;
                echo '</div>';
            }
            $stmt->close();
        }
        ?>

        <?php
        // Handle quantity update
        if (isset($_POST['update_btn'])) {
            $update_id = intval($_POST['update_id']);
            $current_qty = intval($_POST['current_quantity']);
            $new_qty = intval($_POST['new_quantity']);
            
            if ($new_qty > $current_qty) {
                $update_sql = "UPDATE items SET quantity = ? WHERE id = ?";
                $stmt = $conn->prepare($update_sql);
                $stmt->bind_param("ii", $new_qty, $update_id);
                
                if ($stmt->execute()) {
                    echo '<div class="message success">';
                    echo 'Quantity updated! Product ID ' . $update_id . ' changed from ' . $current_qty . ' to ' . $new_qty;
                    echo '</div>';
                } else {
                    echo '<div class="message error">';
                    echo 'Update failed: ' . $conn->error;
                    echo '</div>';
                }
                $stmt->close();
            } else {
                echo '<div class="message error">';
                echo 'New quantity must be greater than current quantity (' . $current_qty . ')';
                echo '</div>';
            }
        }
        ?>

        <section>
            <h2>All Products in Database</h2>
            <?php
            $sql = "SELECT * FROM items ORDER BY id";
            $result = $conn->query($sql);
            
            if ($result->num_rows > 0) {
                echo '<table>';
                echo '<tr>';
                echo '<th>ID</th>';
                echo '<th>Product Name</th>';
                echo '<th>Quantity</th>';
                echo '<th>Status</th>';
                echo '</tr>';
                
                while ($row = $result->fetch_assoc()) {
                    if ($row['quantity'] < 10) {
                        $status = '<span style="color: red; font-weight: bold;">Low Stock</span>';
                    } else if ($row['quantity'] < 5) {
                        $status = '<span style="color: #ff9900; font-weight: bold;">Very Low</span>';
                    } else {
                        $status = '<span style="color: green;">In Stock</span>';
                    }
                    
                    echo '<tr>';
                    echo '<td>' . $row['id'] . '</td>';
                    echo '<td>' . $row['name'] . '</td>';
                    echo '<td>' . $row['quantity'] . '</td>';
                    echo '<td>' . $status . '</td>';
                    echo '</tr>';
                }
                
                echo '</table>';
                
                $count = $result->num_rows;
                echo '<p><strong>Total Products:</strong> ' . $count . '</p>';
            } else {
                echo '<div class="message error">';
                echo 'No products found in database. Please run the SQL script to create sample data.';
                echo '</div>';
            }
            
            $conn->close();
            ?>
        </section>

        <div class="nav-links">
            <a href="logout.php" class="logout">← Logout</a>
            <a href="index.html">← Back to Home</a>
        </div>
    </div>
</body>
</html>