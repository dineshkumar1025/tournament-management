<?php
include('db_config.php');

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    if (
        isset($_POST['tournamentId']) &&
        isset($_POST['name']) &&
        isset($_POST['startTime']) &&
        isset($_POST['endTime'])
    ) {
        $tournamentId = filter_var($_POST['tournamentId'], FILTER_SANITIZE_STRING);
        $name = filter_var($_POST['name'], FILTER_SANITIZE_STRING);
        $startTime = filter_var($_POST['startTime'], FILTER_SANITIZE_STRING);
        $endTime = filter_var($_POST['endTime'], FILTER_SANITIZE_STRING);

      
        try {
           
            $stmt = $pdo->prepare(
                "SELECT COUNT(*) FROM tournament WHERE (start_time <= ? AND end_time >= ?) OR (start_time >= ? AND start_time <= ?)"
            );
            $stmt->execute([$startTime, $endTime, $startTime, $endTime]);
            $count = $stmt->fetchColumn();

            if ($count > 0) {
                http_response_code(400);
                echo json_encode(["message" => "Tournament with overlapping timing already exists."]);
            } else {
               
                $stmt = $pdo->prepare(
                    "INSERT INTO tournament (tournament_id, name, start_time, end_time) VALUES (?, ?, ?, ?)"
                );

                if ($stmt->execute([$tournamentId, $name, $startTime, $endTime])) {
                    http_response_code(201);
                    echo json_encode(["message" => "Tournament created successfully."]);
                } else {
                    http_response_code(500);
                    echo json_encode(["message" => "Failed to create tournament."]);
                }
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
