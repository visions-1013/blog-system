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
$comment_id = intval($_POST['comment_id'] ?? 0);

// 验证数据
if ($comment_id <= 0) {
    echo json_encode([
        'success' => false,
        'error' => '无效的评论ID'
    ]);
    exit;
}

try {
    // 检查当前用户是否为管理员
    $is_admin = isset($_SESSION['role']) && (int)$_SESSION['role'] === 1;
    $current_user_id = $_SESSION['user_id'];
    
    // 查询评论信息
    $sql = "SELECT user_id, post_id FROM comments WHERE id = ?";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([$comment_id]);
    $comment = $stmt->fetch();
    
    if (!$comment) {
        echo json_encode([
            'success' => false,
            'error' => '评论不存在'
        ]);
        exit;
    }
    
    // 权限检查：管理员可以删除所有评论，普通用户只能删除自己的评论
    if (!$is_admin && $comment['user_id'] != $current_user_id) {
        echo json_encode([
            'success' => false,
            'error' => '您没有权限删除此评论'
        ]);
        exit;
    }
    
    // 删除评论
    $sql = "DELETE FROM comments WHERE id = ?";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([$comment_id]);
    
    // 返回成功响应
    echo json_encode([
        'success' => true,
        'message' => '删除成功'
    ]);
    
} catch (PDOException $e) {
    echo json_encode([
        'success' => false,
        'error' => '删除失败：' . $e->getMessage()
    ]);
}
?>
