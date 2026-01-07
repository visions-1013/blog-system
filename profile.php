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

// 从数据库查询用户的往期发布
try {
    $sql = "SELECT p.*, u.username 
            FROM posts p 
            LEFT JOIN users u ON p.user_id = u.id 
            WHERE p.user_id = ? 
            ORDER BY p.created_at DESC 
            LIMIT 20";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([$currentUserId]);
    $posts = $stmt->fetchAll();
    
    // 为每个帖子查询评论
    foreach ($posts as &$post) {
        try {
            $sql = "SELECT c.*, u.username 
                    FROM comments c 
                    LEFT JOIN users u ON c.user_id = u.id 
                    WHERE c.post_id = ? 
                    ORDER BY c.created_at DESC";
            $stmt = $pdo->prepare($sql);
            $stmt->execute([$post['id']]);
            $post['comments'] = $stmt->fetchAll();
        } catch (PDOException $e) {
            $post['comments'] = [];
        }
    }
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
                <br/><br/><br/>
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
                            <div class="blog">
                                <div class="blog-content"><?php echo nl2br(htmlspecialchars($post['content'])); ?></div>
                                <?php if (!empty($post['image'])): ?>
                                <div class="blog-picture">
                                    <img src="<?php echo htmlspecialchars($post['image']); ?>" alt="微博配图">
                                </div>
                                <?php endif; ?>
                                <div class="post-time">
                                    <small><?php echo date('Y-m-d H:i:s', strtotime($post['created_at'])); ?></small>
                                </div>
                                <button class="like-button" onclick="toggleLike(this)">
                                    <span class="like-icon"></span>
                                    <span>点赞</span>
                                    <span class="like-count"><?php echo $post['likes_count']; ?></span>
                                </button>
                                <button class="comment-button" onclick="toggleComment(this)">
                                    <span class="comment-icon">💬</span>
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
                                        <button type="submit" class="delete-button" onclick="return confirm('确定要删除这条微博吗？');">删除</button>
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

    <script>
        // 点赞功能实现
        function toggleLike(button) {
            const likeCountSpan = button.querySelector('.like-count');
            let likeCount = parseInt(likeCountSpan.textContent);
            
            // 添加动画类
            button.classList.add('animating');
            
            // 切换点赞状态
            if (button.classList.contains('liked')) {
                // 取消点赞
                button.classList.remove('liked');
                likeCount--;
                button.querySelector('span:nth-child(2)').textContent = '点赞';
            } else {
                // 点赞
                button.classList.add('liked');
                likeCount++;
                button.querySelector('span:nth-child(2)').textContent = '已赞';
            }
            
            // 更新点赞数
            likeCountSpan.textContent = likeCount;
            
            // 移除动画类，以便下次点击可以重新添加
            setTimeout(() => {
                button.classList.remove('animating');
            }, 400);
        }
        
        // 页面加载时为每个点赞按钮添加随机初始点赞数
        document.addEventListener('DOMContentLoaded', function() {
            const likeButtons = document.querySelectorAll('.like-button');
            likeButtons.forEach(button => {
                // 生成1-100之间的随机点赞数
                const randomLikes = Math.floor(Math.random() * 100) + 1;
                const likeCountSpan = button.querySelector('.like-count');
                likeCountSpan.textContent = randomLikes;
                
                // 随机设置一些按钮为已点赞状态
                if (Math.random() > 0.7) {
                    button.classList.add('liked');
                    button.querySelector('span:nth-child(2)').textContent = '已赞';
                }
            });
        });
        
        // 评论功能实现
        function toggleComment(button) {
            const blogDiv = button.closest('.blog');
            const commentSection = blogDiv.querySelector('.comment-section');
            const commentInputArea = blogDiv.querySelector('.comment-input-area');
            
            // 切换显示/隐藏状态
            if (commentSection.style.display === 'none' || commentSection.style.display === '') {
                commentSection.style.display = 'block';
                commentInputArea.style.display = 'block';
            } else {
                commentSection.style.display = 'none';
                commentInputArea.style.display = 'none';
            }
        }
        
        // 发表评论功能实现
        function submitComment(button) {
            const commentInputArea = button.closest('.comment-input-area');
            const textarea = commentInputArea.querySelector('textarea');
            const commentText = textarea.value.trim();
            
            if (commentText === '') {
                alert('请输入评论内容！');
                return;
            }
            
            // 找到对应的评论区域
            const blogDiv = commentInputArea.closest('.blog');
            const commentSection = blogDiv.querySelector('.comment-section');
            
            // 创建新评论
            const newComment = document.createElement('div');
            newComment.textContent = '当前用户：' + commentText;
            newComment.style.marginBottom = '8px';
            
            // 将新评论添加到评论区域
            commentSection.appendChild(newComment);
            
            // 清空输入框
            textarea.value = '';
            
            // 确保评论区域可见
            commentSection.style.display = 'block';
        }
    </script>
</body>
</html>
