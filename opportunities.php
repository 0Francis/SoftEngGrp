<?php
require_once __DIR__ . '/db.php';

header("Content-Type: application/json");

$pdo = getDBConnection();
$action = $_GET['action'] ?? '';

switch ($action) {

    // READ all opportunities
    case 'read':
        try {
            $stmt = $pdo->query("
                SELECT 
                    o.opportunity_id AS opportunityid,
                    o.title,
                    o.description,
                    o.duration,
                    o.deadline,
                    org.org_name AS organization,
                    o.location,
                    o.category,
                    o.status
                FROM opportunities o
                JOIN organizations org ON o.org_id = org.org_id
                ORDER BY o.date_posted DESC
            ");
            $rows = $stmt->fetchAll();
            echo json_encode($rows);
        } catch (PDOException $e) {
            echo json_encode(['error' => $e->getMessage()]);
        }
        break;

    // CREATE new opportunity
    case 'create':
        $data = json_decode(file_get_contents('php://input'), true);
        if (!$data) { echo json_encode(['error' => 'Invalid data']); exit; }

        try {
            $stmt = $pdo->prepare("
                INSERT INTO opportunities 
                (org_id, title, description, skills_required, duration, deadline, location, category, status)
                VALUES (:org_id, :title, :description, :skills_required, :duration, :deadline, :location, :category::opportunity_category, :status::opportunity_status)
            ");
            $stmt->execute([
                ':org_id' => $data['org_id'],
                ':title' => $data['title'],
                ':description' => $data['description'],
                ':skills_required' => $data['skills_required'] ?? '',
                ':duration' => $data['duration'] ?? '',
                ':deadline' => $data['deadline'] ?? null,
                ':location' => $data['location'] ?? '',
                ':category' => $data['category'] ?? 'Other',
                ':status' => $data['status'] ?? 'Open'
            ]);
            echo json_encode(['message' => 'Opportunity created successfully!']);
        } catch (PDOException $e) {
            echo json_encode(['error' => $e->getMessage()]);
        }
        break;

    // DELETE opportunity
    case 'delete':
        $id = $_GET['id'] ?? 0;
        if (!$id) { echo json_encode(['error' => 'Missing ID']); exit; }

        try {
            $stmt = $pdo->prepare("DELETE FROM opportunities WHERE opportunity_id = :id");
            $stmt->execute([':id' => $id]);
            echo json_encode(['message' => 'Opportunity deleted successfully']);
        } catch (PDOException $e) {
            echo json_encode(['error' => $e->getMessage()]);
        }
        break;

    default:
        echo json_encode(['error' => 'Invalid action']);
}
