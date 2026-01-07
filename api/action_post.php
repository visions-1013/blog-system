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

// 获取微博内容
$content = trim($_POST['content'] ?? '');

// 验证内容
if (empty($content)) {
    echo json_encode([
        'success' => false,
        'error' => '微博内容不能为空'
    ]);
    exit;
}

// 处理图片上传
$image_path = null;
if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
    $upload_dir = '../static/img/';
    
    // 检查目录是否存在，不存在则创建
    if (!file_exists($upload_dir)) {
        mkdir($upload_dir, 0777, true);
    }
    
    $file = $_FILES['image'];
    $file_ext = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
    
    // 验证文件类型
    $allowed_types = ['jpg', 'jpeg', 'png', 'gif'];
    if (!in_array($file_ext, $allowed_types)) {
        echo json_encode([
            'success' => false,
            'error' => '只允许上传 JPG、JPEG、PNG、GIF 格式的图片'
        ]);
        exit;
    }
    
    // 验证文件大小（限制为5MB）
    if ($file['size'] > 5 * 1024 * 1024) {
        echo json_encode([
            'success' => false,
            'error' => '图片大小不能超过5MB'
        ]);
        exit;
    }
    
    // 生成唯一文件名
    $new_filename = uniqid() . '_' . time() . '.' . $file_ext;
    $upload_path = $upload_dir . $new_filename;
    
    // 移动上传文件
    if (move_uploaded_file($file['tmp_name'], $upload_path)) {
        $image_path = 'static/img/' . $new_filename;
    } else {
        echo json_encode([
            'success' => false,
            'error' => '图片上传失败，请重试'
        ]);
        exit;
    }
}

try {
    // 插入数据库
    $sql = "INSERT INTO posts (user_id, content, image, created_at) VALUES (?, ?, ?, NOW())";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([$_SESSION['user_id'], $content, $image_path]);
    
    // 获取新插入的微博ID
    $post_id = $pdo->lastInsertId();
    
    // 查询新微博的完整信息（包含用户名）
    $sql = "SELECT p.*, u.username, u.avatar 
            FROM posts p 
            LEFT JOIN users u ON p.user_id = u.id 
            WHERE p.id = ?";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([$post_id]);
    $post = $stmt->fetch();
    
    // 格式化时间
    $post['created_at'] = date('Y-m-d H:i:s', strtotime($post['created_at']));
    
    // 返回成功响应
    echo json_encode([
        'success' => true,
        'post' => $post
    ]);
    
} catch (PDOException $e) {
    echo json_encode([
        'success' => false,
        'error' => '发布失败：' . $e->getMessage()
    ]);
}
?>
