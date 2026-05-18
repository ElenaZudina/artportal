<?php
/**
 * Purchase Request Model - handles database operations for painting purchase requests
 * Manages purchase request records and status
 */
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
                       u.username AS user_name,
                       p.title AS painting_title,
                       p.artist_id,
                       a.name AS artist_name,
                       au.email AS artist_email
                FROM `purchase_requests` pr
                JOIN `users` u ON pr.user_id = u.id
                JOIN `paintings` p ON pr.painting_id = p.id
                JOIN `artists` a ON p.artist_id = a.id
                JOIN `users` au ON a.user_id = au.id
                WHERE pr.id = ?
                LIMIT 1';
        $db = new Database();
        return $db->getOne($sql, [$requestId]);
    }
    
    public static function getArtistRequests($artistId, $limit = 20, $offset = 0) {
        $limit = (int)$limit;
        $offset = (int)$offset;
        $sql = 'SELECT pr.*, 
                       u.email AS user_email,
                       u.username AS user_name,
                       p.title AS painting_title
                FROM `purchase_requests` pr
                JOIN `paintings` p ON pr.painting_id = p.id
                JOIN `users` u ON pr.user_id = u.id
                WHERE p.artist_id = ?
                ORDER BY pr.created_at DESC
                LIMIT ' . $limit . ' OFFSET ' . $offset;
        $db = new Database();
        return $db->getAll($sql, [$artistId]);
    }

    public static function getUserRequests($userId, $limit = 10, $offset = 0) {
        $limit = (int)$limit;
        $offset = (int)$offset;
        $sql = 'SELECT pr.*, 
                       p.title AS painting_title,
                       p.image AS image,
                       a.name AS artist_name
                FROM `purchase_requests` pr
                JOIN `paintings` p ON pr.painting_id = p.id
                JOIN `artists` a ON p.artist_id = a.id
                WHERE pr.user_id = ?
                ORDER BY pr.created_at DESC
                LIMIT ' . $limit . ' OFFSET ' . $offset;
        $db = new Database();
        return $db->getAll($sql, [$userId]);
    }

    public static function getUserRequestsCount($userId) {
        $db = new Database();
        $sql = 'SELECT COUNT(*) AS cnt FROM `purchase_requests` WHERE user_id = ?';
        $res = $db->getOne($sql, [$userId]);
        return (int)($res['cnt'] ?? 0);
    }
}
?>
