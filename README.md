# iApp-Doc

> iApp开发文档

## 项目信息

- 作者：欧阳鹏
- 开发日期：2022年12月19日
- 官网：https://apee.top
- 关键词：PHP、Bootstrap、伪静态、Cookie、文件上传

## 功能规划

- 代码搜索（标题、全文）
- 文章可编辑，手册可增加文章
- Markdown 默认支持
- 控制台：导入手册、删除手册

## 数据库设计

### 手册（iapp_doc_book）

| 字段名      | 类型     | 描述     |
| ----------- | -------- | -------- |
| id          | int      | 手册ID   |
| create_time | datetime | 创建时间 |
| update_time | datetime | 更新时间 |
| title       | varchar  | 手册标题 |
| intro       | varchar  | 手册介绍 |

### 文章（iapp_doc_article）

| 字段名      | 类型     | 描述     |
| ----------- | -------- | -------- |
| id          | int      | 文章ID   |
| title       | varchar  | 文章标题 |
| content     | text     | 正文内容 |
| update_time | datetime | 更新时间 |
| book_id     | int      | 手册ID   |

## 路由规划

| 伪静态后URL      | 原URL                                | 描述       |
| ---------------- | ------------------------------------ | ---------- |
| /                | /                                    | 主页       |
| /book/3          | /book.php?book_id=3                  | 某手册主页 |
| /article/123     | /article.php?article_id=123          | 某文章页面 |
| /search/all/1234 | /search.php?book_id=all&keyword=1234 | 搜索       |
| /control/upload  | /control/upload.php                  | 上传手册   |

## 待优化

- `document.title` 统一化
- `require` 统一化，`location` 重定向适配伪静态
- 补全关于页的项目简介