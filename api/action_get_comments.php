<?php
require_once '../config/db_connect.php';

// 设置响应头为JSON
header('Content-Type: application/json; charset=utf-8');

// 检查请求方法
if ($_SERVER['REQUEST_METHOD'] !== 'GET') {
    echo json_encode([
        'success' => false,
        'error' => '请求方法错误'
    ]);
    exit;
}

// 获取GET参数
$post_id = intval($_GET['post_id'] ?? 0);

// 验证post_id
if ($post_id <= 0) {
    echo json_encode([
        'success' => false,
        'error' => '无效的微博ID'
    ]);
    exit;
}

try {
    // 检查当前用户是否为管理员
    session_start();
    $is_admin = isset($_SESSION['role']) && (int)$_SESSION['role'] === 1;
    $current_user_id = isset($_SESSION['user_id']) ? $_SESSION['user_id'] : 0;
    
    // 查询该微博的所有评论（按时间倒序）
    $sql = "SELECT c.id, c.content, c.created_at, c.user_id, u.username 
            FROM comments c 
            LEFT JOIN users u ON c.user_id = u.id 
            WHERE c.post_id = ? 
            ORDER BY c.created_at ASC";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([$post_id]);
    $comments = $stmt->fetchAll();
    
    // 格式化时间并判断权限
    foreach ($comments as &$comment) {
        $comment['created_at'] = date('Y-m-d H:i:s', strtotime($comment['created_at']));
        // 判断当前用户是否可以删除此评论（管理员或评论作者）
        $comment['can_delete'] = $is_admin || $comment['user_id'] == $current_user_id;
    }
    
    // 返回成功响应
    echo json_encode([
        'success' => true,
        'comments' => $comments
    ]);
    
} catch (PDOException $e) {
    echo json_encode([
        'success' => false,
        'error' => '获取评论失败：' . $e->getMessage()
    ]);
}
?>
