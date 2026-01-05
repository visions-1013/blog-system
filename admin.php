<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>XX微博 - 管理后台</title>
    <style>
        *{
            margin:0;
            padding:0;
            box-sizing:border-box;
        } 
        .container{
            width:1400px;
            margin:0 auto;
            background-color:#fdfdfd;
            box-shadow: 0 0 12px rgba(0,0,0,0.02);
            border-radius: 8px;
            overflow: hidden;
        }
        .header{
            padding:15px 20px;
            color:#4a5568;
            background-color: #f0f8fb;
            display: flex;
            border-bottom: 1px solid #e8f4f8;
        }
        .head-left{
            flex:3;
            margin-right:10px;
            background-color: #f0f8fb;
            padding: 8px 12px;
            border-radius: 6px;
        }
        .head-right{
            flex:2;
            text-align: right;
            background-color: #f0f8fb;
            padding: 8px 12px;
            border-radius: 6px;
        }
        .footer{
            text-align:center;
            padding:20px;
            background-color: #f0f8fb;
            color: #718096;
            border-top: 1px solid #e8f4f8;
        }
        .body{
            display:flex;
            padding:15px;
            background-color: #f5fafe;
        }
        .main-part{
            flex:3;
            margin-right:10px;
            background-color: #ffffff;
            padding: 15px;
            border-radius: 6px;
            box-shadow: 0 1px 2px rgba(0,0,0,0.01);
        }
        .right-sider{
            flex:1;
            background-color: #ffffff;
            padding: 10px;
            border-radius: 6px;
            box-shadow: 0 1px 2px rgba(0,0,0,0.01);
        }
        
        /* 导航栏样式 */
        .admin-nav{
            background-color: #ffffff;
            border-bottom: 1px solid #e8f4f8;
            padding: 0 20px;
        }
        .admin-nav ul{
            display: flex;
            list-style: none;
        }
        .admin-nav li{
            padding: 12px 18px;
        }
        .admin-nav a{
            text-decoration: none;
            color: #4a5568;
            font-size: 14px;
            transition: color 0.2s;
        }
        .admin-nav a:hover{
            color: #3182ce;
        }
        .admin-nav li.active{
            border-bottom: 2px solid #3182ce;
        }
        .admin-nav li.active a{
            color: #3182ce;
            font-weight: 500;
        }
        
        /* 按钮样式 */
        .admin-btn{
            background-color: #3182ce;
            color: white;
            border: none;
            padding: 6px 12px;
            border-radius: 4px;
            cursor: pointer;
            font-size: 13px;
            transition: background-color 0.2s;
            margin-right: 8px;
            margin-bottom: 8px;
        }
        .admin-btn:hover{
            background-color: #2b6cb0;
        }
        .btn-danger{
            background-color: #f56565;
        }
        .btn-danger:hover{
            background-color: #e53e3e;
        }
        .btn-success{
            background-color: #48bb78;
        }
        .btn-success:hover{
            background-color: #38a169;
        }
        .btn-warning{
            background-color: #ecc94b;
        }
        .btn-warning:hover{
            background-color: #d69e2e;
        }
        
        /* 管理数据显示区域样式 */
        .admin-display{
            padding: 15px;
        }
        .admin-display h4{
            color: #4a5568;
            font-size: 16px;
            margin-bottom: 15px;
            padding-bottom: 8px;
            border-bottom: 1px solid #e8f4f8;
        }
        .data-card{
            padding: 15px;
            border-radius: 6px;
            background-color: #f9fafc;
            margin-bottom: 15px;
            border: 1px solid #e8f4f8;
        }
        
        /* 用户发帖内容样式 */
        .post-content{
            color: #2d3748;
            font-size: 13px;
            line-height: 1.6;
            margin: 10px 0;
            padding: 10px;
            background-color: #fff;
            border-radius: 4px;
            border-left: 3px solid #e8f4f8;
        }
        .post-info{
            display: flex;
            justify-content: space-between;
            font-size: 12px;
            color: #718096;
            margin-bottom: 5px;
        }
        .post-image{
            margin-top: 10px;
        }
        .post-image img{
            max-width: 200px;
            max-height: 120px;
            border-radius: 4px;
        }
        
        /* 右侧边栏 - 用户管理样式 */
        .right-sider h4{
            color: #4a5568;
            font-size: 15px;
            margin: 15px 0 10px;
            padding-bottom: 5px;
            border-bottom: 1px solid #e8f4f8;
        }
        .user-list{
            display: flex;
            flex-wrap: wrap;
            gap: 8px;
        }
        .user-card{
            flex: 1 1 calc(50% - 8px);
            background-color: #f9fafc;
            padding: 10px;
            border-radius: 4px;
            border: 1px solid #e8f4f8;
            font-size: 12px;
        }
        .user-name{
            color: #2d3748;
            font-weight: 500;
            margin-bottom: 4px;
        }
        .user-id{
            color: #718096;
            font-size: 11px;
            margin-bottom: 6px;
        }
        .user-status{
            display: inline-block;
            padding: 2px 6px;
            border-radius: 10px;
            font-size: 10px;
            margin-bottom: 6px;
        }
        .status-normal{
            background-color: #c6f6d5;
            color: #22543d;
        }
        .status-banned{
            background-color: #fed7d7;
            color: #742a2a;
        }
        
        /* 搜索区域样式 */
        .search-area{
            margin-bottom: 15px;
        }
        .search-input{
            width: 100%;
            padding: 8px 12px;
            border: 1px solid #e8f4f8;
            border-radius: 4px;
            font-size: 13px;
        }
        .search-input:focus{
            outline: none;
            border-color: #b3e0f2;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <div class="head-left">
                <h3>管理员 admin，您好！</h3>
            </div>
            <div class="head-right">
                <h3>管理系统</h3>
                <br/><br/><button class="admin-btn btn-warning" onclick="logout()">退出登录</button>
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
                    
                    <!-- 用户发帖内容1 -->
                    <div class="data-card">
                        <div class="post-info">
                            <div>
                                <strong>用户：</strong>章航渝(神人) (ID: U10023) |
                                <strong>时间：</strong>2023-10-15 14:30
                            </div>
                        </div>
                        <div class="post-content">
                            今天是昨天的明天，也就是明天的昨天.....
                        </div>
                        <div class="post-image">
                            <img src="data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='200' height='120' viewBox='0 0 200 120'%3E%3Crect width='200' height='120' fill='%23f0f8fb'/%3E%3Ctext x='100' y='60' font-family='Arial' font-size='12' fill='%234a5568' text-anchor='middle'%3E微博配图%3C/text%3E%3C/svg%3E" alt="微博配图">
                        </div>
                        <div style="margin-top: 10px;">
                            <button class="admin-btn btn-success" onclick="approvePost('P001')">通过</button>
                            <button class="admin-btn btn-danger" onclick="deletePost('P001')">删除</button>
                        </div>
                    </div>
                    
                    <!-- 用户发帖内容2 -->
                    <div class="data-card">
                        <div class="post-info">
                            <div>
                                <strong>用户：</strong>三生有幸 (ID: U10022) |
                                <strong>时间：</strong>2023-10-10 09:15
                            </div>
                        </div>
                        <div class="post-content">
                            人生第一次到纽约....真是我的精神故乡啊····
                        </div>
                        <div class="post-image">
                            <img src="data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='200' height='120' viewBox='0 0 200 120'%3E%3Crect width='200' height='120' fill='%23f0f8fb'/%3E%3Ctext x='100' y='60' font-family='Arial' font-size='12' fill='%234a5568' text-anchor='middle'%3E微博配图%3C/text%3E%3C/svg%3E" alt="微博配图">
                        </div>
                        <div style="margin-top: 10px;">
                            <button class="admin-btn btn-success" onclick="approvePost('P002')">通过</button>
                            <button class="admin-btn btn-danger" onclick="deletePost('P002')">删除</button>
                        </div>
                    </div>
                    
                    <!-- 用户发帖内容3 -->
                    <div class="data-card">
                        <div class="post-info">
                            <div>
                                <strong>用户：</strong>周凯涵 (ID: U10021) |
                                <strong>时间：</strong>2023-10-13 18:45
                            </div>
                        </div>
                        <div class="post-content">
                            今天，我们恋爱啦！请大家祝福我们！❤️🎉
                        </div>
                        <div class="post-image">
                            <img src="data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='200' height='120' viewBox='0 0 200 120'%3E%3Crect width='200' height='120' fill='%23f0f8fb'/%3E%3Ctext x='100' y='60' font-family='Arial' font-size='12' fill='%234a5568' text-anchor='middle'%3E微博配图%3C/text%3E%3C/svg%3E" alt="微博配图">
                        </div>
                        <div style="margin-top: 10px;">
                            <button class="admin-btn btn-success" onclick="approvePost('P003')">通过</button>
                            <button class="admin-btn btn-danger" onclick="deletePost('P003')">删除</button>
                        </div>
                    </div>
                    
                    <!-- 用户发帖内容4 -->
                    <div class="data-card">
                        <div class="post-info">
                            <div>
                                <strong>用户：</strong>楠楠 (ID: U10020) |
                                <strong>时间：</strong>2023-09-28 18:45
                            </div>
                        </div>
                        <div class="post-content">
                            分享今天的美食探店经历！这家日料店的寿司真的绝了，强烈推荐给大家！
                        </div>
                        <div class="post-image">
                            <img src="" alt="微博配图">
                        </div>
                        <div style="margin-top: 10px;">
                            <button class="admin-btn btn-success" onclick="approvePost('P004')">通过</button>
                            <button class="admin-btn btn-danger" onclick="deletePost('P004')">删除</button>
                        </div>
                    </div>
                    
                    <!-- 用户发帖内容5 -->
                    <div class="data-card">
                        <div class="post-info">
                            <div>
                                <strong>用户：</strong>杭州小航 (ID: U10019) |
                                <strong>时间：</strong>2023-09-25 10:20
                            </div>
                        </div>
                        <div class="post-content">
                            今天的学习笔记：前端开发中的CSS Grid布局真的太强大了！
                        </div>
                        <div class="post-image">
                            <img src="data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='200' height='120' viewBox='0 0 200 120'%3E%3Crect width='200' height='120' fill='%23f0f8fb'/%3E%3Ctext x='100' y='60' font-family='Arial' font-size='12' fill='%234a5568' text-anchor='middle'%3E学习笔记%3C/text%3E%3C/svg%3E" alt="学习笔记截图">
                        </div>
                        <div style="margin-top: 10px;">
                            <button class="admin-btn btn-success" onclick="approvePost('P005')">通过</button>
                            <button class="admin-btn btn-danger" onclick="deletePost('P005')">删除</button>
                        </div>
                    </div>
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
                            <button class="admin-btn btn-success" style="font-size: 11px; padding: 4px 8px;" onclick="restoreUser('U10021')">恢复账号</button>
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