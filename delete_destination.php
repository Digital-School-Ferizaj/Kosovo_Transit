<?php
require_once 'config.php';

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    echo json_encode(['success' => false, 'message' => 'Not logged in']);
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['id'])) {
    $destination_id = filter_input(INPUT_POST, 'id', FILTER_SANITIZE_NUMBER_INT);
    $user_id = $_SESSION['user_id'];
    
    try {
        // Verify that the destination belongs to the user
        $stmt = $pdo->prepare("SELECT id FROM user_destinations WHERE id = ? AND user_id = ?");
        $stmt->execute([$destination_id, $user_id]);
        
        if ($stmt->fetch()) {
            // Delete the destination
            $stmt = $pdo->prepare("DELETE FROM user_destinations WHERE id = ?");
            $stmt->execute([$destination_id]);
            echo json_encode(['success' => true]);
        } else {
            echo json_encode(['success' => false, 'message' => 'Destination not found']);
        }
    } catch (PDOException $e) {
        echo json_encode(['success' => false, 'message' => 'Database error']);
    }
} else {
    echo json_encode(['success' => false, 'message' => 'Invalid request']);
}
?> 