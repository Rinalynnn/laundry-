<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Initialize the array for low-stock items
$lowStockItems = [];

try {
    // Database connection setup
    $conn = new PDO("mysql:host=localhost;dbname=laundry_db", "root", "");
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // SQL query to fetch low-stock items based on qty <= 50% of required_stock
    $stmt = $conn->prepare("SELECT stock_type, qty, required_stock 
                            FROM inventory 
                            WHERE qty <= required_stock * 0.5");
    $stmt->execute();
    $lowStockItems = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Debugging: Log the result to check if low-stock items are fetched
    error_log("Low Stock Items: " . print_r($lowStockItems, true));

} catch (PDOException $e) {
    error_log("Database Error: " . $e->getMessage());
}
?>

<style>
    .navbar {
        background: #95a5a6;  /* Grey background */
        padding: 0.5rem 1rem;
        display: flex;
        justify-content: space-between;
        align-items: center;
        box-shadow: 0 2px 5px rgba(0, 0, 0, 0.2);
    }

    .navbar .brand-name {
        color: white;
        font-size: 1.6rem;
        font-weight: 500;
    }

    .right-side {
        display: flex;
        align-items: center;
        gap: 15px; /* Space between notification and logout button */
    }

    .notification {
        position: relative;
        cursor: pointer;
    }

    .notification .count {
        position: absolute;
        top: -5px;
        right: -10px;
        background: #e74c3c;
        color: white;
        border-radius: 50%;
        padding: 5px 8px;
        font-size: 0.75rem;
        font-weight: bold;
    }

    .notification-dropdown {
        display: none;
        position: absolute;
        top: 30px;
        right: 0;
        background: white;
        color: black;
        width: 300px;
        box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
        border-radius: 5px;
        z-index: 10;
    }

    .notification-dropdown ul {
        list-style: none;
        padding: 0;
        margin: 0;
    }

    .notification-dropdown li {
        padding: 10px;
        border-bottom: 1px solid #ccc;
        font-size: 0.9rem;
    }

    .notification-dropdown li:last-child {
        border-bottom: none;
    }

    .notification-dropdown li:hover {
        background: #f4f4f4;
    }

    .notification:hover .notification-dropdown {
        display: block;
    }

    .logout-button {
        background: none;
        border: none;
        color: white;
        font-size: 0.9rem;
        cursor: pointer;
        transition: opacity 0.3s ease;
    }

    .logout-button:hover {
        opacity: 0.7;
    }
</style>

<nav class="navbar navbar-dark fixed-top">
    <div class="container-fluid">
        <div class="brand-name">Habit Laba Laundry</div>
        <div class="right-side">
            <!-- Notifications -->
            <div class="notification">
                <i class="fa fa-bell" style="font-size: 1.5rem; color: white;"></i>
                <?php if (!empty($lowStockItems)): ?>
                    <div class="count"><?php echo count($lowStockItems); ?></div>
                <?php endif; ?>
                <div class="notification-dropdown">
                    <ul>
                        <?php if (!empty($lowStockItems)): ?>
                            <?php foreach ($lowStockItems as $item): ?>
                                <li>
                                    <strong><?php echo htmlspecialchars($item['stock_type']); ?></strong>:
                                    <?php echo htmlspecialchars($item['qty']); ?> / <?php echo htmlspecialchars($item['required_stock']); ?> in stock
                                </li>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <li>No low-stock items.</li>
                        <?php endif; ?>
                    </ul>
                </div>
            </div>

            <!-- Logout Button -->
            <button class="logout-button" id="logout-link">Logout</button>
        </div>
    </div>
</nav>

<script>
    // Logout functionality using fetch
    document.getElementById('logout-link').addEventListener('click', function (e) {
        e.preventDefault();
        
        // Perform logout via AJAX (ajax.php)
        fetch('ajax.php?action=logout')
            .then(response => response.text())
            .then(() => {
                // Redirect to login page
                window.location.href = 'login.php';
            })
            .catch(error => {
                console.error('Error during logout:', error);
            });
    });
</script>
