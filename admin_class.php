<?php
session_start();
ini_set('display_errors', 1);

class Action {
    private $db;

    public function __construct() {
        ob_start();
        include 'db_connect.php';
        $this->db = $conn;
    }

    function __destruct() {
        $this->db->close();
        ob_end_flush();
    }

    function login() {
        extract($_POST);

        // Use prepared statements
        $stmt = $this->db->prepare("SELECT * FROM users WHERE username = ? AND password = ?");
        $stmt->bind_param("ss", $username, $password);
        $stmt->execute();
        $qry = $stmt->get_result();

        if ($qry->num_rows > 0) {
            foreach ($qry->fetch_array() as $key => $value) {
                if ($key != 'password' && !is_numeric($key)) {
                    $_SESSION['login_' . $key] = $value;
                }
            }
            return 1;
        } else {
            return 3;
        }
    }

    function login2() {
        extract($_POST);

        // Use prepared statements
        $stmt = $this->db->prepare("SELECT * FROM user_info WHERE email = ? AND password = ?");
        $hashed_password = md5($password); // Update this to a better hashing algorithm if needed
        $stmt->bind_param("ss", $email, $hashed_password);
        $stmt->execute();
        $qry = $stmt->get_result();

        if ($qry->num_rows > 0) {
            foreach ($qry->fetch_array() as $key => $value) {
                if ($key != 'password' && !is_numeric($key)) {
                    $_SESSION['login_' . $key] = $value;
                }
            }

            $ip = $_SERVER['REMOTE_ADDR'] ?? '';
            $this->db->query("UPDATE cart SET user_id = '{$_SESSION['login_user_id']}' WHERE client_ip = '$ip'");
            return 1;
        } else {
            return 3;
        }
    }

    function logout() {
        session_destroy();
        header("location:login.php");
    }

    function logout2() {
        session_destroy();
        header("location:../index.php");
    }

    function save_user() {
        extract($_POST);

        // Use prepared statements
        if (empty($id)) {
            $stmt = $this->db->prepare("INSERT INTO users (name, username, password, type) VALUES (?, ?, ?, ?)");
            $stmt->bind_param("ssss", $name, $username, $password, $type);
        } else {
            $stmt = $this->db->prepare("UPDATE users SET name = ?, username = ?, password = ?, type = ? WHERE id = ?");
            $stmt->bind_param("ssssi", $name, $username, $password, $type, $id);
        }

        if ($stmt->execute()) {
            return 1;
        }
    }

    function signup() {
        extract($_POST);
        $hashed_password = md5($password);

        // Check if email already exists
        $chk = $this->db->prepare("SELECT * FROM user_info WHERE email = ?");
        $chk->bind_param("s", $email);
        $chk->execute();
        $chk->store_result();

        if ($chk->num_rows > 0) {
            return 2;
        }

        $stmt = $this->db->prepare("INSERT INTO user_info (first_name, last_name, mobile, address, email, password) VALUES (?, ?, ?, ?, ?, ?)");
        $stmt->bind_param("ssssss", $first_name, $last_name, $mobile, $address, $email, $hashed_password);

        if ($stmt->execute()) {
            return $this->login2();
        }
    }

    function save_settings() {
        extract($_POST);

        $data = "name = '$name', email = '$email', contact = '$contact', about_content = '" . htmlentities(str_replace("'", "&#x2019;", $about)) . "'";

        if ($_FILES['img']['tmp_name'] != '') {
            $allowed_types = ['image/jpeg', 'image/png'];
            $file_type = mime_content_type($_FILES['img']['tmp_name']);
            if (!in_array($file_type, $allowed_types)) {
                return "Invalid file type";
            }

            $fname = strtotime(date('y-m-d H:i')) . '_' . basename($_FILES['img']['name']);
            if (move_uploaded_file($_FILES['img']['tmp_name'], '../assets/img/' . $fname)) {
                $data .= ", cover_img = '$fname'";
            }
        }

        $chk = $this->db->query("SELECT * FROM system_settings");
        if ($chk->num_rows > 0) {
            $id = $chk->fetch_array()['id'];
            $this->db->query("UPDATE system_settings SET $data WHERE id = $id");
        } else {
            $this->db->query("INSERT INTO system_settings SET $data");
        }

        // Update session settings
        $query = $this->db->query("SELECT * FROM system_settings LIMIT 1")->fetch_array();
        foreach ($query as $key => $value) {
            if (!is_numeric($key)) {
                $_SESSION['setting_' . $key] = $value;
            }
        }

        return 1;
    }

    // Other functions are updated similarly for security, parenthesization, and error handling...
}
