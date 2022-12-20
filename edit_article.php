<?php
require('./config.php');
require('./init.php');
require('./public_fun.php');
/**
 * 编辑文章
 * 
 * 新增文章时，需传入 `action=edit`
 */
class Edit_article extends Public_fun
{
    /**
     * 是否是新增文章模式
     */
    public bool $mode_add;
    /**
     * 是否是编辑文章模式
     */
    public bool $mode_edit;
    /**
     * 状态文本，编辑/新增
     */
    public string $status_text;
    public function __construct()
    {
        $action = $_GET['action'] ?? '';
        $submit = $_POST['submit'] ?? '';
        if ($action == 'edit') {
            $this->mode_add = false;
            $this->mode_edit = true;
            $this->get_article_id();
            $this->status_text = '编辑';
            $this->get_article_info();
            if ($submit) {
                $this->get_title();
                $this->get_content();
                $this->update();
                $this->update_book_info();
                header('location: ./article.php?article_id=' . $this->article_id);
            }
        } else {
            $this->mode_add = true;
            $this->mode_edit = false;
            $this->get_book_id();
            $this->status_text = '新增';
            if ($submit) {
                $this->get_title();
                $this->get_content();
                $this->create_article();
                $this->update_book_info();
                $this->article_id = $this->get_new_article_id();
                header('location: ./article.php?article_id=' . $this->article_id);
            }
        }
    }
    /**
     * 更新手册的更新时间
     */
    public function update_book_info()
    {
        $table = Config::$table['book'];
        $sql = "UPDATE `$table` SET `update_time` = CURRENT_TIMESTAMP WHERE `id` = {$this->book_id};";
        mysqli_query(Init::$conn, $sql);
    }
    /**
     * 获取最新一条文章记录的 ID 值
     */
    public function get_new_article_id()
    {
        $table = Config::$table['article'];
        $sql = "SELECT `id` FROM `$table` ORDER BY `id` DESC LIMIT 1;";
        $result = mysqli_query(Init::$conn, $sql);
        $id = mysqli_fetch_assoc($result)['id'];
        return $id;
    }
    /**
     * 创建文章
     */
    public function create_article()
    {
        $table = Config::$table['article'];
        $title = $this->title;
        $content = $this->content;
        $book_id = $this->book_id;
        $sql = "INSERT INTO `$table` (`title`, `content`, `book_id`) VALUES ('$title', '$content', '$book_id');";
        mysqli_query(Init::$conn, $sql);
    }
    /**
     * 更新文章数据
     */
    public function update()
    {
        $table = Config::$table['article'];
        $sql = "UPDATE `$table` SET `title` = '{$this->title}', `content` = '{$this->content}', `update_time` = CURRENT_TIMESTAMP WHERE `id` = {$this->article_id};";
        mysqli_query(Init::$conn, $sql);
    }
}

$edit_article = new Edit_article();
?>
<!DOCTYPE html>
<html lang="zh-CN">

<head>
    <?php require('./head.php') ?>
    <title><?php echo $edit_article->status_text ?>文章 - iApp 手册网</title>
    <script>
        const PAGE_NAME = 'edit_book' // 页面标识
    </script>
</head>

<body>
    <?php require('./nav.php') ?>
    <div class="container">
        <form method="POST">
            <div class="h4 mb-3"><?php echo $edit_article->status_text ?>文章</div>
            <div class="mb-3">
                <label for="articleTitle" class="form-label">文章标题（250字以内）</label>
                <input type="text" class="form-control" name="title" id="articleTitle" placeholder="请输入文章标题" value="<?php echo $edit_article->mode_edit ? $edit_article->article_title : '' ?>" required>
            </div>
            <div class="mb-3">
                <label for="articleContent" class="form-label">文章内容（Markdown 格式）</label>
                <textarea class="form-control" name="content" rows="15" id="articleContent" placeholder="" required><?php echo $edit_article->mode_edit ? $edit_article->article_info['content'] : '' ?></textarea>
            </div>
            <input type="hidden" name="submit" value="1">
            <button class="btn btn-success">提交修改</button>
        </form>
    </div>
    <script>
        let contentEle = document.getElementById('articleContent')
        contentEle.placeholder = '请输入文章内容\n文档严格遵循 Markdown 语法'
    </script>
    <?php require('./footer.php') ?>
</body>

</html>