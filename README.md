# 微博系统课程设计技术方案书

**项目名称**：基于WAMP的微型微博系统设计与实现
**小组成员**：周楷涵、章晨阳、章航渝

---

## 1. 系统概述

### 1.1 项目背景
本项目旨在模仿新浪微博的核心功能，开发一个基于Web的社交媒体平台。系统需支持多用户交互，包括微博发布、浏览、评论及点赞等功能，区分管理员与普通用户权限，并注重用户体验，采用AJAX技术实现页面无刷新交互。

### 1.2 技术架构
* **服务器架构**：B/S
* **开发环境**：WAMP Server
* **后端语言**：PHP
* **前端技术**：HTML, CSS, JavaScript, AJAX
* **数据库**：MySQL

---

## 2. 数据库设计

数据库命名为 `weibo_sys`，字符集采用 `utf8mb4_unicode_ci` 

### 2.1 用户表 (`users`)
用于存储用户基础信息及权限标识。

| 字段名 | 类型 | 长度 | 说明 | 约束 |
| :--- | :--- | :--- | :--- | :--- |
| `id` | INT | 11 | 用户ID | PK, Auto Increment |
| `username` | VARCHAR | 50 | 登录用户名 | Unique, Not Null |
| `password` | VARCHAR | 255 | 密码（MD5加密） | Not Null |
| `role` | TINYINT | 1 | 权限 | 0:普通用户, 1:管理员 |
| `avatar` | VARCHAR | 100 | 头像路径 | Default 'default.png' |
| `created_at` | DATETIME | - | 注册时间 | Default CURRENT_TIMESTAMP |

### 2.2 微博内容表 (`posts`)
存储用户发布的微博内容。

| 字段名 | 类型 | 长度 | 说明 | 约束 |
| :--- | :--- | :--- | :--- | :--- |
| `id` | INT | 11 | 微博ID | PK, Auto Increment |
| `user_id` | INT | 11 | 发布者ID | FK -> users.id |
| `content` | TEXT | - | 微博正文 | Not Null |
| `likes_count`| INT | 11 | 点赞数缓存 | Default 0 |
| `created_at` | DATETIME | - | 发布时间 | Default CURRENT_TIMESTAMP |

### 2.3 评论表 (`comments`)
存储针对微博的评论数据。

| 字段名 | 类型 | 长度 | 说明 | 约束 |
| :--- | :--- | :--- | :--- | :--- |
| `id` | INT | 11 | 评论ID | PK, Auto Increment |
| `post_id` | INT | 11 | 关联微博ID | FK -> posts.id |
| `user_id` | INT | 11 | 评论者ID | FK -> users.id |
| `content` | VARCHAR | 255 | 评论内容 | Not Null |
| `created_at` | DATETIME | - | 评论时间 | Default CURRENT_TIMESTAMP |

### 2.4 点赞记录表 (`likes`)
用于防止用户重复点赞，维护数据一致性。

| 字段名 | 类型 | 长度 | 说明 | 约束 |
| :--- | :--- | :--- | :--- | :--- |
| `id` | INT | 11 | 记录ID | PK, Auto Increment |
| `user_id` | INT | 11 | 点赞用户 | FK -> users.id |
| `post_id` | INT | 11 | 被点赞微博 | FK -> posts.id |

---

## 3. 关键功能与AJAX实现 

系统包含以下三处核心JS动态效果：

### 3.1 动态发布微博 (AJAX Post)
* **前端逻辑**：监听发布按钮点击事件，获取输入框内容，通过 `XMLHttpRequest` 发送 POST 请求至后端。
* **后端处理**：PHP接收数据，写入 `posts` 表，返回新插入微博的 JSON 数据（包含ID、时间、内容）。
* **DOM操作**：前端接收成功响应后，使用 `insertBefore` 方法在列表顶部动态创建并插入新的微博节点，无需刷新页面。

### 3.2 无刷新点赞 (AJAX Like)
* **前端逻辑**：绑定点赞图标的点击事件。点击后立即切换图标样式（如变红），并通过 AJAX 通知后台。
* **后端处理**：PHP 检查 `likes` 表。若未点赞则插入记录并 `posts.likes_count + 1`；若已点赞则删除记录并 `-1`。
* **数据同步**：后端返回最新的点赞数值，前端JS更新页面上的数字显示。

### 3.3 实时字数统计 (JS Interaction)
* **前端逻辑**：监听输入框的 `input` 或 `keyup` 事件。
* **计算逻辑**：`剩余字数 = 140 - 当前输入长度`。
* **交互反馈**：当剩余字数 < 0 时，数字变红，并禁用“发布”按钮 (`disabled = true`)。

---

## 4. 目录结构规范

```text
/Project_Root
│  index.php            // 系统主页（微博流、发布框）
│  login.php            // 用户登录/注册页
│  admin.php            // 管理员后台（删帖、封号）
│  profile.php          // 个人中心（管理自己的内容）
│  search.php           // 搜索结果页
│
├─ /api                 // AJAX 请求处理接口
│     action_post.php   // 处理发布
│     action_like.php   // 处理点赞
│     action_comment.php// 处理评论
│
├─ /config              // 配置目录
│     db_connect.php    // 数据库连接配置
│
├─ /static              // 静态资源
│  ├─ css
│  │     style.css      // 全局样式
│  ├─ js
│  │     main.js        // UI交互逻辑
│  │     ajax_req.js    // AJAX封装函数
│  └─ img               // 图片资源
```