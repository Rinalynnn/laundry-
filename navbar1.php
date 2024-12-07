<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Check session variables
$login_type = isset($_SESSION['login_type']) ? $_SESSION['login_type'] : 0; // Default to guest
?>

<style>
    #sidebar {
        width: 250px;
        height: 100vh;
        position: fixed;
        top: 0;
        left: 0;
        background: #7f8c8d;  /* Grey background for the sidebar */
        color: white;
        font-family: Arial, sans-serif;
        overflow-y: auto;
        padding-top: 20px;
        box-shadow: 2px 0 5px rgba(0, 0, 0, 0.2);
    }

    .sidebar-list a {
        display: flex;
        align-items: center;
        padding: 15px 20px;
        color: white;
        text-decoration: none;
        transition: background 0.3s ease, transform 0.2s ease;
    }

    .sidebar-list a:hover {
        background: #95a5a6;  /* Lighter grey for hover effect */
        transform: translateX(5px);
    }

    .sidebar-list .icon-field {
        margin-right: 10px;
    }

    .sidebar-list a.active {
        background: #2c3e50;  /* Dark grey for active links */
        color: white;
    }
</style>


<nav id="sidebar">
    <div class="sidebar-list">
        <a href="" class=""><span class=""><i class=""></i></span> </a>
        <a href="index1.php?page=home1" class="nav-item nav-home"><span class="icon-field"><i class="fa fa-home"></i></span> Home</a>
        <a href="index1.php?page=laundry" class="nav-item nav-laundry"><span class="icon-field"><i class="fa fa-water"></i></span> Laundry List</a>
        <a href="index1.php?page=inventory1" class="nav-item nav-inventory"><span class="icon-field"><i class="fa fa-list-alt"></i></span> Inventory</a>
    </div>
</nav>

<script>
    // Highlight active page
    document.querySelectorAll('.nav-item').forEach(function (link) {
        if (link.classList.contains('nav-<?php echo isset($_GET["page"]) ? $_GET["page"] : ""; ?>')) {
            link.classList.add('active');
        }
    });
</script>

