// 页面加载完成后初始化
document.addEventListener('DOMContentLoaded', function() {
    initPostButton();
    initLikeButtons();
    initCommentButtons();
    initFileUpload();
    initFollowButtons();
    loadFollows();
});

// ==================== 发布微博功能 ====================

function initPostButton() {
    const submitButton = document.getElementById('content-submit');
    const contentInput = document.getElementById('contentInput');
    const fileInput = document.getElementById('weibo-picture');
    const charCount = document.getElementById('char-count');
    
    // 检查元素是否存在（避免在profile.php和search.php页面出错）
    if (!submitButton || !contentInput || !fileInput) {
        console.log('initPostButton: 元素不存在，跳过初始化');
        return;
    }
    
    submitButton.addEventListener('click', function(e) {
        e.preventDefault();
        
        const content = contentInput.value.trim();
        
        // 验证内容
        if (!content) {
            showMessage('请输入微博内容', 'error');
            return;
        }
        
        // 一次性提交所有数据（内容+图片）
        postWeibo(content, fileInput.files[0]);
    });
    
    // 字数统计（不限制字数）
    contentInput.addEventListener('input', function() {
        const currentLength = this.value.length;
        
        // 只显示字数，不限制
        if (charCount) {
            charCount.textContent = currentLength + '字';
        }
    });
}

// 发布微博（一次性提交内容和图片）
function postWeibo(content, imageFile) {
    const submitButton = document.getElementById('content-submit');
    const originalText = submitButton.value;
    
    submitButton.value = '发布中...';
    submitButton.disabled = true;
    
    // 创建FormData，包含内容和图片
    const formData = new FormData();
    formData.append('content', content);
    
    // 如果有图片，添加到FormData
    if (imageFile) {
        formData.append('image', imageFile);
    }
    
    // 一次性提交
    ajaxRequest('POST', 'api/action_post.php', formData, function(error, response) {
        if (error) {
            showMessage(error, 'error');
            submitButton.value = originalText;
            submitButton.disabled = false;
            return;
        }
        
        if (!response.success) {
            showMessage(response.error, 'error');
            submitButton.value = originalText;
            submitButton.disabled = false;
            return;
        }
        
        // 发布成功，创建新微博节点
        createPostElement(response.post);
        
        // 清空输入框
        document.getElementById('contentInput').value = '';
        document.getElementById('weibo-picture').value = '';
        document.getElementById('file-name').style.display = 'none';
        document.getElementById('file-name').textContent = '';
        
        // 重置字数统计
        if (document.getElementById('char-count')) {
            document.getElementById('char-count').textContent = '0字';
        }
        
        // 恢复按钮状态
        submitButton.value = originalText;
        submitButton.disabled = false;
        
        showMessage('发布成功！', 'success');
    }, true); // true表示使用FormData
}

// 创建微博DOM元素
function createPostElement(post) {
    const blogDisplay = document.querySelector('.blog-display');
    const noPosts = document.querySelector('.no-posts');
    
    // 如果有"暂无微博"提示，移除它
    if (noPosts) {
        noPosts.remove();
    }
    
    // 创建微博div
    const blogDiv = document.createElement('div');
    blogDiv.className = 'blog';
    blogDiv.setAttribute('data-post-id', post.id);
    
    // 构建HTML - 包含头像和用户名
    let html = `
        <div class="post-user">
            <img src="static/img/${escapeHtml(post.avatar || 'default.png')}" 
                 class="post-user-avatar" 
                 alt="${escapeHtml(post.username)}的头像">
            <div class="username">${escapeHtml(post.username)}</div>
        </div>
        <div class="blog-content">${escapeHtml(post.content).replace(/\n/g, '<br>')}</div>
    `;
    
    // 如果有图片
    if (post.image) {
        html += `
            <div class="blog-picture">
                <img src="${escapeHtml(post.image)}" alt="微博配图">
            </div>
        `;
    }
    
    html += `
        <div class="post-time">
            <small>${post.created_at}</small>
        </div>
        <button class="like-button" data-liked="false" onclick="toggleLike(this)">
            <i class="fa-solid fa-thumbs-up"></i>
            <span>点赞</span>
            <span class="like-count">${post.likes_count}</span>
        </button>
        <button class="comment-button" onclick="toggleComment(this)">
            <i class="fa-regular fa-comment"></i>
            <span>评论</span>
        </button>
        <div class="comment-section" style="display: none;"></div>
        <div class="comment-input-area" style="display: none;">
            <textarea placeholder="写下你的评论..."></textarea>
            <button class="comment-submit-btn" onclick="submitComment(this)">发表评论</button>
        </div>
    `;
    
    blogDiv.innerHTML = html;
    
    // 插入到列表顶部
    blogDisplay.insertBefore(blogDiv, blogDisplay.firstChild);
    
    // 添加动画效果
    blogDiv.style.animation = 'fadeIn 0.5s ease-in';
}

// ==================== 点赞功能 ====================

function initLikeButtons() {
    const likeButtons = document.querySelectorAll('.like-button');
    likeButtons.forEach(button => {
        // 根据data-liked属性设置初始状态
        const isLiked = button.getAttribute('data-liked') === 'true';
        if (isLiked) {
            button.classList.add('liked');
            const textSpan = button.querySelector('span:nth-child(2)');
            if (textSpan) textSpan.textContent = '已赞';
        }
    });
}

function toggleLike(button) {
    const blogDiv = button.closest('.blog');
    const postId = blogDiv.getAttribute('data-post-id');
    const likeCountSpan = button.querySelector('.like-count');
    const textSpan = button.querySelector('span:nth-child(2)');
    
    // 立即切换UI状态（乐观更新）
    const isLiked = button.classList.contains('liked');
    let newLikeCount = parseInt(likeCountSpan.textContent);
    
    if (isLiked) {
        button.classList.remove('liked');
        textSpan.textContent = '点赞';
        newLikeCount--;
    } else {
        button.classList.add('liked');
        textSpan.textContent = '已赞';
        newLikeCount++;
    }
    
    likeCountSpan.textContent = newLikeCount;
    
    // 添加动画
    button.classList.add('animating');
    setTimeout(() => {
        button.classList.remove('animating');
    }, 400);
    
    // 发送AJAX请求
    ajaxRequest('POST', 'api/action_like.php', {
        post_id: postId
    }, function(error, response) {
        if (error) {
            // 请求失败，恢复状态
            if (isLiked) {
                button.classList.add('liked');
                textSpan.textContent = '已赞';
                newLikeCount++;
            } else {
                button.classList.remove('liked');
                textSpan.textContent = '点赞';
                newLikeCount--;
            }
            likeCountSpan.textContent = newLikeCount;
            showMessage(error, 'error');
            return;
        }
        
        if (!response.success) {
            // 操作失败，恢复状态
            if (isLiked) {
                button.classList.add('liked');
                textSpan.textContent = '已赞';
                newLikeCount++;
            } else {
                button.classList.remove('liked');
                textSpan.textContent = '点赞';
                newLikeCount--;
            }
            likeCountSpan.textContent = newLikeCount;
            showMessage(response.error, 'error');
            return;
        }
        
        // 更新为实际的点赞数
        likeCountSpan.textContent = response.likes_count;
        
        // 更新按钮状态
        if (response.liked) {
            button.classList.add('liked');
            textSpan.textContent = '已赞';
        } else {
            button.classList.remove('liked');
            textSpan.textContent = '点赞';
        }
    });
}

// ==================== 评论功能 ====================

function initCommentButtons() {
    // 初始化时不做任何操作，因为按钮已经有了onclick="toggleComment(this)"
    console.log('评论按钮初始化完成');
}

function toggleComment(button) {
    const blogDiv = button.closest('.blog');
    const postId = blogDiv.getAttribute('data-post-id');
    const commentSection = blogDiv.querySelector('.comment-section');
    const commentInputArea = blogDiv.querySelector('.comment-input-area');
    
    // 切换显示/隐藏状态
    if (commentSection.style.display === 'none' || commentSection.style.display === '') {
        commentSection.style.display = 'block';
        commentInputArea.style.display = 'block';
        
        // 加载评论列表
        loadComments(postId, commentSection);
    } else {
        commentSection.style.display = 'none';
        commentInputArea.style.display = 'none';
    }
}

// 加载评论列表
function loadComments(postId, commentSection) {
    console.log('加载评论 - postId:', postId);
    commentSection.innerHTML = '<p style="text-align:center;color:#999;">加载中...</p>';
    
    ajaxRequest('GET', 'api/action_get_comments.php', {
        post_id: postId
    }, function(error, response) {
        console.log('评论加载响应 - error:', error, 'response:', response);
        
        if (error) {
            commentSection.innerHTML = '<p style="text-align:center;color:red;">加载失败，请重试</p>';
            showMessage(error, 'error');
            return;
        }
        
        if (!response.success) {
            commentSection.innerHTML = '<p style="text-align:center;color:red;">' + escapeHtml(response.error) + '</p>';
            return;
        }
        
        // 显示评论列表
        if (response.comments.length === 0) {
            commentSection.innerHTML = '<p style="text-align:center;color:#999;">暂无评论，快来抢沙发吧！</p>';
        } else {
            commentSection.innerHTML = '';
            response.comments.forEach(comment => {
                createCommentElement(comment, commentSection);
            });
        }
    });
}

// 创建评论DOM元素
function createCommentElement(comment, commentSection) {
    const commentDiv = document.createElement('div');
    commentDiv.className = 'comment-item';
    commentDiv.setAttribute('data-comment-id', comment.id);
    commentDiv.style.marginBottom = '12px';
    commentDiv.style.padding = '12px';
    
    let deleteButtonHtml = '';
    if (comment.can_delete) {
        deleteButtonHtml = `
            <button class="delete-comment-btn" onclick="confirmDeleteComment(this)" title="删除评论">
                <i class="fa-solid fa-trash"></i>
            </button>
        `;
    }
    
    commentDiv.innerHTML = `
        <div class="comment-content-wrapper">
            <strong>${escapeHtml(comment.username)}</strong>
            <small style="color:#666;margin-left:8px;">${comment.created_at}</small>
            <div style="margin-top:8px;color:#333;">${escapeHtml(comment.content)}</div>
            ${deleteButtonHtml}
        </div>
    `;
    
    commentSection.appendChild(commentDiv);
}

// 确认删除评论
function confirmDeleteComment(button) {
    const commentDiv = button.closest('.comment-item');
    const commentContent = commentDiv.querySelector('.comment-content-wrapper div:last-of-type').textContent.trim();
    
    // 二次确认
    if (confirm('确定要删除这条评论吗？')) {
        const commentId = commentDiv.getAttribute('data-comment-id');
        deleteCommentAction(button, commentId);
    }
}

// 执行删除评论
function deleteCommentAction(button, commentId) {
    const commentDiv = button.closest('.comment-item');
    
    button.disabled = true;
    button.style.opacity = '0.5';
    
    deleteComment(commentId, function(error, response) {
        if (error) {
            showMessage(error, 'error');
            button.disabled = false;
            button.style.opacity = '1';
            return;
        }
        
        if (!response.success) {
            showMessage(response.error, 'error');
            button.disabled = false;
            button.style.opacity = '1';
            return;
        }
        
        // 删除成功，移除评论元素
        commentDiv.style.transition = 'opacity 0.3s ease';
        commentDiv.style.opacity = '0';
        
        setTimeout(() => {
            commentDiv.remove();
            
            // 检查是否还有评论，如果没有则显示"暂无评论"
            const commentSection = commentDiv.parentElement;
            const remainingComments = commentSection.querySelectorAll('.comment-item');
            if (remainingComments.length === 0) {
                commentSection.innerHTML = '<p style="text-align:center;color:#999;">暂无评论，快来抢沙发吧！</p>';
            }
            
            showMessage('删除成功！', 'success');
        }, 300);
    });
}

// 提交评论
function submitComment(button) {
    const blogDiv = button.closest('.blog');
    const postId = blogDiv.getAttribute('data-post-id');
    const commentInputArea = button.closest('.comment-input-area');
    const textarea = commentInputArea.querySelector('textarea');
    const commentText = textarea.value.trim();
    const commentSection = blogDiv.querySelector('.comment-section');
    
    console.log('提交评论 - postId:', postId, 'content:', commentText);
    
    if (!commentText) {
        showMessage('请输入评论内容', 'error');
        return;
    }
    
    if (!postId || postId === 'null') {
        showMessage('无法获取微博ID，请刷新页面重试', 'error');
        return;
    }
    
    const originalText = button.textContent;
    button.textContent = '提交中...';
    button.disabled = true;
    
    const data = {
        post_id: parseInt(postId),
        content: commentText
    };
    
    console.log('发送评论数据:', data);
    
    ajaxRequest('POST', 'api/action_comment.php', data, function(error, response) {
        console.log('评论响应:', error, response);
        
        if (error) {
            showMessage(error, 'error');
            button.textContent = originalText;
            button.disabled = false;
            return;
        }
        
        if (!response.success) {
            showMessage(response.error, 'error');
            button.textContent = originalText;
            button.disabled = false;
            return;
        }
        
        // 评论成功，创建新评论元素
        // 如果显示"暂无评论"提示，移除它
        const noCommentMsg = commentSection.querySelector('p');
        if (noCommentMsg && noCommentMsg.textContent.includes('暂无评论')) {
            noCommentMsg.remove();
        }
        
        createCommentElement(response.comment, commentSection);
        
        // 清空输入框
        textarea.value = '';
        
        // 恢复按钮状态
        button.textContent = originalText;
        button.disabled = false;
        
        showMessage('评论成功！', 'success');
    });
}

// ==================== 文件上传功能 ====================

function initFileUpload() {
    const fileInput = document.getElementById('weibo-picture');
    
    fileInput.addEventListener('change', function() {
        updateFileName(this);
    });
}

function updateFileName(input) {
    const fileNameDisplay = document.getElementById('file-name');
    if (input.files.length > 0) {
        let fileName = input.files[0].name;
        if (fileName.length > 20) {
            fileName = fileName.substring(0, 17) + '...';
        }
        fileNameDisplay.textContent = fileName;
        fileNameDisplay.title = input.files[0].name;
        fileNameDisplay.style.display = 'inline';
    } else {
        fileNameDisplay.style.display = 'none';
        fileNameDisplay.textContent = '';
        fileNameDisplay.title = '';
    }
}

// ==================== 关注功能 ====================

function initFollowButtons() {
    const followButtons = document.querySelectorAll('.follow-button');
    followButtons.forEach(button => {
        // 根据data-following属性设置初始状态
        const isFollowing = button.getAttribute('data-following') === 'true';
        if (isFollowing) {
            button.classList.add('following');
            button.classList.remove('not-following');
            button.innerHTML = '<i class="fa-solid fa-check"></i> 已关注';
        } else {
            button.classList.add('not-following');
            button.classList.remove('following');
            button.innerHTML = '<i class="fa-solid fa-plus"></i> 关注';
        }
    });
}

function toggleFollow(button) {
    const userId = button.getAttribute('data-user-id');
    const isFollowing = button.classList.contains('following');
    
    // 立即切换UI状态（乐观更新）
    if (isFollowing) {
        button.classList.remove('following');
        button.classList.add('not-following');
        button.innerHTML = '<i class="fa-solid fa-plus"></i> 关注';
    } else {
        button.classList.add('following');
        button.classList.remove('not-following');
        button.innerHTML = '<i class="fa-solid fa-check"></i> 已关注';
    }
    
    // 发送AJAX请求
    ajaxRequest('POST', 'api/action_follow.php', {
        target_user_id: parseInt(userId)
    }, function(error, response) {
        if (error) {
            // 请求失败，恢复状态
            if (isFollowing) {
                button.classList.add('following');
                button.classList.remove('not-following');
                button.innerHTML = '<i class="fa-solid fa-check"></i> 已关注';
            } else {
                button.classList.remove('following');
                button.classList.add('not-following');
                button.innerHTML = '<i class="fa-solid fa-plus"></i> 关注';
            }
            showMessage(error, 'error');
            return;
        }
        
        if (!response.success) {
            // 操作失败，恢复状态
            if (isFollowing) {
                button.classList.add('following');
                button.classList.remove('not-following');
                button.innerHTML = '<i class="fa-solid fa-check"></i> 已关注';
            } else {
                button.classList.remove('following');
                button.classList.add('not-following');
                button.innerHTML = '<i class="fa-solid fa-plus"></i> 关注';
            }
            showMessage(response.error, 'error');
            return;
        }
        
        // 更新为实际的状态
        if (response.is_following) {
            button.classList.add('following');
            button.classList.remove('not-following');
            button.innerHTML = '<i class="fa-solid fa-check"></i> 已关注';
        } else {
            button.classList.remove('following');
            button.classList.add('not-following');
            button.innerHTML = '<i class="fa-solid fa-plus"></i> 关注';
        }
        
        // 显示提示信息
        showMessage(response.message, 'success');
        
        // 重新加载关注列表
        loadFollows();
    });
}

function loadFollows() {
    console.log('loadFollows() 函数开始执行');
    
    const followsList = document.getElementById('follows-list');
    
    console.log('follows-list 元素:', followsList);
    
    // 如果元素不存在，跳过（例如在未登录状态）
    if (!followsList) {
        console.log('follows-list 元素不存在，跳过加载');
        return;
    }
    
    // 检查是否在用户资料页面（查看他人资料页）
    const currentPath = window.location.pathname;
    const isUserProfilePage = currentPath.includes('user_profile.php');
    
    console.log('当前页面路径:', currentPath);
    console.log('是否为用户资料页:', isUserProfilePage);
    
    // 如果在查看他人资料的页面，不加载关注列表
    if (isUserProfilePage) {
        followsList.innerHTML = '<p style="text-align:center;color:#999;">查看他人资料</p>';
        return;
    }
    
    followsList.innerHTML = '<p style="text-align:center;color:#999;">加载中...</p>';
    
    console.log('准备发送AJAX请求获取关注列表');
    
    ajaxRequest('GET', 'api/get_follows.php', {}, function(error, response) {
        console.log('AJAX响应 - error:', error);
        console.log('AJAX响应 - response:', response);
        
        if (error) {
            console.error('加载关注列表失败:', error);
            followsList.innerHTML = '<p style="text-align:center;color:red;">加载失败</p>';
            return;
        }
        
        if (!response.success) {
            console.error('API返回失败:', response.error);
            followsList.innerHTML = '<p style="text-align:center;color:red;">' + escapeHtml(response.error) + '</p>';
            return;
        }
        
        console.log('获取到的关注用户数量:', response.data.length);
        
        // 显示关注列表
        if (response.data.length === 0) {
            followsList.innerHTML = '<p style="text-align:center;color:#999;">暂无关注</p>';
        } else {
            followsList.innerHTML = '';
            response.data.forEach(user => {
                console.log('创建关注用户元素:', user);
                createFollowUserElement(user, followsList);
            });
        }
        
        console.log('关注列表加载完成');
    });
}

function createFollowUserElement(user, container) {
    const userDiv = document.createElement('div');
    userDiv.className = 'user-item';
    userDiv.style.cursor = 'pointer';
    userDiv.onclick = function() {
        window.location.href = 'user_profile.php?user_id=' + user.id;
    };
    
    userDiv.innerHTML = `
        <img src="static/img/${escapeHtml(user.avatar || 'default.png')}" alt="${escapeHtml(user.username)}">
        <span>${escapeHtml(user.username)}</span>
    `;
    
    container.appendChild(userDiv);
}

// ==================== 工具函数 ====================

// HTML转义，防止XSS
function escapeHtml(text) {
    if (!text) return '';
    const div = document.createElement('div');
    div.textContent = text;
    return div.innerHTML;
}
