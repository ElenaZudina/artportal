<?php
class PurchaseRequest {
    
    public static function create($userId, $paintingId) {
        if ($userId === null || $paintingId === null) {
            return [
                'success' => false,
                'message' => 'Missing user or painting id'
            ];
        }

        $userId = (int)$userId;
        $paintingId = (int)$paintingId;

        if ($userId <= 0 || $paintingId <= 0) {
            return [
                'success' => false,
                'message' => 'Invalid user or painting id'
            ];
        }

        $db = new Database();
        
        // Создание новой заявки
        $sql = 'INSERT INTO `purchase_requests` (user_id, painting_id) VALUES (?, ?)';
        $result = $db->executeRun($sql, [$userId, $paintingId]);

        if ($result) {
            return [
                'success' => true,
                'message' => 'Request sent successfully',
                'id' => $db->getLastInsertId()
            ];
        } else {
            return [
                'success' => false,
                'message' => 'Failed to create request'
            ];
        }
    }
    
    public static function getLastRequestTime($userId, $paintingId) {
        $db = new Database();
        $sql = 'SELECT UNIX_TIMESTAMP(created_at) as created_timestamp FROM `purchase_requests` 
                WHERE user_id = ? AND painting_id = ? 
                ORDER BY created_at DESC 
                LIMIT 1';
        $result = $db->getOne($sql, [$userId, $paintingId]);
        
        if ($result && isset($result['created_timestamp']) && !empty($result['created_timestamp'])) {
            return (int)$result['created_timestamp'];
        }
        return null;
    }
    
    public static function getRequestById($requestId) {
        $sql = 'SELECT pr.*, 
                       u.email AS user_email, 
                       u.name AS user_name,
                       p.title AS painting_title,
                       p.artist_id,
                       a.name AS artist_name,
                       a.email AS artist_email
                FROM `purchase_requests` pr
                JOIN `users` u ON pr.user_id = u.id
                JOIN `paintings` p ON pr.painting_id = p.id
                JOIN `artists` a ON p.artist_id = a.id
                WHERE pr.id = ?
                LIMIT 1';
        $db = new Database();
        return $db->getOne($sql, [$requestId]);
    }
    
    public static function getArtistRequests($artistId, $limit = 20, $offset = 0) {
        $sql = 'SELECT pr.*, 
                       u.email AS user_email,
                       u.name AS user_name,
                       p.title AS painting_title
                FROM `purchase_requests` pr
                JOIN `paintings` p ON pr.painting_id = p.id
                JOIN `users` u ON pr.user_id = u.id
                WHERE p.artist_id = ?
                ORDER BY pr.created_at DESC
                LIMIT ? OFFSET ?';
        $db = new Database();
        return $db->getAll($sql, [$artistId, $limit, $offset]);
    }
}
?>
