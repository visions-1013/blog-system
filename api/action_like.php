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

// 检查请求方法
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode([
        'success' => false,
        'error' => '请求方法错误'
    ]);
    exit;
}

// 获取POST数据
$post_id = intval($_POST['post_id'] ?? 0);

// 验证post_id
if ($post_id <= 0) {
    echo json_encode([
        'success' => false,
        'error' => '无效的微博ID'
    ]);
    exit;
}

$user_id = $_SESSION['user_id'];

try {
    // 开启事务
    $pdo->beginTransaction();
    
    // 检查是否已点赞
    $sql = "SELECT id FROM likes WHERE user_id = ? AND post_id = ?";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([$user_id, $post_id]);
    $existing_like = $stmt->fetch();
    
    if ($existing_like) {
        // 已点赞：删除点赞记录，点赞数-1
        $sql = "DELETE FROM likes WHERE id = ?";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([$existing_like['id']]);
        
        $sql = "UPDATE posts SET likes_count = likes_count - 1 WHERE id = ?";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([$post_id]);
        
        $liked = false;
    } else {
        // 未点赞：插入点赞记录，点赞数+1
        $sql = "INSERT INTO likes (user_id, post_id) VALUES (?, ?)";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([$user_id, $post_id]);
        
        $sql = "UPDATE posts SET likes_count = likes_count + 1 WHERE id = ?";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([$post_id]);
        
        $liked = true;
    }
    
    // 提交事务
    $pdo->commit();
    
    // 获取最新的点赞数
    $sql = "SELECT likes_count FROM posts WHERE id = ?";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([$post_id]);
    $result = $stmt->fetch();
    
    // 返回成功响应
    echo json_encode([
        'success' => true,
        'liked' => $liked,
        'likes_count' => $result['likes_count']
    ]);
    
} catch (PDOException $e) {
    // 回滚事务
    if ($pdo->inTransaction()) {
        $pdo->rollBack();
    }
    
    echo json_encode([
        'success' => false,
        'error' => '操作失败：' . $e->getMessage()
    ]);
}
?>
