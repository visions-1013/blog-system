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

// 查询所有帖子
$posts = [];
try {
    $sql = "SELECT p.*, u.username 
            FROM posts p 
            LEFT JOIN users u ON p.user_id = u.id 
            ORDER BY p.created_at DESC";
    $stmt = $pdo->query($sql);
    $posts = $stmt->fetchAll();
} catch (PDOException $e) {
    // 查询失败，$posts为空数组
    $posts = [];
}
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>XX微博 - 管理后台</title>
    <link rel="stylesheet" href="static\css\admin_style.css">
</head>
<body>
    <div class="container">
        <div class="header">
            <div class="head-left">
                <h3>管理员 <?php echo $adminUsername; ?>，您好！</h3>
            </div>
            <div class="head-right">
                <h3>管理系统</h3>
                <br/><br/>
                <form action="" method="post">
                    <button type="submit" name="logout" value="1" class="admin-btn btn-warning">退出登录</button>
                </form>
            </div>
        </div>

        <div class="admin-nav">
            <ul>
                <li class="active"><a href="#">内容管理</a></li>
                <li><a href="#">用户管理</a></li>
            </ul>
        </div>

        <div class="body">
            <div class="main-part">
                <div class="admin-display">
                    <h4>发帖内容管理</h4>
                    
                    <!-- 搜索区域 -->
                    <div class="search-area">
                        <input type="text" class="search-input" id="searchInput" placeholder="搜索用户名或内容...">
                        <button class="admin-btn" onclick="search()">搜索</button>
                    </div>
                    
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
            </div>

            <!-- 右侧边栏 - 用户管理 -->
            <div class="right-sider">
                <h4>用户管理</h4>
                
                <!-- 用户搜索 -->
                <div class="search-area">
                    <form action="" method="post">
                    <input type="text" class="search-input" id="userSearch" placeholder="搜索用户名...">
                    <button class="admin-btn" onclick="search()">搜索</button>
                    </form>
                </div>
                
                <div class="user-list">
                    <!-- 用户卡片1 -->
                    <div class="user-card">
                        <div class="user-name">章航渝(神人)</div>
                        <div class="user-id">ID: U10023</div>
                        <div class="user-status status-normal">正常</div>
                        <div>
                            <button class="admin-btn btn-danger" style="font-size: 11px; padding: 4px 8px;" onclick="banUser('U10023')">注销账号</button>
                        </div>
                    </div>
                    
                    <!-- 用户卡片2 -->
                    <div class="user-card">
                        <div class="user-name">三生有幸</div>
                        <div class="user-id">ID: U10022</div>
                        <div class="user-status status-normal">正常</div>
                        <div>
                            <button class="admin-btn btn-danger" style="font-size: 11px; padding: 4px 8px;" onclick="banUser('U10022')">注销账号</button>
                        </div>
                    </div>
                    
                    <!-- 用户卡片3 -->
                    <div class="user-card">
                        <div class="user-name">周凯涵</div>
                        <div class="user-id">ID: U10021</div>
                        <div class="user-status status-banned">已注销</div>
                        <div>
                        </div>
                    </div>
                    
                    <!-- 用户卡片4 -->
                    <div class="user-card">
                        <div class="user-name">楠楠</div>
                        <div class="user-id">ID: U10020</div>
                        <div class="user-status status-normal">正常</div>
                        <div>
                            <button class="admin-btn btn-danger" style="font-size: 11px; padding: 4px 8px;" onclick="banUser('U10020')">注销账号</button>
                        </div>
                    </div>
                    
                    <!-- 用户卡片5 -->
                    <div class="user-card">
                        <div class="user-name">杭州小航</div>
                        <div class="user-id">ID: U10019</div>
                        <div class="user-status status-normal">正常</div>
                        <div>
                            <button class="admin-btn btn-danger" style="font-size: 11px; padding: 4px 8px;" onclick="banUser('U10019')">注销账号</button>
                        </div>
                    </div>
                    
                    <!-- 用户卡片6 -->
                    <div class="user-card">
                        <div class="user-name">重庆小渝</div>
                        <div class="user-id">ID: U10018</div>
                        <div class="user-status status-normal">正常</div>
                        <div>
                            <button class="admin-btn btn-danger" style="font-size: 11px; padding: 4px 8px;" onclick="banUser('U10018')">注销账号</button>
                        </div>
                    </div>
                    
                    <!-- 用户卡片7 -->
                    <div class="user-card">
                        <div class="user-name">白月光</div>
                        <div class="user-id">ID: U10017</div>
                        <div class="user-status status-normal">正常</div>
                        <div>
                            <button class="admin-btn btn-danger" style="font-size: 11px; padding: 4px 8px;" onclick="banUser('U10017')">注销账号</button>
                        </div>
                    </div>
                    
                    <!-- 用户卡片8 -->
                    <div class="user-card">
                        <div class="user-name">小和山生活</div>
                        <div class="user-id">ID: U10016</div>
                        <div class="user-status status-normal">正常</div>
                        <div>
                            <button class="admin-btn btn-danger" style="font-size: 11px; padding: 4px 8px;" onclick="banUser('U10016')">注销账号</button>
                        </div>
                    </div>
                    
                    <!-- 用户卡片9 -->
                    <div class="user-card">
                        <div class="user-name">科技前沿</div>
                        <div class="user-id">ID: U10015</div>
                        <div class="user-status status-normal">正常</div>
                        <div>
                            <button class="admin-btn btn-danger" style="font-size: 11px; padding: 4px 8px;" onclick="banUser('U10015')">注销账号</button>
                        </div>
                    </div>
                    
                    <!-- 用户卡片10 -->
                    <div class="user-card">
                        <div class="user-name">美食探店</div>
                        <div class="user-id">ID: U10014</div>
                        <div class="user-status status-normal">正常</div>
                        <div>
                            <button class="admin-btn btn-danger" style="font-size: 11px; padding: 4px 8px;" onclick="banUser('U10014')">注销账号</button>
                        </div>
                    </div>
                </div>
                
                <h4>用户统计</h4>
                <div style="font-size: 12px; color: #718096;">
                    <div>总用户数: 1256</div>
                    <div>正常用户: 1240</div>
                    <div>已注销: 16</div>
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
        
    </script>
</body>
</html>
