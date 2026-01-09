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
$target_user_id = intval($_POST['target_user_id'] ?? 0);

// 验证target_user_id
if ($target_user_id <= 0) {
    echo json_encode([
        'success' => false,
        'error' => '无效的用户ID'
    ]);
    exit;
}

// 不能关注自己
if ($target_user_id == $_SESSION['user_id']) {
    echo json_encode([
        'success' => false,
        'error' => '不能关注自己'
    ]);
    exit;
}

$follower_id = $_SESSION['user_id'];
$followed_id = $target_user_id;

try {
    // 检查是否已关注
    $sql = "SELECT id FROM follows WHERE follower_id = ? AND followed_id = ?";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([$follower_id, $followed_id]);
    $existing_follow = $stmt->fetch();
    
    if ($existing_follow) {
        // 已关注：删除关注记录
        $sql = "DELETE FROM follows WHERE id = ?";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([$existing_follow['id']]);
        
        $is_following = false;
    } else {
        // 未关注：插入关注记录
        $sql = "INSERT INTO follows (follower_id, followed_id) VALUES (?, ?)";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([$follower_id, $followed_id]);
        
        $is_following = true;
    }
    
    // 返回成功响应
    echo json_encode([
        'success' => true,
        'is_following' => $is_following,
        'message' => $is_following ? '关注成功' : '已取消关注'
    ]);
    
} catch (PDOException $e) {
    echo json_encode([
        'success' => false,
        'error' => '操作失败：' . $e->getMessage()
    ]);
}
?>