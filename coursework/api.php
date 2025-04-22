<?php
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");

$db = new SQLite3("users.db");

$action = $_GET["action"] ?? "";
$response = ["success" => false, "message" => "Unknown action"];

switch($action) {
    case "register":
        $data = json_decode(file_get_contents("php://input"), true);
        $username = $data["username"] ?? "";
        $password = $data["password"] ?? "";
        
        if(empty($username) || empty($password)) {
            $response = ["success" => false, "message" => "Username and password required"];
            break;
        }
        
        $stmt = $db->prepare("INSERT INTO users (username, password) VALUES (:username, :password)");
        $stmt->bindValue(":username", $username, SQLITE3_TEXT);
        $stmt->bindValue(":password", $password, SQLITE3_TEXT);
        
        if($stmt->execute()) {
            $response = ["success" => true, "message" => "User registered"];
        } else {
            $response = ["success" => false, "message" => "Registration failed"];
        }
        break;
        
    case "login":
        $data = json_decode(file_get_contents("php://input"), true);
        $username = $data["username"] ?? "";
        $password = $data["password"] ?? "";
        
        if(empty($username) || empty($password)) {
            $response = ["success" => false, "message" => "Username and password required"];
            break;
        }
        
        $stmt = $db->prepare("SELECT id FROM users WHERE username = :username AND password = :password");
        $stmt->bindValue(":username", $username, SQLITE3_TEXT);
        $stmt->bindValue(":password", $password, SQLITE3_TEXT);
        
        $result = $stmt->execute();
        if($row = $result->fetchArray()) {
            $response = ["success" => true, "message" => "Login successful"];
        } else {
            $response = ["success" => false, "message" => "Invalid credentials"];
        }
        break;
        
    case "check_username":
        $username = $_GET["username"] ?? "";
        
        if(empty($username)) {
            $response = ["success" => false, "message" => "Username required"];
            break;
        }
        
        $stmt = $db->prepare("SELECT id FROM users WHERE username = :username");
        $stmt->bindValue(":username", $username, SQLITE3_TEXT);
        
        $result = $stmt->execute();
        if($result->fetchArray()) {
            $response = ["success" => true, "message" => "Username exists"];
        } else {
            $response = ["success" => false, "message" => "Username available"];
        }
        break;
}

echo json_encode($response);
?>