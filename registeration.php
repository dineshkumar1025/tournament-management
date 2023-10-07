<?php
header("Content-Type: application/json");

if ($_SERVER["REQUEST_METHOD"] === "POST") {
   
    $data = json_decode(file_get_contents("php://input"));

    if (
        isset($data->userId) &&
        isset($data->userName) &&
        isset($data->userEmail)
    ) {
      
        $userId = filter_var($data->userId, FILTER_SANITIZE_STRING);
        $userName = filter_var($data->userName, FILTER_SANITIZE_STRING);
        $userEmail = filter_var($data->userEmail, FILTER_SANITIZE_EMAIL);

        if (
            empty($userId) ||
            empty($userName) ||
            empty($userEmail) ||
            !filter_var($userEmail, FILTER_VALIDATE_EMAIL)
        ) {
            http_response_code(400); // Bad Request
            echo json_encode(["message" => "Invalid data provided."]);
            exit();
        }

      
        try {
           
            $host = "localhost";
            $dbname = "tournament";
            $username = "root"; 
            $password = "your_password";    

            $pdo = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
            $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

            $stmt = $pdo->prepare(
                "INSERT INTO user (user_id, name, email) VALUES (?, ?, ?)"
            );

            if ($stmt->execute([$userId, $userName, $userEmail])) {
                http_response_code(201); // Created
                echo json_encode(["message" => "User registered successfully."]);
            } else {
                http_response_code(500); // Internal Server Error
                echo json_encode(["message" => "Failed to register user."]);
            }
        } catch (PDOException $e) {
            http_response_code(500); // Internal Server Error
            echo json_encode(["message" => "Database error: " . $e->getMessage()]);
        }
    } else {
        http_response_code(400); // Bad Request
        echo json_encode(["message" => "Incomplete data provided."]);
    }
} else {
    http_response_code(405); // Method Not Allowed
    echo json_encode(["message" => "Method not allowed."]);
}
