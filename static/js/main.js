// 页面加载完成后初始化
document.addEventListener('DOMContentLoaded', function() {
    initPostButton();
    initLikeButtons();
    initCommentButtons();
    initFileUpload();
});

// ==================== 发布微博功能 ====================

function initPostButton() {
    const submitButton = document.getElementById('content-submit');
    const contentInput = document.getElementById('contentInput');
    const fileInput = document.getElementById('weibo-picture');
    
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
        
        if (content.length > 140) {
            showMessage('字数超出限制（最多140字）', 'error');
            return;
        }
        
        // 如果有图片，先上传图片
        if (fileInput.files.length > 0) {
            uploadImageAndPost(content, fileInput.files[0]);
        } else {
            postWeibo(content, null);
        }
    });
    
    // 字数统计
    contentInput.addEventListener('input', function() {
        const errInfo = document.getElementById('errInfo');
        if (contentInput.value.length > 140) {
            errInfo.textContent = '字数超出限制！请精简您的内容。';
            errInfo.style.color = 'red';
        } else {
            errInfo.textContent = '';
        }
    });
}

// 上传图片并发布微博
function uploadImageAndPost(content, imageFile) {
    const submitButton = document.getElementById('content-submit');
    const originalText = submitButton.value;
    
    submitButton.value = '上传中...';
    submitButton.disabled = true;
    
    // 创建FormData
    const formData = new FormData();
    formData.append('image', imageFile);
    
    // 上传图片
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
        
        // 图片上传成功，获取图片路径
        const imagePath = response.post.image;
        postWeibo(content, imagePath, submitButton, originalText);
    }, true);
}

// 发布微博
function postWeibo(content, imagePath, submitButton = null, originalText = null) {
    if (!submitButton) {
        submitButton = document.getElementById('content-submit');
    }
    if (!originalText) {
        originalText = submitButton.value;
    }
    
    submitButton.value = '发布中...';
    submitButton.disabled = true;
    
    const data = {
        content: content,
        image: imagePath || ''
    };
    
    ajaxRequest('POST', 'api/action_post.php', data, function(error, response) {
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
        document.getElementById('file-name').textContent = '未选择文件';
        document.getElementById('weibo-picture').value = '';
        
        // 恢复按钮状态
        submitButton.value = originalText;
        submitButton.disabled = false;
        
        showMessage('发布成功！', 'success');
    });
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
    
    // 构建HTML
    let html = `
        <div class="username">${escapeHtml(post.username)}</div>
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
            <span class="like-icon"></span>
            <span>点赞</span>
            <span class="like-count">${post.likes_count}</span>
        </button>
        <button class="comment-button" onclick="toggleComment(this)">
            <span class="comment-icon">💬</span>
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
    commentDiv.style.marginBottom = '8px';
    commentDiv.style.padding = '8px';
    commentDiv.style.borderBottom = '1px solid #eee';
    
    commentDiv.innerHTML = `
        <strong>${escapeHtml(comment.username)}</strong>
        <small style="color:#999;">${comment.created_at}</small>
        <div style="margin-top:4px;">${escapeHtml(comment.content)}</div>
    `;
    
    commentSection.appendChild(commentDiv);
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
    } else {
        fileNameDisplay.textContent = '未选择文件';
        fileNameDisplay.title = '';
    }
}

// ==================== 工具函数 ====================

// HTML转义，防止XSS
function escapeHtml(text) {
    if (!text) return '';
    const div = document.createElement('div');
    div.textContent = text;
    return div.innerHTML;
}
