<?php
function dbConnect()
{
    $servername = "localhost";
    $username = "root";
    $password = "";
    $dbname = "simple_delivery_suai";

    $conn = new mysqli($servername, $username, $password, $dbname);
    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }
    return $conn;
}

function userExists($login)
{
    $conn = dbConnect();
    $stmt = $conn->prepare("SELECT id FROM users WHERE login = ?");
    $stmt->bind_param("s", $login);
    $stmt->execute();
    $result = $stmt->get_result();
    $userExists = $result->num_rows > 0;
    $stmt->close();
    $conn->close();
    return $userExists;
}


function registerUser($login, $email, $password)
{
    $conn = dbConnect();
    $hashed_password = password_hash($password, PASSWORD_DEFAULT);
    $stmt = $conn->prepare("INSERT INTO users (login, email, password) VALUES (?, ?, ?)");
    $stmt->bind_param("sss", $login, $email, $hashed_password);
    $stmt->execute();
    $stmt->close();
    $conn->close();
}

function loginUser($login, $password)
{
    $conn = dbConnect();
    $stmt = $conn->prepare("SELECT id, password FROM users WHERE login = ?");
    $stmt->bind_param("s", $login);
    $stmt->execute();
    $result = $stmt->get_result();
    if ($result->num_rows === 1) {
        $user = $result->fetch_assoc();
        if (password_verify($password, $user['password'])) {
            $stmt->close();
            $conn->close();
            return $user['id'];
        }
    }
    $stmt->close();
    $conn->close();
    return false;
}

function createOrder($userId, $recipientName, $recipientLatitude, $recipientLongitude, $weight, $dimensions, $status)
{
    $conn = dbConnect();
    $stmt = $conn->prepare("INSERT INTO orders (user_id, recipient_name, recipient_latitude, recipient_longitude, weight, dimensions, status) VALUES (?, ?, ?, ?, ?, ?, ?)");
    $stmt->bind_param("issddss", $userId, $recipientName, $recipientLatitude, $recipientLongitude, $weight, $dimensions, $status);

    if ($stmt->execute()) {
        $orderId = $stmt->insert_id;
        createTrackOrder($orderId, $recipientLatitude, $recipientLongitude);
    }

    $stmt->close();
    $conn->close();
    return $orderId;
}

function createTrackOrder($orderId, $initialLatitude, $initialLongitude)
{
    $conn = dbConnect();
    $stmt = $conn->prepare("INSERT INTO orders_tracking (order_id, current_latitude, current_longitude) VALUES (?, ?, ?)");
    $stmt->bind_param("idd", $orderId, $initialLatitude, $initialLongitude);
    $stmt->execute();
    $trackOrderId = $stmt->insert_id;
    $stmt->close();
    $conn->close();
    return $trackOrderId;
}

function trackOrder($orderId)
{
    $conn = dbConnect();
    $stmt = $conn->prepare("
        SELECT ot.*, o.status
        FROM orders_tracking ot
        JOIN orders o ON ot.order_id = o.id
        WHERE ot.order_id = ?
    ");
    $stmt->bind_param("i", $orderId);
    $stmt->execute();
    $result = $stmt->get_result();
    $trackingInfo = $result->fetch_all(MYSQLI_ASSOC);
    $stmt->close();
    $conn->close();
    return $trackingInfo;
}


function updateOrderStatus($orderId, $status)
{
    $conn = dbConnect();
    $stmt = $conn->prepare("UPDATE orders SET status = ? WHERE id = ?");
    $stmt->bind_param("si", $status, $orderId);
    $stmt->execute();
    $stmt->close();
    $conn->close();
}

function getUserOrders($userId)
{
    $conn = dbConnect();
    $stmt = $conn->prepare("SELECT id, recipient_name, status FROM orders WHERE user_id = ?");
    $stmt->bind_param("i", $userId);
    $stmt->execute();
    $result = $stmt->get_result();
    $orders = $result->fetch_all(MYSQLI_ASSOC);
    $stmt->close();
    $conn->close();
    return $orders;
}

function getUserInfo($userId)
{
    $conn = dbConnect();
    $stmt = $conn->prepare("SELECT login, email FROM users WHERE id = ?");
    $stmt->bind_param("i", $userId);
    $stmt->execute();
    $result = $stmt->get_result();
    if ($row = $result->fetch_assoc()) {
        return $row;
    } else {
        return null;
    }
}

function isAdmin($userId)
{
    $conn = dbConnect();
    $stmt = $conn->prepare("SELECT is_admin FROM users WHERE id = ?");
    $stmt->bind_param("i", $userId);
    $stmt->execute();
    $result = $stmt->get_result();
    $isAdmin = $result->fetch_assoc()['is_admin'];
    $stmt->close();
    $conn->close();
    return $isAdmin;
}

function getAllOrders()
{
    $conn = dbConnect();
    $query = "SELECT * FROM orders";
    $result = $conn->query($query);

    $orders = [];
    if ($result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            $orders[] = $row;
        }
    }
    $conn->close();
    return $orders;
}

function updateOrderLocation($orderId, $latitude, $longitude)
{
    $conn = dbConnect();
    $stmt = $conn->prepare("UPDATE orders_tracking SET current_latitude = ?, current_longitude = ? WHERE order_id = ?");
    $stmt->bind_param("ddi", $latitude, $longitude, $orderId);
    $stmt->execute();
    $stmt->close();
    $conn->close();
}

function deleteOrder($orderId)
{
    $conn = dbConnect();
    $stmt = $conn->prepare("DELETE FROM orders WHERE id = ?");
    $stmt->bind_param("i", $orderId);
    $result = $stmt->execute();

    $stmt->close();
    $conn->close();

    return $result;
}
