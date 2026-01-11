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
$content = trim($_POST['content'] ?? '');

// 验证数据
if ($post_id <= 0) {
    echo json_encode([
        'success' => false,
        'error' => '无效的微博ID'
    ]);
    exit;
}

if (empty($content)) {
    echo json_encode([
        'success' => false,
        'error' => '评论内容不能为空'
    ]);
    exit;
}

try {
    // 插入评论
    $sql = "INSERT INTO comments (post_id, user_id, content, created_at) VALUES (?, ?, ?, NOW())";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([$post_id, $_SESSION['user_id'], $content]);
    
    // 获取新插入的评论ID
    $comment_id = $pdo->lastInsertId();
    
    // 检查当前用户是否为管理员
    $is_admin = isset($_SESSION['role']) && (int)$_SESSION['role'] === 1;
    $current_user_id = $_SESSION['user_id'];
    
    // 查询新评论的完整信息（包含用户名）
    $sql = "SELECT c.*, u.username 
            FROM comments c 
            LEFT JOIN users u ON c.user_id = u.id 
            WHERE c.id = ?";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([$comment_id]);
    $comment = $stmt->fetch();
    
    // 格式化时间
    $comment['created_at'] = date('Y-m-d H:i:s', strtotime($comment['created_at']));
    
    // 判断当前用户是否可以删除此评论（管理员或评论作者）
    $comment['can_delete'] = $is_admin || $comment['user_id'] == $current_user_id;
    
    // 返回成功响应
    echo json_encode([
        'success' => true,
        'comment' => $comment
    ]);
    
} catch (PDOException $e) {
    echo json_encode([
        'success' => false,
        'error' => '评论失败：' . $e->getMessage()
    ]);
}
?>
