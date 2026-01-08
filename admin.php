<?php
session_start();
require_once __DIR__ . '/config/db_connect.php';

// 处理退出登录
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['logout'])) {
    session_destroy();
    header('Location: login.php');
    exit;
}

// 处理删除帖子
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['delete_post_id'])) {
    $post_id = (int)$_POST['delete_post_id'];
    try {
        $sql = "DELETE FROM posts WHERE id = ?";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([$post_id]);
        // 删除成功，刷新页面
        header('Location: admin.php');
        exit;
    } catch (PDOException $e) {
        // 删除失败，可以添加错误处理
    }
}

// 处理删除用户
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['delete_user_id'])) {
    $user_id = (int)$_POST['delete_user_id'];
    try {
        // 先删除该用户的所有帖子
        $sql = "DELETE FROM posts WHERE user_id = ?";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([$user_id]);
        
        // 再删除用户
        $sql = "DELETE FROM users WHERE id = ? AND role != 1";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([$user_id]);
        
        // 删除成功，刷新页面
        header('Location: admin.php');
        exit;
    } catch (PDOException $e) {
        // 删除失败，可以添加错误处理
    }
}

// 检查是否已登录
if (!isset($_SESSION['user_id']) || !isset($_SESSION['role'])) {
    header('Location: login.php');
    exit;
}

// 检查是否为管理员
if ((int)$_SESSION['role'] !== 1) {
    header('Location: index.php');
    exit;
}

$adminUsername = isset($_SESSION['username']) ? htmlspecialchars($_SESSION['username'], ENT_QUOTES, 'UTF-8') : '管理员';

// 处理帖子搜索关键词
$searchKeyword = '';
if ($_SERVER['REQUEST_METHOD'] === 'GET' && isset($_GET['search'])) {
    $searchKeyword = trim($_GET['search']);
}

// 处理用户搜索关键词
$userSearchKeyword = '';
if ($_SERVER['REQUEST_METHOD'] === 'GET' && isset($_GET['user_search'])) {
    $userSearchKeyword = trim($_GET['user_search']);
}

// 查询帖子（带搜索功能）
$posts = [];
try {
    if (!empty($searchKeyword)) {
        // 带搜索条件的查询
        $sql = "SELECT p.*, u.username 
                FROM posts p 
                LEFT JOIN users u ON p.user_id = u.id 
                WHERE p.content LIKE ? OR u.username LIKE ?
                ORDER BY p.created_at DESC";
        $stmt = $pdo->prepare($sql);
        $searchTerm = "%{$searchKeyword}%";
        $stmt->execute([$searchTerm, $searchTerm]);
    } else {
        // 查询所有帖子
        $sql = "SELECT p.*, u.username 
                FROM posts p 
                LEFT JOIN users u ON p.user_id = u.id 
                ORDER BY p.created_at DESC";
        $stmt = $pdo->query($sql);
    }
    $posts = $stmt->fetchAll();
} catch (PDOException $e) {
    // 查询失败，$posts为空数组
    $posts = [];
}

// 查询用户（带搜索功能）
$users = [];
try {
    if (!empty($userSearchKeyword)) {
        // 带搜索条件的查询（按用户名搜索）
        $sql = "SELECT id, username, role, created_at 
                FROM users 
                WHERE username LIKE ?
                ORDER BY id DESC";
        $stmt = $pdo->prepare($sql);
        $searchTerm = "%{$userSearchKeyword}%";
        $stmt->execute([$searchTerm]);
    } else {
        // 查询所有用户
        $sql = "SELECT id, username, role, created_at 
                FROM users 
                ORDER BY id DESC";
        $stmt = $pdo->query($sql);
    }
    $users = $stmt->fetchAll();
} catch (PDOException $e) {
    // 查询失败，$users为空数组
    $users = [];
}
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>XX微博 - 管理后台</title>
    <link rel="stylesheet" href="static/css/admin_style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body>
    <div class="container">
        <div class="header">
            <div class="head-left">
                <h3><i class="fa-solid fa-user-shield"></i> 管理员 <?php echo $adminUsername; ?>，您好！</h3>
            </div>
            <div class="head-right">
                <h3><i class="fa-solid fa-gear"></i> 管理系统</h3>
                <form action="" method="post">
                    <button type="submit" name="logout" value="1" class="admin-btn btn-warning">
                        <i class="fa-solid fa-right-from-bracket"></i> 退出登录
                    </button>
                </form>
            </div>
        </div>

        <div class="admin-nav">
            <ul>
                <li class="active" onclick="switchTab('content')"><a href="javascript:;"><i class="fa-solid fa-file-lines"></i> 内容管理</a></li>
                <li onclick="switchTab('user')"><a href="javascript:;"><i class="fa-solid fa-users"></i> 用户管理</a></li>
            </ul>
        </div>

        <div class="body">
            <div class="main-part">
                <!-- 内容管理部分 -->
                <div id="content-management" class="admin-display">
                    <h4>发帖内容管理</h4>
                    
                    <!-- 搜索区域 -->
                    <div class="search-area">
                        <form action="" method="get" class="search-form">
                            <div class="search-wrapper">
                                <i class="fa-solid fa-magnifying-glass search-icon"></i>
                                <input type="text" class="search-input" name="search" 
                                       placeholder="搜索用户名或内容..."
                                       value="<?php echo htmlspecialchars($searchKeyword); ?>">
                            </div>
                            <button type="submit" class="admin-btn"><i class="fa-solid fa-search"></i> 搜索</button>
                            <?php if (!empty($searchKeyword)): ?>
                                <a href="admin.php" class="admin-btn btn-clear">
                                    <i class="fa-solid fa-xmark"></i> 清除
                                </a>
                            <?php endif; ?>
                        </form>
                    </div>
                    
                    <?php if (!empty($searchKeyword)): ?>
                        <div class="search-result-info">
                            搜索结果（关键词："<?php echo htmlspecialchars($searchKeyword); ?>"）：共找到 <?php echo count($posts); ?> 条记录
                        </div>
                    <?php endif; ?>
                    
                    <?php if (empty($posts)): ?>
                        <div class="data-card" style="text-align: center; padding: 20px;">
                            <p style="color: #718096;">暂无帖子</p>
                        </div>
                    <?php else: ?>
                        <?php foreach ($posts as $post): ?>
                            <!-- 用户发帖内容 -->
                            <div class="data-card">
                                <div class="post-info">
                                    <div>
                                        <strong>用户：</strong><?php echo htmlspecialchars($post['username']); ?> (ID: <?php echo $post['id']; ?>) |
                                        <strong>时间：</strong><?php echo date('Y-m-d H:i', strtotime($post['created_at'])); ?>
                                    </div>
                                </div>
                                <div class="post-content">
                                    <?php echo nl2br(htmlspecialchars($post['content'])); ?>
                                </div>
                                <?php if (!empty($post['image'])): ?>
                                    <div class="post-image">
                                        <img src="<?php echo htmlspecialchars($post['image']); ?>" alt="微博配图">
                                    </div>
                                <?php endif; ?>
                                <div style="margin-top: 10px;">
                                    <form action="" method="post">
                                        <input type="hidden" name="delete_post_id" value="<?php echo $post['id']; ?>">
                                        <button type="submit" class="admin-btn btn-danger">删除</button>
                                    </form>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </div>

                <!-- 用户管理部分 -->
                <div id="user-management" class="admin-display" style="display: none;">
                    <h4>用户管理</h4>
                    
                    <!-- 用户搜索 -->
                    <div class="search-area">
                        <form action="" method="get" class="search-form">
                            <div class="search-wrapper">
                                <i class="fa-solid fa-magnifying-glass search-icon"></i>
                                <input type="text" class="search-input" name="user_search" 
                                       placeholder="搜索用户名..."
                                       value="<?php echo htmlspecialchars($userSearchKeyword); ?>">
                            </div>
                            <button type="submit" class="admin-btn"><i class="fa-solid fa-search"></i> 搜索</button>
                            <?php if (!empty($userSearchKeyword)): ?>
                                <a href="admin.php" class="admin-btn btn-clear">
                                    <i class="fa-solid fa-xmark"></i> 清除
                                </a>
                            <?php endif; ?>
                        </form>
                    </div>
                    
                    <?php if (!empty($userSearchKeyword)): ?>
                        <div class="search-result-info">
                            搜索结果（关键词："<?php echo htmlspecialchars($userSearchKeyword); ?>"）：共找到 <?php echo count($users); ?> 个用户
                        </div>
                    <?php endif; ?>
                    
                    <?php if (empty($users)): ?>
                        <div class="data-card" style="text-align: center; padding: 20px;">
                            <p style="color: #718096;">暂无用户</p>
                        </div>
                    <?php else: ?>
                        <div class="user-list">
                            <?php foreach ($users as $user): ?>
                                <div class="user-card">
                                    <div class="user-name"><?php echo htmlspecialchars($user['username']); ?></div>
                                    <div class="user-id">ID: <?php echo $user['id']; ?></div>
                                    <div class="user-status <?php echo (int)$user['role'] === 1 ? 'status-admin' : 'status-normal'; ?>">
                                        <?php echo (int)$user['role'] === 1 ? '管理员' : '正常用户'; ?>
                                    </div>
                                    <div>
                                        <?php if ((int)$user['role'] !== 1): ?>
                                        <form action="" method="post">
                                            <input type="hidden" name="delete_user_id" value="<?php echo $user['id']; ?>">
                                            <button type="submit" class="admin-btn btn-danger" style="font-size: 11px; padding: 4px 8px;">删除用户</button>
                                        </form>
                                        <?php endif; ?>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                        
                        <h4 style="margin-top: 20px;">用户统计</h4>
                        <div class="user-statistics">
                            <div>总用户数: <?php echo count($users); ?></div>
                            <?php 
                            $adminCount = 0;
                            foreach ($users as $user) {
                                if ((int)$user['role'] === 1) $adminCount++;
                            }
                            ?>
                            <div>管理员: <?php echo $adminCount; ?></div>
                            <div>普通用户: <?php echo count($users) - $adminCount; ?></div>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>

    <div class="footer">
        © 2026 XX微博 版权所有 | 管理系统<br>
            开发者团队：章航渝、章晨阳、周凯涵<br>
            当前版本：v2.1.0
    </div>

    <script>
        // 切换标签页
        function switchTab(tabName) {
            // 获取所有管理区域的元素
            var contentManagement = document.getElementById('content-management');
            var userManagement = document.getElementById('user-management');
            
            // 获取所有导航项
            var navItems = document.querySelectorAll('.admin-nav li');
            
            // 移除所有导航项的active类
            navItems.forEach(function(item) {
                item.classList.remove('active');
            });
            
            // 根据点击的标签显示对应内容
            if (tabName === 'content') {
                contentManagement.style.display = 'block';
                userManagement.style.display = 'none';
                navItems[0].classList.add('active');
            } else if (tabName === 'user') {
                contentManagement.style.display = 'none';
                userManagement.style.display = 'block';
                navItems[1].classList.add('active');
            }
        }
        
        
    </script>
</body>
</html>
