<?php
include('db_config.php');

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    if (
        isset($_POST['scoreId']) &&
        isset($_POST['userId']) &&
        isset($_POST['tournamentId']) &&
        isset($_POST['score'])
    ) {
        $scoreId = filter_var($_POST['scoreId'], FILTER_SANITIZE_STRING);
        $userId = filter_var($_POST['userId'], FILTER_SANITIZE_STRING);
        $tournamentId = filter_var($_POST['tournamentId'], FILTER_SANITIZE_STRING);
        $score = filter_var($_POST['score'], FILTER_SANITIZE_NUMBER_INT);

        // Add validation and error handling here if needed

        try {
            // Insert the score into the 'score' table
            $stmt = $pdo->prepare(
                "INSERT INTO score (score_id, user_id, tournament_id, score) VALUES (?, ?, ?, ?)"
            );

            if ($stmt->execute([$scoreId, $userId, $tournamentId, $score])) {
                // Calculate and update the user rank based on scores
                // Fetch all scores for the tournament
                $stmt = $pdo->prepare(
                    "SELECT user_id, score FROM score WHERE tournament_id = ? ORDER BY score DESC"
                );
                $stmt->execute([$tournamentId]);
                $scores = $stmt->fetchAll(PDO::FETCH_ASSOC);

                // Calculate ranks based on scores
                $rank = 0;
                $prevScore = PHP_INT_MAX; // Initialize with a high value
                foreach ($scores as $row) {
                    if ($row['score'] < $prevScore) {
                        $rank++;
                        $prevScore = $row['score'];
                    }
                    if ($row['user_id'] === $userId) {
                        break; // Found the user's score, no need to continue
                    }
                }

                http_response_code(201);
                echo json_encode(["message" => "Score submitted successfully.", "rank" => $rank]);
            } else {
                http_response_code(500);
                echo json_encode(["message" => "Failed to submit score."]);
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
