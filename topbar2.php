<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// No longer fetching low-stock items since notification is removed.
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
        gap: 15px; /* Space between elements */
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
        <div class="brand-name">Habit Laba Laundry-Staff Labuin,Pila</div>
        <div class="right-side">
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
