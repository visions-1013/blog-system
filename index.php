<!DOCTYPE html>
<html lang="zh-CN">
<head>
    <meta charset="UTF-8">
    <title>XX微博系统主页</title>
    <style type="text/css">
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        /* 全局body样式：避免页面溢出、统一字体 */
         body {
            background-color: #f5f5f5;
            font-family: "微软雅黑", sans-serif;
            color: #333; /* 统一全局文字颜色，提升可读性 */
        }
        /* 主容器：限定宽度+居中+阴影+背景白（更贴近实际产品） */
        .container {
            width: 1200px;
            margin: 0 auto;
            background-color: #fff;
            box-shadow: 0 0 8px rgba(0,0,0,0.1); /* 增加轻微阴影提升质感 */
            min-height: 100vh; /* 保证容器至少占满视口高度 */
            display: flex;
            flex-direction: column; /* 纵向排列：头部-主体-底部 */
        }
        /* 头部：独立分层（优化登录/注册+搜索框布局） */
        .header {
            background-color: aquamarine;
            padding: 15px 20px;
            height: 80px; /* 固定头部高度，符合常规导航栏尺寸 */
            display: flex; /* 横向排列所有元素 */
            align-items: center; /* 垂直居中对齐 */
            gap: 20px; /* 统一元素间距，替代margin更整洁 */
            justify-content: space-between; /* 欢迎语左对齐，登录/注册+搜索右对齐 */
        }
        /* 头部左侧：欢迎语 */
        .header .welcome-text {
            font-size: 18px;
            font-weight: bold;
            color: #2c7c7c;
        }
        /* 头部右侧：登录注册+搜索 容器（避免元素混乱） */
        .header .header-right {
            display: flex;
            align-items: center;
            gap: 16px; /* 登录/注册与搜索框之间的间距 */
        }
        /* 登录/注册链接样式优化 */
        .header .auth-link {
            color: #2c7c7c;
            text-decoration: none;
            font-size: 14px;
            font-weight: 500;
            padding: 6px 10px;
            border-radius: 4px;
            transition: all 0.2s;
        }
        .header .auth-link:hover {
            color: #1f6363;
            background-color: rgba(44, 124, 124, 0.1); /* 悬浮浅背景，提升交互体验 */
            text-decoration: none; /* 保持无下划线，更简洁 */
        }
        /* 头部搜索框样式（保留原有，无需修改） */
        .header input {
            padding: 6px 10px;
            border: 1px solid #ddd;
            border-radius: 4px 0 0 4px;
            outline: none;
            font-size: 14px;
        }
        .header button {
            padding: 6px 12px;
            border: 1px solid #2c7c7c;
            background-color: #2c7c7c;
            color: #fff;
            border-radius: 0 4px 4px 0;
            cursor: pointer;
            font-size: 14px;
        }
        .header button:hover {
            background-color: #1f6363;
        }
        /* 主体区域：Flex横向划分（左-中-右），占满剩余高度 */
        .main {
            display: flex;
            flex: 1;
            padding: 20px;
            gap: 20px;
        }
        /* 左侧边栏：样式保留原有 */
        .left-sidebar {
            flex: 2;
            background-color: #f0f8fb;
            border-radius: 4px;
            padding: 20px 15px;
        }
        .left-sidebar p {
            margin-bottom: 12px;
            font-size: 15px;
        }
        .left-sidebar a {
            color: #2c7c7c;
            text-decoration: none;
            transition: color 0.2s;
        }
        .left-sidebar a:hover {
            color: #1f6363;
            text-decoration: underline;
        }
        /* 中间主体：核心优化帖子发布表单 + 话题选择 */
        .main-content {
            flex: 6;
            background-color: #fefefe;
            border-radius: 4px;
            padding: 25px 20px;
            border: 1px solid #eee;
        }
        /* 帖子发布表单：整体样式优化 */
        .publish-form {
            margin-bottom: 30px; /* 与下方分类标题拉开间距，替代多个<br> */
            padding: 20px;
            background-color: #f9fcfe; /* 浅背景区分表单区域 */
            border-radius: 4px;
            border: 1px solid #e8f4f8;
        }
        /* 文本输入框优化 */
        .publish-form #blog {
            width: 100%; /* 铺满表单宽度，替代固定cols，适配布局 */
            padding: 12px 15px;
            border: 1px solid #eee;
            border-radius: 4px;
            outline: none;
            font-family: "微软雅黑", sans-serif;
            font-size: 14px;
            resize: vertical; /* 仅允许垂直拉伸，保持布局稳定 */
            margin-bottom: 12px; /* 与文件上传控件间距 */
            min-height: 100px; /* 增大默认高度，更易输入 */
        }
        /* 文本框聚焦样式 */
        .publish-form #blog:focus {
            border-color: #2c7c7c;
            box-shadow: 0 0 3px rgba(44, 124, 124, 0.2);
        }
        /* 图片上传控件优化 */
        .publish-form #upload_img {
            padding: 8px 0;
            margin-bottom: 12px; /* 与话题选择框间距 */
            color: #666;
            font-size: 14px;
            cursor: pointer;
            display: block; /* 独占一行，避免布局错乱 */
        }
        /* 话题选择功能：核心优化样式 */
        .publish-form .topic-select-wrapper {
            margin-bottom: 12px; /* 与提交按钮间距 */
            display: flex;
            align-items: center; /* 文字与下拉框垂直居中对齐 */
            gap: 10px; /* 文字与下拉框间距 */
        }
        /* 话题提示文字样式 */
        .publish-form .topic-label {
            font-size: 14px;
            color: #666;
            white-space: nowrap; /* 文字不换行 */
        }
        /* 下拉框样式优化 */
        .publish-form #select {
            flex: 0 0 auto; /* 不拉伸，保持合适宽度 */
            min-width: 120px; /* 最小宽度，避免选项被挤压 */
            padding: 6px 10px; /* 内边距，提升交互体验 */
            border: 1px solid #eee; /* 统一边框样式，贴合表单风格 */
            border-radius: 4px; /* 圆角，与其他表单元素一致 */
            outline: none; /* 去掉默认聚焦轮廓 */
            font-family: "微软雅黑", sans-serif; /* 统一字体 */
            font-size: 14px; /* 统一字号 */
            color: #333; /* 文字颜色 */
            background-color: #fff; /* 白色背景，更清爽 */
            cursor: pointer; /* 鼠标悬浮手型 */
            /* 移除默认下拉框样式（兼容大部分浏览器） */
            appearance: none;
            -webkit-appearance: none;
            -moz-appearance: none;
            /* 自定义下拉箭头（可选，更美观） */
            background-image: url("data:image/svg+xml;utf8,<svg xmlns='http://www.w3.org/2000/svg' width='10' height='6'><path fill='%23666' d='M0 0l5 6 5-6z'/></svg>");
            background-repeat: no-repeat;
            background-position: right 10px center;
            background-size: 10px;
            padding-right: 30px; /* 给自定义箭头预留空间 */
        }
        /* 下拉框聚焦样式 */
        .publish-form #select:focus {
            border-color: #2c7c7c; /* 聚焦时边框变主色调，与文本框一致 */
            box-shadow: 0 0 3px rgba(44, 124, 124, 0.2); /* 轻微阴影，提升质感 */
        }
        /* 下拉框选项样式 */
        .publish-form #select option {
            padding: 6px 10px; /* 选项内边距，提升可读性 */
            font-size: 14px;
            color: #333;
        }
        /* 提交按钮优化 */
        .publish-form #blog_submit {
            padding: 8px 24px;
            border: 1px solid #2c7c7c;
            background-color: #2c7c7c;
            color: #fff;
            border-radius: 4px;
            cursor: pointer;
            font-size: 14px;
            font-weight: 500;
            transition: background-color 0.2s;
            border: none;
        }
        .publish-form #blog_submit:hover {
            background-color: #1f6363;
        }
        /* 中间主体分类标题：样式保留原有 */
        .main-content p {
            font-size: 17px;
            font-weight: 600;
            color: #2c3e50;
            margin-bottom: 30px;
            padding-bottom: 8px;
            border-bottom: 1px solid #eee;
        }
        /* 右侧边栏：样式保留原有 */
        .right-sidebar {
            flex: 2;
            background-color: #f0f8fb;
            border-radius: 4px;
            padding: 10px;
        }
        .right-sidebar p {
            margin-bottom: 12px;
            font-size: 15px;
        }
        /* 底部：样式保留原有 */
        .footer {
            background-color: #f8f8f8;
            text-align: center;
            padding: 20px;
            color: #666;
            font-size: 14px;
            border-top: 1px solid #eee;
        }
        .footer p {
            margin-bottom: 6px;
            line-height: 1.5;
        }
    </style>
</head>
<body>
    <!-- 整体容器：包含头部、主体、底部 -->
    <div class="container">
        <!-- 头部：优化登录/注册布局 -->
        <div class="header">
            <!-- 欢迎语：单独类名，便于样式控制 -->
            <p class="welcome-text">XX微博主页，欢迎您的到来!</p>
            
            <!-- 头部右侧：登录/注册 + 搜索 统一容器 -->
            <div class="header-right">
                <!-- 登录/注册链接：添加类名，优化样式 -->
                <a href="login.php" target="_self" class="auth-link">登录</a>
                <a href="register.php" target="_self" class="auth-link">注册</a>
                
                <!-- 搜索表单：保留原有功能 -->
                <form method="post" action="">
                    <input type="text" name="search" id="search" placeholder="请输入您想搜索的微博!"/>
                    <button onclick="return checkInput()">点击搜索</button>
                </form>
            </div>
        </div>

        <!-- 主体：左中右三列布局 -->
        <div class="main">
            <!-- 左侧边栏：保留原有内容 -->
            <div class="left-sidebar">
                <p><a href="profile.php" target="_self">个人中心</a></p>
                <p><a href="#star">·明星</a></p>
                <p><a href="#coding">·学习天地</a></p>
                <p><a href="#society">·社会</a></p>
                <p><a href="#hotNews">·热搜</a></p>
            </div>
            
            <!-- 中间核心区域：优化帖子发布表单 + 话题选择 -->
            <div class="main-content">
                <!-- 帖子发布表单：添加类名，优化布局 -->
                <form action="" method="post" enctype="multipart/form-data" class="publish-form">
                    <!-- 文本框：移除cols，用CSS控制宽度 -->
                    <textarea id="blog" name="blog" rows="5" placeholder="有什么新鲜事想分享给大家?"></textarea>
                    <!-- 图片上传：保留原有功能 -->
                    <input type="file" name="upload_img" id="upload_img" accept="image/*">
                    <!-- 话题选择：新增包裹容器，优化布局 -->
                    <div class="topic-select-wrapper">
                        <span class="topic-label">选择话题：</span>
                        <select name="select" id="select">
                            <option value="1">学习</option>
                            <option value="2">娱乐</option>
                            <option value="3">热点</option>
                            <option value="4">生活</option>
                            <option value="5">游戏</option>
                        </select>
                    </div>
                    <!-- 提交按钮：保留原有功能 -->
                    <input type="submit" name="blog_submit" id="blog_submit" value="点击分享"/>
                </form>

                <!-- 分类标题：移除多个<br>，用CSS margin控制间距 -->
                <p id="hotNews">热搜</p><!--待填充内容     -->
                <p id="coding">学习天地</p><!--待填充内容     -->
                <p id="star">明星趣闻</p><!--待填充内容     -->
                <p id="society">社会热点</p><!--待填充内容     -->
            </div>

            <!-- 右侧边栏：保留原有内容 -->
            <div class="right-sidebar">
                <p>热搜1，待填充(带有帖子标题的链接)</p>
                <p>热搜2，待填充(带有帖子标题的链接)</p>
                <p>热搜3，待填充(带有帖子标题的链接)</p>
            </div>
        </div>

        <!-- 底部：保留原有内容 -->
        <div class="footer">
            <p>&copy;XX微博  2026 保留所有权力</p>
            <p>本网站仅用于娱乐，学习使用</p>
        </div>
    </div>
</body>
<script>
	function checkInput(){
		var input=document.getElementById("search");
		var text=input.value.trim();
		if (text.lengh==0) return false;
		else return true;
	}
	
	
	
	
</script>
</html>