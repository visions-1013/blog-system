<?php
session_start();
require_once __DIR__ . '/config/db_connect.php';

// 检查是否已登录，未登录则跳转到登录页
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}

// 处理退出登录请求
if (isset($_GET['logout']) && $_GET['logout'] == '1') {
    session_destroy();
    header('Location: login.php');
    exit;
}

// 处理删除帖子请求
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['delete_post_id'])) {
    $post_id = (int)$_POST['delete_post_id'];
    try {
        $sql = "DELETE FROM posts WHERE id = ? AND user_id = ?";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([$post_id, $currentUserId]);
        // 删除成功，刷新页面
        header('Location: profile.php');
        exit;
    } catch (PDOException $e) {
        // 删除失败，可以添加错误处理
    }
}

// 获取当前登录用户信息
$currentUserId = $_SESSION['user_id'];
$username = $_SESSION['username'];

// 从数据库查询用户的往期发布
try {
    $sql = "SELECT id, content, image, created_at 
            FROM posts 
            WHERE user_id = ? 
            ORDER BY created_at DESC 
            LIMIT 20";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([$currentUserId]);
    $posts = $stmt->fetchAll();
} catch (PDOException $e) {
    $posts = [];
    $error = '获取发布内容失败';
}
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>个人中心 - <?php echo htmlspecialchars($username); ?></title>
    <link rel="stylesheet" href="static\css\profile_style.css">
</head>
<body>
    <!-- 主容器 -->
    <div class="container">
        <div class="header">
            <div class="head-left">
                <h3>亲爱的<?php echo htmlspecialchars($username); ?>，欢迎回来!</h3>
            </div>
            <div class="head-right">
				<h3>XX微博-个人中心</h3>
                <br/><br/><br/><a href="?logout=1" style="color: #666; text-decoration: none;">退出登录</a>
            </div>
        </div>

        <!-- 导航栏区域 -->
        <div class="nav">
            <ul>
                <li class="active"><a href="#">首页</a></li>
                <li><a href="#">发现</a></li>
                <li><a href="#">消息</a></li>
                <li><a href="#">收藏</a></li>
                <li><a href="#">设置</a></li>
            </ul>
        </div>

        <div class="body">
            <div class="main-part">
                <div class="blog-display">
                    <p>您的往期发布</p>
                    <?php if (isset($error)): ?>
                        <div class="error-message" style="color: darkred;">
                            <?php echo htmlspecialchars($error); ?>
                        </div>
                    <?php elseif (empty($posts)): ?>
                        <div class="no-posts" style="text-align: center; color: #999; padding: 20px;">
                            您还没有发布任何微博，快去分享您的第一条动态吧！
                        </div>
                    <?php else: ?>
                        <?php foreach ($posts as $post): ?>
                        <div class="blog">
                            <div class="data"><?php echo htmlspecialchars($post['created_at']); ?></div>
                            <div class="blog-content"><?php echo htmlspecialchars($post['content']); ?></div>
                            <?php if (!empty($post['image'])): ?>
                            <div class="blog-picture">
                                <img src="<?php echo htmlspecialchars($post['image']); ?>" alt="微博配图">
                            </div>
                            <?php endif; ?>
                            <div style="margin-top: 10px;">
                                <form action="" method="post" style="display: inline;">
                                    <input type="hidden" name="delete_post_id" value="<?php echo $post['id']; ?>">
                                    <button type="submit" class="admin-btn btn-danger" onclick="return confirm('确定要删除这条微博吗？');">删除</button>
                                </form>
                            </div>
                        </div>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </div>
            </div>

            <!-- 右侧边栏 -->
            <div class="right-sider">
                <h4>你的关注</h4>
				<div class="following">
				<div class="username">东北御姐</div>
				<div class="username">宇 少将</div>
				<div class="username">安徽秀才</div>
				<div class="username">杭州小航</div>
				<div class="username">重庆小渝</div>
				</div>
				
				<h4>常看</h4>
				<div class="Frequently">
					<div class="username">白月光</div>
					<div class="username">小和山本地生活</div>
					<div class="username">记录我的美国生活（润人）</div>
					</div>
				<h4>推荐关注</h4>
				<div class="recommend">
					<div class="username">科技前沿</div>
					<div class="username">美食探店</div>
				</div>
            </div>
        </div>
    </div>

    <div class="footer">
        © 2026 XX微博 版权所有 | 隐私政策 | 联系我们<br>
            开发者团队：章航渝、章晨阳、周楷涵<br>
            本页面为自制微博前端演示，后端功能待后续开发
    </div>

</body>
</html>
