<?php
session_start();
require_once 'config/db_connect.php';

// 获取要查看的用户ID
$user_id = isset($_GET['user_id']) ? (int)$_GET['user_id'] : 0;

if ($user_id <= 0) {
    header('Location: index.php');
    exit;
}

// 获取用户信息
try {
    $sql = "SELECT id, username, avatar, created_at FROM users WHERE id = ?";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([$user_id]);
    $profile_user = $stmt->fetch();
    
    if (!$profile_user) {
        echo "<script>alert('用户不存在'); window.location.href='index.php';</script>";
        exit;
    }
} catch (PDOException $e) {
    echo "<script>alert('获取用户信息失败'); window.location.href='index.php';</script>";
    exit;
}

// 检查当前登录用户
$is_logged_in = isset($_SESSION['user_id']);
$is_own_profile = $is_logged_in && $_SESSION['user_id'] == $user_id;

// 检查是否已关注
$is_following = false;
if ($is_logged_in && !$is_own_profile) {
    try {
        $sql = "SELECT id FROM follows WHERE follower_id = ? AND followed_id = ?";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([$_SESSION['user_id'], $user_id]);
        $is_following = $stmt->fetch() !== false;
    } catch (PDOException $e) {
        // 忽略错误
    }
}

// 获取用户的微博列表（包含当前用户的点赞状态）
$posts = [];
try {
    if ($is_logged_in) {
        $sql = "SELECT p.*, u.username, u.avatar,
                CASE WHEN l.id IS NOT NULL THEN 1 ELSE 0 END as is_liked
                FROM posts p 
                LEFT JOIN users u ON p.user_id = u.id 
                LEFT JOIN likes l ON p.id = l.post_id AND l.user_id = ?
                WHERE p.user_id = ?
                ORDER BY p.created_at DESC";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([$_SESSION['user_id'], $user_id]);
    } else {
        $sql = "SELECT p.*, u.username, u.avatar, 0 as is_liked
                FROM posts p 
                LEFT JOIN users u ON p.user_id = u.id 
                WHERE p.user_id = ?
                ORDER BY p.created_at DESC";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([$user_id]);
    }
    $posts = $stmt->fetchAll();
} catch (PDOException $e) {
    $posts = [];
}
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>用户资料 - XX微博</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="static/css/index_style.css">
    <script src="static/js/ajax_req.js"></script>
    <script src="static/js/main.js"></script>
    <style>
        .user-profile-header {
            background: linear-gradient(135deg, #1677ff 0%, #0d6efd 100%);
            color: white;
            padding: 40px 20px;
            text-align: center;
            margin-bottom: 30px;
        }
        
        .profile-avatar {
            width: 120px;
            height: 120px;
            border-radius: 50%;
            border: 4px solid white;
            object-fit: cover;
            margin-bottom: 15px;
        }
        
        .profile-username {
            font-size: 28px;
            font-weight: bold;
            margin-bottom: 10px;
        }
        
        .profile-info {
            color: rgba(255, 255, 255, 0.9);
            font-size: 14px;
        }
        
        .follow-button {
            margin-top: 20px;
            padding: 10px 30px;
            font-size: 16px;
            border: none;
            border-radius: 25px;
            cursor: pointer;
            transition: all 0.3s ease;
        }
        
        .follow-button.not-following {
            background: #ff6b6b;
            color: white;
        }
        
        .follow-button.not-following:hover {
            background: #ee5a5a;
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(255, 107, 107, 0.4);
        }
        
        .follow-button.following {
            background: #f0f0f0;
            color: #666;
        }
        
        .follow-button.following:hover {
            background: #e0e0e0;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <div class="head-left">
                <h3>欢迎您访问XX微博!</h3>
                <br/>
                <a href="index.php" style="color: white; text-decoration: none;">
                    <i class="fa-solid fa-arrow-left"></i> 返回主页
                </a>
            </div>
            <div class="head-right">
                <?php if ($is_logged_in): ?>
                    <div class="user-info">
                        <span class="username-display"><?php echo htmlspecialchars($_SESSION['username']); ?></span>
                    </div>
                    
                    <div class="user-avatar-container">
                        <img src="static/img/<?php echo htmlspecialchars($_SESSION['avatar'] ?? 'default.png'); ?>" 
                             alt="用户头像" 
                             class="user-avatar" 
                             onclick="window.location.href='profile.php'"/>
                    </div>
                    <nav class="user-nav">
                        <a href="profile.php">个人中心</a>
                        <a href="?action=logout">返回主页</a>
                    </nav>
                <?php else: ?>
                    <nav class="auth-nav">
                        <a href="login.php">登录</a>
                        <span>|</span>
                        <a href="register.php">注册</a>
                    </nav>
                <?php endif; ?>
            </div>
        </div>
        
        <div class="nav">
            今日新闻&nbsp;&nbsp;&nbsp;&nbsp;明星趣闻&nbsp;&nbsp;&nbsp;&nbsp;学习妙招
        </div>
        
        <div class="body">
            <div class="main-part">
                <div class="user-profile-header">
                    <img src="static/img/<?php echo htmlspecialchars($profile_user['avatar']); ?>" 
                         alt="<?php echo htmlspecialchars($profile_user['username']); ?>的头像"
                         class="profile-avatar">
                    <div class="profile-username"><?php echo htmlspecialchars($profile_user['username']); ?></div>
                    <div class="profile-info">
                        注册时间：<?php echo date('Y-m-d', strtotime($profile_user['created_at'])); ?>
                    </div>
                    
                    <?php if ($is_logged_in && !$is_own_profile): ?>
                        <button class="follow-button <?php echo $is_following ? 'following' : 'not-following'; ?>" 
                                data-user-id="<?php echo $user_id; ?>"
                                data-following="<?php echo $is_following ? 'true' : 'false'; ?>"
                                onclick="toggleFollow(this)">
                            <?php echo $is_following ? '<i class="fa-solid fa-check"></i> 已关注' : '<i class="fa-solid fa-plus"></i> 关注'; ?>
                        </button>
                    <?php endif; ?>
                </div>
                
                <div class="blog-display">
                    <?php if (empty($posts)): ?>
                        <div class="no-posts">
                            <p>该用户暂无微博</p>
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
                                </div>
                                <div class="comment-input-area" style="display: none;">
                                    <textarea placeholder="写下你的评论..."></textarea>
                                    <button class="comment-submit-btn" onclick="submitComment(this)">发表评论</button>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </div>
            </div>
            
            <div class="right-sider">
                <div class="sider-section">
                    <h4><i class="fa-solid fa-fire"></i> 热门话题</h4>
                    <a href="#" class="topic-tag">#生活分享</a>
                    <a href="#" class="topic-tag">#心情日记</a>
                    <a href="#" class="topic-tag">#美食探店</a>
                    <a href="#" class="topic-tag">#旅行记录</a>
                    <a href="#" class="topic-tag">#学习笔记</a>
                </div>
            </div>
        </div>
    </div>
    
    <div class="footer">
        © 2026 XX微博 版权所有 | 隐私政策 | 联系我们<br/>
        开发者团队：周楷涵、章晨阳、章航渝
    </div>
</body>
</html>
