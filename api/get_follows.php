<?php
session_start();
require_once '../config/db_connect.php';

// 设置响应头为JSON
header('Content-Type: application/json; charset=utf-8');

// 检查用户登录状态
if (!isset($_SESSION['user_id'])) {
    echo json_encode([
        'success' => false,
        'error' => '请先登录'
    ]);
    exit;
}

$user_id = $_SESSION['user_id'];

try {
    // 获取当前用户关注的所有用户信息
    $sql = "SELECT u.id, u.username, u.avatar, u.created_at
            FROM follows f
            INNER JOIN users u ON f.followed_id = u.id
            WHERE f.follower_id = ?
            ORDER BY f.created_at DESC";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([$user_id]);
    $followed_users = $stmt->fetchAll();
    
    // 返回成功响应
    echo json_encode([
        'success' => true,
        'data' => $followed_users
    ]);
    
} catch (PDOException $e) {
    echo json_encode([
        'success' => false,
        'error' => '获取关注列表失败：' . $e->getMessage()
    ]);
}
?>