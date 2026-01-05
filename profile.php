<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>页面标题</title>
    <link rel="stylesheet" href="static\css\profile_style.css">
</head>
<body>
    <!-- 主容器 -->
    <div class="container">
        <div class="header">
            <div class="head-left">
                <h3>亲爱的（用户名称），欢迎回来!</h3>
            </div>
            <div class="head-right">
				<h3>XX微博-个人中心</h3>
                <br/><br/><br/>退出登录
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
                <div class="blog-area">
                    <form method="post" action="">
                    	<textarea placeholder="分享您的新鲜事···" rows="5" cols="30"
                    	name="contentInput" id="contentInput"></textarea>
                    	<br/>
                    	<label for="weibo-picture" class="file-upload-btn">选择图片</label>
                    	<input type="file" name="weibo-picture" id="weibo-picture"
                    	accept="image/jpeg,image/png,image/gif" onchange="displayFileName(this)"/>
                    	<span class="file-name" id="file-name"></span>
                    	<input type="submit" name="content-submit" id="content-submit" value="一键分享">
                    </form>
                </div>

                <div class="blog-display">
                    <p>您的往期发布</p>
					<div class="blog">
						<div class="data">2023-10-15 14:30</div>
						<div class="blog-content">今天是昨天的明天，也就是明天的昨天.....</div>
						<div class="blog-picture">
							<img src="D:\大学资料\图片集\图片1.jpg" alt="微博配图">
						</div>
					</div>
					<div class="blog">
						<div class="data">2023-10-10 09:15</div>
						<div class="blog-content">人生第一次到纽约....真是我的精神故乡啊····</div>
						<div class="blog-picture">
							<img src="D:\大学资料\图片集\图片1.jpg" alt="微博配图">
						</div>
					</div>
					<div class="blog">
						<div class="data">2023-09-28 18:45</div>
						<div class="blog-content">今天，我们恋爱啦！请大家祝福我们！</div>
						<div class="blog-picture">
							<img src="D:\大学资料\图片集\图片1.jpg" alt="微博配图">
						</div>
					</div>
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
        function displayFileName(input) {
            const fileName = input.files[0] ? input.files[0].name : '';
            document.getElementById('file-name').textContent = fileName;
        }
    </script>
</body>
</html>
