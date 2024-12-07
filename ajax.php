
<?php
include 'db_connect.php';

if (isset($_GET['action'])) {
    $action = $_GET['action'];

    if ($action == 'save_supply') {
        save_supply();
    } elseif ($action == 'delete_supply') {
        delete_supply();
    }
}

function save_supply() {
    global $conn;

    $id = isset($_POST['id']) ? $_POST['id'] : '';
    $name = isset($_POST['name']) ? $_POST['name'] : '';

    if (empty($name)) {
        echo "0"; // Error: Supply name cannot be empty
        return;
    }

    if (empty($id)) {
        // Insert new supply
        $stmt = $conn->prepare("INSERT INTO supply_list (name) VALUES (?)");
        $stmt->bind_param("s", $name);
        if ($stmt->execute()) {
            echo "1"; // Successfully added
        } else {
            echo "0"; // Error
        }
    } else {
        // Update existing supply
        $stmt = $conn->prepare("UPDATE supply_list SET name = ? WHERE id = ?");
        $stmt->bind_param("si", $name, $id);
        if ($stmt->execute()) {
            echo "2"; // Successfully updated
        } else {
            echo "0"; // Error
        }
    }
}

function delete_supply() {
    global $conn;

    $id = isset($_POST['id']) ? $_POST['id'] : '';

    if (!empty($id)) {
        $stmt = $conn->prepare("DELETE FROM supply_list WHERE id = ?");
        $stmt->bind_param("i", $id);
        if ($stmt->execute()) {
            echo "1"; // Successfully deleted
        } else {
            echo "0"; // Error
        }
    } else {
        echo "0"; // Error: Invalid ID
    }
}
?>

<?php
include 'db_connect.php';

if (isset($_GET['action'])) {
    $action = $_GET['action'];

    if ($action == 'save_category') {
        save_category();
    } elseif ($action == 'delete_category') {
        delete_category();
    }
}

function save_category() {
    global $conn;

    $id = isset($_POST['id']) ? $_POST['id'] : '';
    $name = isset($_POST['name']) ? $_POST['name'] : '';
    $price = isset($_POST['price']) ? $_POST['price'] : '';

    if (empty($name) || empty($price)) {
        echo "0"; // Error: Category name or price is missing
        return;
    }

    if (empty($id)) {
        // Insert new category
        $stmt = $conn->prepare("INSERT INTO laundry_categories (name, price) VALUES (?, ?)");
        $stmt->bind_param("sd", $name, $price);
        if ($stmt->execute()) {
            echo "1"; // Successfully added
        } else {
            echo "0"; // Error
        }
    } else {
        // Update existing category
        $stmt = $conn->prepare("UPDATE laundry_categories SET name = ?, price = ? WHERE id = ?");
        $stmt->bind_param("sdi", $name, $price, $id);
        if ($stmt->execute()) {
            echo "2"; // Successfully updated
        } else {
            echo "0"; // Error
        }
    }
}

function delete_category() {
    global $conn;

    $id = isset($_POST['id']) ? $_POST['id'] : '';

    if (!empty($id)) {
        $stmt = $conn->prepare("DELETE FROM laundry_categories WHERE id = ?");
        $stmt->bind_param("i", $id);
        if ($stmt->execute()) {
            echo "1"; // Successfully deleted
        } else {
            echo "0"; // Error
        }
    } else {
        echo "0"; // Error: Invalid ID
    }
}
?>

<?php
include 'db_connect.php';

if(isset($_GET['action'])) {
    if($_GET['action'] == 'save_laundry') {
        // Save Laundry
        $id = isset($_POST['id']) ? $_POST['id'] : '';
        $customer_name = $_POST['customer_name'];
        $remarks = $_POST['remarks'];
        $status = isset($_POST['status']) ? $_POST['status'] : 0;
        $total_amount = $_POST['tamount'];
        $pay_status = isset($_POST['pay']) ? 1 : 0;
        $amount_tendered = isset($_POST['tendered']) ? $_POST['tendered'] : 0;
        $amount_change = isset($_POST['change']) ? $_POST['change'] : 0;

        if($id == '') {
            // Insert new laundry record
            $query = $conn->prepare("INSERT INTO laundry_list (customer_name, remarks, status, total_amount, pay_status, amount_tendered, amount_change) VALUES (?, ?, ?, ?, ?, ?, ?)");
            $query->bind_param("ssiiddi", $customer_name, $remarks, $status, $total_amount, $pay_status, $amount_tendered, $amount_change);
            $query->execute();
            $laundry_id = $query->insert_id;
        } else {
            // Update existing laundry record
            $query = $conn->prepare("UPDATE laundry_list SET customer_name=?, remarks=?, status=?, total_amount=?, pay_status=?, amount_tendered=?, amount_change=? WHERE id=?");
            $query->bind_param("ssiiddii", $customer_name, $remarks, $status, $total_amount, $pay_status, $amount_tendered, $amount_change, $id);
            $query->execute();
            $laundry_id = $id;
        }

        // Save laundry items
        $conn->query("DELETE FROM laundry_items WHERE laundry_id = $laundry_id"); // Remove old items
        if(isset($_POST['laundry_category_id'])) {
            foreach($_POST['laundry_category_id'] as $key => $category_id) {
                $weight = $_POST['weight'][$key];
                $unit_price = $_POST['unit_price'][$key];
                $amount = $_POST['amount'][$key];
                $query = $conn->prepare("INSERT INTO laundry_items (laundry_id, laundry_category_id, weight, unit_price, amount) VALUES (?, ?, ?, ?, ?)");
                $query->bind_param("iiddi", $laundry_id, $category_id, $weight, $unit_price, $amount);
                $query->execute();
            }
        }

        echo $id == '' ? 1 : 2; // Return 1 for insert, 2 for update
    }

    if($_GET['action'] == 'delete_laundry') {
        // Delete Laundry
        $id = $_POST['id'];
        $conn->query("DELETE FROM laundry_list WHERE id = $id");
        $conn->query("DELETE FROM laundry_items WHERE laundry_id = $id");
        echo 1; // Return success response
    }
}

?>
<?php
session_start();

// Check if the logout action is triggered
if (isset($_GET['action']) && $_GET['action'] === 'logout') {
    // Destroy the session
    session_destroy();
    // Optionally, unset specific session variables if needed
    // unset($_SESSION['login_name']);
    // unset($_SESSION['login_type']);
}

?>

<?php
include 'db_connect.php';

if ($_GET['action'] == 'save_inv') {
    $id = $_POST['id'];
    if (empty($id)) {
        // Insert new record
        $qry = $conn->query("INSERT INTO inventory (supply_id, qty, stock_type, date_created) VALUES ('{$_POST['supply_id']}', '{$_POST['qty']}', '{$_POST['stock_type']}', NOW())");
        echo $qry ? 1 : 0;
    } else {
        // Update existing record
        $qry = $conn->query("UPDATE inventory SET supply_id = '{$_POST['supply_id']}', qty = '{$_POST['qty']}', stock_type = '{$_POST['stock_type']}' WHERE id = $id");
        echo $qry ? 1 : 0;
    }
}

if ($_GET['action'] == 'delete_inv') {
    $id = $_POST['id'];
    $qry = $conn->query("DELETE FROM inventory WHERE id = $id");
    echo $qry ? 1 : 0;
}
if ($_GET['action'] == 'get_laundry_details') {
    $id = $_POST['id'];
    $result = $conn->query("SELECT * FROM laundry_list WHERE id = $id");
    $laundry = $result->fetch_assoc();
    $items = [];
    $items_result = $conn->query("SELECT * FROM laundry_items WHERE laundry_id = $id");
    while ($item = $items_result->fetch_assoc()) {
        $items[] = [
            'category' => $item['laundry_category_id'],
            'weight' => $item['weight'],
            'unit_price' => $item['unit_price'],
            'amount' => $item['amount']
        ];
    }
    echo json_encode([
        'customer_name' => $laundry['customer_name'],
        'status' => $laundry['status'],
        'items' => $items,
        'total_amount' => $laundry['total_amount']
    ]);
}

?>
<?php
include 'db_connect.php';

if ($_GET['action'] == 'get_laundry_details') {
    $id = $_POST['id'];
    $query = "SELECT * FROM laundry_list1 WHERE id = $id";
    $result = $conn->query($query);
    
    if ($result->num_rows > 0) {
        $laundry = $result->fetch_assoc();
        
        // Fetch laundry items (if any) for the receipt
        $itemsQuery = "SELECT * FROM laundry_items WHERE laundry_id = $id"; // Example table for items
        $itemsResult = $conn->query($itemsQuery);
        $items = [];
        while ($item = $itemsResult->fetch_assoc()) {
            $items[] = $item;
        }
        
        // Send the response back to the frontend
        echo json_encode([
            'customer_name' => $laundry['customer_name'],
            'status' => $laundry['status'],
            'date_created' => $laundry['date_created'],
            'items' => $items,
            'total_amount' => $laundry['total_amount'] // Example, you can calculate the total from items
        ]);
    } else {
        echo json_encode(['error' => 'Laundry not found']);
    }
}
?>
<?php
include 'db_connect.php';

// Enable error reporting
error_reporting(E_ALL);
ini_set('display_errors', 1);

if (isset($_POST['action']) && $_POST['action'] == 'save_laundry') {
    // Get values from the POST request
    $id = isset($_POST['id']) ? $_POST['id'] : null;
    $custom_name = $_POST['custom_name'];   
    $stat = $_POST['stat'];                 
    $queu = $_POST['queu'];                 
    $amount_total = $_POST['amount_total']; 
    $status_pay = $_POST['status_pay'];     
    $tendered_amount = $_POST['tendered_amount']; 
    $change_amount = $_POST['change_amount']; 
    $remark = $_POST['remark'];             
    $created_date = date('Y-m-d H:i:s');    

    // Validate required fields
    if (empty($custom_name) || empty($stat) || empty($queu)) {
        echo "Error: Missing required fields.";
        exit;
    }

    // Debug: Check values
    var_dump($custom_name, $stat, $queu, $amount_total, $status_pay, $tendered_amount, $change_amount, $remark, $created_date);

    // Prepare the query: If ID is set, perform update; otherwise, insert
    if ($id) {
        // Update query
        $stmt = $conn->prepare("UPDATE laundry_list1 SET custom_name = ?, stat = ?, queu = ?, amount_total = ?, status_pay = ?, tendered_amount = ?, change_amount = ?, remark = ?, created_date = ? WHERE id = ?");
        $stmt->bind_param('ssiidddssi', $custom_name, $stat, $queu, $amount_total, $status_pay, $tendered_amount, $change_amount, $remark, $created_date, $id);
    } else {
        // Insert query
        $stmt = $conn->prepare("INSERT INTO laundry_list1 (custom_name, stat, queu, amount_total, status_pay, tendered_amount, change_amount, remark, created_date) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)");
        $stmt->bind_param('ssiidddss', $custom_name, $stat, $queu, $amount_total, $status_pay, $tendered_amount, $change_amount, $remark, $created_date);
    }

    // Execute the query and check if it was successful
    if ($stmt->execute()) {
        echo "Success: Data inserted!";
    } else {
        echo "Error: " . $stmt->error; // Display MySQL error
    }

    $stmt->close();
    $conn->close();
}


?>

<?php
include 'db_connect.php';

if (isset($_GET['action'])) {
    $action = $_GET['action'];

    // Handle Delete Laundry
    if ($action == 'delete_laundry' && isset($_POST['id'])) {
        $id = $_POST['id'];
        $sql = "DELETE FROM laundry_list WHERE id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("i", $id);
        if ($stmt->execute()) {
            echo 1; // Success
        } else {
            echo 0; // Failure
        }
        $stmt->close();
    }

    // Handle Print Receipt
    if ($action == 'print_receipt' && isset($_POST['id'])) {
        $id = $_POST['id'];
        $sql = "SELECT * FROM laundry_list WHERE id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("i", $id);
        $stmt->execute();
        $result = $stmt->get_result();
        
        if ($result->num_rows > 0) {
            $row = $result->fetch_assoc();

            // Get total_amount (assuming total_amount is a column in your database)
            $total_amount = isset($row['total_amount']) ? $row['total_amount'] : 0;

            // Generate the receipt HTML with enhanced styling and logo
            $receipt = '
            <div style="width: 600px; margin: 0 auto; font-family: Arial, sans-serif; padding: 30px; border: 2px solid #007bff; background-color: #f9f9f9; text-align: center; border-radius: 15px;">
                <img src="image/logo.jpg" alt="Habit Laba Laundry" style="width: 180px; margin-bottom: 20px;">
                <h2 style="font-size: 28px; color: #007bff; font-weight: bold;">Habit Laba Laundry Receipt</h2>
                <h3 style="font-size: 24px; color: #333; margin-bottom: 20px;">"Clean clothes, happy life!"</h3>
                <p style="font-size: 18px; margin: 10px 0; color: #333;"><strong>Customer Name:</strong> ' . ucwords($row['customer_name']) . '</p>
                <p style="font-size: 18px; margin: 10px 0; color: #333;"><strong>Date:</strong> ' . date("M d, Y", strtotime($row['date_created'])) . '</p>
                <p style="font-size: 18px; margin: 10px 0; color: #333;"><strong>Queue:</strong> ' . $row['queue'] . '</p>
                <p style="font-size: 18px; margin: 10px 0; color: #333;"><strong>Status:</strong> ' . 
                    ($row['status'] == 0 ? 'Pending' : 
                    ($row['status'] == 1 ? 'Processing' : 
                    ($row['status'] == 2 ? 'Ready to Claim' : 'Claimed'))) 
                    . '</p>
                <hr style="border: 1px solid #007bff; margin: 20px 0;">
                <p style="font-size: 20px; margin: 10px 0; font-weight: bold; color: #007bff;"><strong>Total Amount:</strong> ₱' . number_format($total_amount, 2) . '</p>
                <hr style="border: 1px solid #007bff; margin: 20px 0;">
                <p style="font-size: 18px; margin: 10px 0; color: #333;">Thank you for choosing Habit Laba Laundry! We appreciate your business.</p>
                <div style="font-size: 14px; color: #777; margin-top: 30px;">
                    <p>Address: Labuin, Pila Laguna</p>
                    
                </div>
            </div>';

            // Output the receipt HTML
            echo $receipt;
        } else {
            echo "No data found for the given ID.";
        }
    }
}



?>

<?php
include "db_connect.php";

if ($_GET['action'] == 'update_inventory') {
    $inventory_id = $_POST['inventory_id'];
    $quantity = $_POST['quantity'];

    // Update stock after subtracting the quantity
    $conn->query("UPDATE inventory SET qty = qty - $quantity WHERE id = $inventory_id");
    echo "success";
}

?>
<?php
// Assuming you have a connection to the database ($conn)

if (isset($_POST['submit_order'])) {
    $inventory_id = $_POST['inventory_item_id'];  // Selected item ID
    $quantity_used = $_POST['quantity'];  // Quantity user wants to use

    // Step 1: Fetch current inventory quantity
    $result = $conn->query("SELECT qty FROM inventory WHERE id = $inventory_id");
    $row = $result->fetch_assoc();
    $current_qty = $row['qty'];

    // Step 2: Check if enough stock is available
    if ($current_qty >= $quantity_used) {
        // Step 3: Reduce the quantity in the inventory
        $new_qty = $current_qty - $quantity_used;
        
        // Update the inventory table to reflect the new quantity
        $update_query = "UPDATE inventory SET qty = $new_qty WHERE id = $inventory_id";
        if ($conn->query($update_query)) {
            echo "Order saved and inventory updated successfully!";
        } else {
            echo "Error updating inventory: " . $conn->error;
        }
    } else {
        echo "Not enough stock available.";
    }
}
?>
<?php
include('db_connect.php');



// Your existing category save and update handling code
if (isset($_POST['action']) && $_POST['action'] == 'save_category') {
    $id = $_POST['id'];
    $name = $_POST['name'];
    $price = $_POST['price'];

    if (empty($id)) {
        // Insert new category
        $insert_query = "INSERT INTO laundry_categories (name, price) VALUES ('$name', '$price')";
        $conn->query($insert_query);
        echo 1; // Successfully added
    } else {
        // Update existing category
        $update_query = "UPDATE laundry_categories SET name = '$name', price = '$price' WHERE id = $id";
        $conn->query($update_query);
        echo 2; // Successfully updated
    }
}
// Assuming you are processing the form submission
if (isset($_POST['name']) && isset($_POST['price']) && isset($_POST['weight']) && isset($_POST['total_price'])) {
    $name = $_POST['name'];
    $price = $_POST['price'];
    $weight = $_POST['weight'];
    $total_price = $_POST['total_price'];

    // Insert or update the category with price and weight
    $sql = "INSERT INTO laundry_categories (name, price, weight, total_price) VALUES (:name, :price, :weight, :total_price)";
    $stmt = $conn->prepare($sql);
    $stmt->bindParam(':name', $name);
    $stmt->bindParam(':price', $price);
    $stmt->bindParam(':weight', $weight);
    $stmt->bindParam(':total_price', $total_price);
    $stmt->execute();
}
if($action == 'save_category') {
    $id = $_POST['id'];
    $name = $_POST['name'];
    $price = $_POST['price'];
    $kg_range = $_POST['kg_range'];

    if(empty($id)) {
        // Insert new category
        $sql = "INSERT INTO laundry_categories (name, price, kg_range) VALUES ('$name', '$price', '$kg_range')";
        $result = $conn->query($sql);
        echo $result ? 1 : 0;
    } else {
        // Update existing category
        $sql = "UPDATE laundry_categories SET name='$name', price='$price', kg_range='$kg_range' WHERE id=$id";
        $result = $conn->query($sql);
        echo $result ? 2 : 0;
    }
}

?>
<?php 
include('db_connect.php');

$cats = $conn->query("SELECT * FROM laundry_categories ORDER BY id ASC");
$i = 1;
while($row = $cats->fetch(PDO::FETCH_ASSOC)): 
?>
<tr>
    <td class="text-center"><?php echo $i++ ?></td>
    <td class="">
        <p>Name: <b><?php echo $row['name'] ?></b></p>
        <p>Price: <b><?php echo number_format($row['price'], 2) ?></b></p>
    </td>
    <td class="text-center">
        <button class="btn btn-sm btn-primary edit_cat" type="button" data-id="<?php echo $row['id'] ?>" data-name="<?php echo $row['name'] ?>" data-price="<?php echo $row['price'] ?>">Edit</button>
        <button class="btn btn-sm btn-danger delete_cat" type="button" data-id="<?php echo $row['id'] ?>">Delete</button>
    </td>
</tr>
<?php endwhile; ?>
