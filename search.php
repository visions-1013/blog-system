<?php
session_start();
require_once 'config/db_connect.php';

// 获取搜索关键词
$search_keyword = isset($_GET['search']) ? trim($_GET['search']) : '';

// 查询搜索结果
$posts = [];
if (!empty($search_keyword)) {
    try {
        // 检查用户是否登录
        $is_logged_in = isset($_SESSION['user_id']);
        $current_user_id = $is_logged_in ? $_SESSION['user_id'] : 0;
        
        if ($is_logged_in) {
            // 已登录：查询点赞状态、关注状态和头像
            $sql = "SELECT p.*, u.username, u.avatar,
                    CASE WHEN l.id IS NOT NULL THEN 1 ELSE 0 END as is_liked,
                    CASE WHEN f.id IS NOT NULL THEN 1 ELSE 0 END as is_followed
                    FROM posts p 
                    LEFT JOIN users u ON p.user_id = u.id 
                    LEFT JOIN likes l ON p.id = l.post_id AND l.user_id = ?
                    LEFT JOIN follows f ON p.user_id = f.followed_id AND f.follower_id = ?
                    WHERE p.content LIKE ? OR u.username LIKE ?
                    ORDER BY p.created_at DESC";
            $stmt = $pdo->prepare($sql);
            $keyword = "%$search_keyword%";
            $stmt->execute([$current_user_id, $current_user_id, $keyword, $keyword]);
        } else {
            // 未登录：不查询点赞状态、关注状态，但查询头像
            $sql = "SELECT p.*, u.username, u.avatar, 0 as is_liked, 0 as is_followed
                    FROM posts p 
                    LEFT JOIN users u ON p.user_id = u.id 
                    WHERE p.content LIKE ? OR u.username LIKE ?
                    ORDER BY p.created_at DESC";
            $stmt = $pdo->prepare($sql);
            $keyword = "%$search_keyword%";
            $stmt->execute([$keyword, $keyword]);
        }
        $posts = $stmt->fetchAll();
    } catch (PDOException $e) {
        // 查询失败，$posts为空数组
        $posts = [];
    }
}
?>
<!DOCTYPE html>
<html>
    <head>
        <title>搜索结果</title>
        <meta charset="utf-8">
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
        <link rel="stylesheet" href="static\css\search_style.css">
        <script src="static/js/ajax_req.js"></script>
        <script src="static/js/main.js"></script>
    </head>
    <body>
        <div class="container">
        <div class="header">
            <div class="head-left">
            <h3>搜索：<?php echo htmlspecialchars($search_keyword); ?> 的结果</h3>
            </div>
            <div class="head-right">
                <a href="index.php" class="index_button">返回主页</a>
            </div>
        </div>


        <div class="body">

            <div class="main-part">
            
            <?php if (empty($search_keyword)): ?>
                <div class="no-posts">
                    <p>请输入搜索关键词</p>
                </div>
            <?php elseif (empty($posts)): ?>
                <div class="no-posts">
                    <p>未找到包含"<?php echo htmlspecialchars($search_keyword); ?>"的相关微博</p>
                </div>
            <?php else: ?>
                <div class="search-info">
                    <p>找到 <?php echo count($posts); ?> 条相关微博</p>
                </div>
                
                <?php foreach ($posts as $post): ?>
                    <div class="blog" data-post-id="<?php echo $post['id']; ?>">
                        <div class="post-user">
                            <img src="static/img/<?php echo htmlspecialchars($post['avatar'] ?? 'default.png'); ?>" 
                                 class="post-user-avatar" 
                                 alt="<?php echo htmlspecialchars($post['username']); ?>的头像"
                                 onclick="window.location.href='user_profile.php?user_id=<?php echo $post['user_id']; ?>'"
                                 style="cursor: pointer;">
                            <div class="username">
                                <?php echo htmlspecialchars($post['username']); ?>
                                <?php if ($post['is_followed']): ?>
                                    <i class="fa-solid fa-star" style="color: #ffd700; margin-left: 5px; font-size: 14px;" title="已关注"></i>
                                <?php endif; ?>
                            </div>
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
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>

            </div>

            <div class="right-sider">
                <div class="sider-section">
                    <h4><i class="fa-solid fa-fire"></i> 热门话题</h4>
                    <a href="#" class="topic-tag">#杭州新闻</a>
                    <a href="#" class="topic-tag">#生活分享</a>
                    <a href="#" class="topic-tag">#心情日记</a>
                    <a href="#" class="topic-tag">#美食探店</a>
                </div>
                
                <div class="sider-section">
                    <h4><i class="fa-solid fa-chart-line"></i> 热门微博</h4>
                    <div class="hot-post-summary">
                        马杜罗的牺牲
                    </div>
                    <div class="hot-post-summary">
                        罗杰的死
                    </div>
                    <div class="hot-post-summary">
                        皇帝的雨伞
                    </div>
                    <div class="hot-post-summary">
                        小猫的重生
                    </div>
                </div>
            </div>
            
        </div>
        <div class="footer">
        <p>© 2026 XX微博 版权所有 | 开发者团队：219</p>
        <p>本页面为自制微博前端演示，后端功能待后续开发</p>
    </div>
        </body>
    </html>
    </html>
