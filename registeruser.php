<?php
include('db_config.php');

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    if (
        isset($_POST['userId']) &&
        isset($_POST['name']) &&
        isset($_POST['email'])
    ) {
        $userId = filter_var($_POST['userId'], FILTER_SANITIZE_STRING);
        $name = filter_var($_POST['name'], FILTER_SANITIZE_STRING);
        $email = filter_var($_POST['email'], FILTER_SANITIZE_EMAIL);

       

        try {
            $stmt = $pdo->prepare(
                "INSERT INTO user (user_id, name, email) VALUES (?, ?, ?)"
            );

            if ($stmt->execute([$userId, $name, $email])) {
                http_response_code(201);
                echo json_encode(["message" => "User registered successfully."]);
            } else {
                http_response_code(500);
                echo json_encode(["message" => "Failed to register user."]);
            }
        } catch (PDOException $e) {
            http_response_code(500);
            echo json_encode(["message" => "Database error: " . $e->getMessage()]);
        }
    } else {
        http_response_code(400);
        echo json_encode(["message" => "Incomplete data provided."]);
    }
} else {
    http_response_code(405);
    echo json_encode(["message" => "Method not allowed."]);
}
?>
