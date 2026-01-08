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

// 获取当前登录用户信息
$currentUserId = $_SESSION['user_id'];
$username = $_SESSION['username'];

// 获取用户头像信息
try {
    $sql = "SELECT avatar FROM users WHERE id = ?";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([$currentUserId]);
    $user = $stmt->fetch();
    $avatar = $user['avatar'] ?? 'default.png';
} catch (PDOException $e) {
    $avatar = 'default.png';
}

// 处理头像上传请求
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_FILES['avatar']) && $_FILES['avatar']['error'] === UPLOAD_ERR_OK) {
    $file = $_FILES['avatar'];
    $upload_dir = 'static/img/';
    
    // 检查目录是否存在，不存在则创建
    if (!file_exists($upload_dir)) {
        mkdir($upload_dir, 0777, true);
    }
    
    $file_ext = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
    
    // 验证文件类型
    $allowed_types = ['jpg', 'jpeg', 'png', 'gif'];
    if (!in_array($file_ext, $allowed_types)) {
        echo "<script>alert('只允许上传 JPG、JPEG、PNG、GIF 格式的图片！'); history.back();</script>";
        exit;
    }
    
    // 验证文件大小（限制为2MB）
    if ($file['size'] > 2 * 1024 * 1024) {
        echo "<script>alert('图片大小不能超过2MB！'); history.back();</script>";
        exit;
    }
    
    // 生成唯一文件名
    $new_filename = 'avatar_' . $currentUserId . '_' . time() . '.' . $file_ext;
    $upload_path = $upload_dir . $new_filename;
    
    // 移动上传文件
    if (move_uploaded_file($file['tmp_name'], $upload_path)) {
        try {
            // 更新数据库
            $sql = "UPDATE users SET avatar = ? WHERE id = ?";
            $stmt = $pdo->prepare($sql);
            $stmt->execute([$new_filename, $currentUserId]);
            
            // 更新session
            $_SESSION['avatar'] = $new_filename;
            
            // 上传成功，刷新页面
            header('Location: profile.php');
            exit;
        } catch (PDOException $e) {
            echo "<script>alert('更新头像失败：" . $e->getMessage() . "'); history.back();</script>";
            exit;
        }
    } else {
        echo "<script>alert('图片上传失败，请重试！'); history.back();</script>";
        exit;
    }
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

// 从数据库查询用户的往期发布（包含点赞状态和头像）
try {
    $sql = "SELECT p.*, u.username, u.avatar,
            CASE WHEN l.id IS NOT NULL THEN 1 ELSE 0 END as is_liked
            FROM posts p 
            LEFT JOIN users u ON p.user_id = u.id 
            LEFT JOIN likes l ON p.id = l.post_id AND l.user_id = ?
            WHERE p.user_id = ? 
            ORDER BY p.created_at DESC 
            LIMIT 20";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([$currentUserId, $currentUserId]);
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
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="static\css\profile_style.css">
    <script src="static/js/ajax_req.js"></script>
    <script src="static/js/main.js"></script>
    <style>
        /* 用户头像显示样式 */
        .user-avatar-container {
            display: inline-block;
            vertical-align: middle;
            margin-left: 20px;
            cursor: pointer;
        }
        
        .user-avatar {
            width: 60px;
            height: 60px;
            border-radius: 50%;
            object-fit: cover;
            border: 2px solid #e8f4f8;
            vertical-align: middle;
            transition: transform 0.2s ease;
        }
        
        .user-avatar:hover {
            transform: scale(1.05);
            box-shadow: 0 0 0 8px rgba(66, 217, 232, 0.3);
        }
    </style>
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
                <div class="user-avatar-container">
                    <form action="profile.php" method="post" enctype="multipart/form-data">
                        <input type="file" name="avatar" id="avatarUpload" 
                               accept="image/*" style="display:none;" onchange="this.form.submit()"/>
                        <img src="static/img/<?php echo htmlspecialchars($avatar); ?>" 
                             alt="用户头像" 
                             class="user-avatar" 
                             id="currentAvatar"
                             onclick="document.getElementById('avatarUpload').click()"/>
                    </form>
                </div>
                <br/><br/>
                <a href="?logout=1" style="color: #666; text-decoration: none;">退出登录</a>
                <a href="index.php" style="color: #666; text-decoration: none; margin-left: 20px;">返回主页</a>
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
                        <div class="no-posts">
                            <p>暂无微博，快来发布第一条吧！</p>
                        </div>
                    <?php else: ?>
                        <?php foreach ($posts as $post): ?>
                            <div class="blog" data-post-id="<?php echo $post['id']; ?>">
                                <div class="post-user">
                                    <img src="static/img/<?php echo htmlspecialchars($post['avatar'] ?? 'default.png'); ?>" 
                                         class="post-user-avatar" 
                                         alt="<?php echo htmlspecialchars($post['username']); ?>的头像">
                                    <div class="username"><?php echo htmlspecialchars($post['username']); ?></div>
                                </div>
                                <div class="blog-content"><?php echo nl2br(htmlspecialchars($post['content'])); ?></div>
                                <?php if (!empty($post['image'])): ?>
                                <div class="blog-picture">
                                    <img src="<?php echo htmlspecialchars($post['image']); ?>" alt="微博配图">
                                </div>
                                <?php endif; ?>
                                <div class="post-time">
                                    <small><?php echo date('Y-m-d H:i:s', strtotime($post['created_at'])); ?></small>
                                </div>
                                <button class="like-button" 
                                        data-liked="<?php echo $post['is_liked'] ? 'true' : 'false'; ?>"
                                        onclick="toggleLike(this)">
                                    <i class="fa-solid fa-thumbs-up"></i>
                                    <span><?php echo $post['is_liked'] ? '已赞' : '点赞'; ?></span>
                                    <span class="like-count"><?php echo $post['likes_count']; ?></span>
                                </button>
                                <button class="comment-button" onclick="toggleComment(this)">
                                    <i class="fa-regular fa-comment"></i>
                                    <span>评论</span>
                                </button>
                                <div class="comment-section" style="display: none;">
                                    <!-- 评论将在这里动态加载 -->
                                </div>
                                <div class="comment-input-area" style="display: none;">
                                    <textarea placeholder="写下你的评论..."></textarea>
                                    <button class="comment-submit-btn" onclick="submitComment(this)">发表评论</button>
                                </div>
                                <div style="margin-top: 10px;">
                                    <form action="" method="post" style="display: inline;">
                                        <input type="hidden" name="delete_post_id" value="<?php echo $post['id']; ?>">
                                        <button type="submit" class="delete-button" onclick="return confirm('确定要删除这条微博吗？');">
                                            <i class="fa-solid fa-trash"></i>
                                            删除
                                        </button>
                                    </form>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </div>
            </div>

            <!-- 右侧边栏 -->
            <div class="right-sider">
                <div class="sider-section">
                    <h4><i class="fa-solid fa-users"></i> 你的关注</h4>
                    <div class="user-item">
                        <img src="static/img/default.png" alt="">
                        <span>东北御姐</span>
                    </div>
                    <div class="user-item">
                        <img src="static/img/default.png" alt="">
                        <span>宇少将</span>
                    </div>
                    <div class="user-item">
                        <img src="static/img/default.png" alt="">
                        <span>杭州小航</span>
                    </div>
                </div>
                
                <div class="sider-section">
                    <h4><i class="fa-solid fa-fire"></i> 推荐关注</h4>
                    <div class="user-item">
                        <img src="static/img/default.png" alt="">
                        <span>科技前沿</span>
                    </div>
                    <div class="user-item">
                        <img src="static/img/default.png" alt="">
                        <span>美食探店</span>
                    </div>
                    <div class="user-item">
                        <img src="static/img/default.png" alt="">
                        <span>旅行日记</span>
                    </div>
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
            开发者团队：章航渝、章晨阳、周楷涵<br>
            开发者团队：章航渝、章晨阳、周楷涵<br>
                    </div>
