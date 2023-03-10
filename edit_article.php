<?php
require('./include/config.php');
require('./include/init.php');
require('./include/public_fun.php');
/**
 * 编辑文章
 * 
 * 新增文章时，需传入 `action=edit`
 */
class Edit_article extends Public_fun
{
    /**
     * 手册列表
     */
    public array $book_list;
    public function __construct()
    {
        $this->must_login();
        $this->get_book_list();
        $action = $_GET['action'] ?? '';
        $submit = $_POST['submit'] ?? '';
        if ($action == 'edit') {
            $this->mode_add = false;
            $this->mode_edit = true;
            $this->get_article_id();
            $this->status_text = '编辑';
            $this->get_article_info();
            // 更新原手册的更新时间
            $this->update_book_info();
            if ($submit) {
                $old_book_id = $this->book_id;
                $this->get_new_book_id();
                $this->get_title();
                $this->get_content();
                $this->update();
                // 更新新手册的更新时间
                if ($old_book_id != $this->book_id) {
                    // 文章所属的手册有变化
                    $this->update_book_info();
                }
                header('location:' . $this->get_article_url());
            }
        } else {
            $this->mode_add = true;
            $this->mode_edit = false;
            $this->status_text = '新增';
            $this->get_book_id();
            if ($submit) {
                $this->get_new_book_id();
                $this->get_title();
                $this->get_content();
                $this->create_article();
                $this->update_book_info();
                $this->article_id = $this->get_new_article_id();
                header('location:' . $this->get_article_url());
            }
        }
    }

    /**
     * 获取手册列表
     */
    public function get_book_list()
    {
        $table = Config::$table['book'];
        $sql = "SELECT * FROM `$table`";
        $result = mysqli_query(Init::$conn, $sql);
        $data = mysqli_fetch_all($result, MYSQLI_ASSOC);
        $this->book_list = $data;
    }

    /**
     * 获取 POST 参数 new_book_id
     */
    public function get_new_book_id()
    {
        $book_id = $_POST['new_book_id'] ?? '';
        if ($book_id == '') {
            die('手册ID不能为空');
        }
        $this->book_id = $book_id;
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
        $sql = "UPDATE `$table` SET `book_id` = '{$this->book_id}', `title` = '{$this->title}', `content` = '{$this->content}', `update_time` = CURRENT_TIMESTAMP WHERE `id` = {$this->article_id};";
        mysqli_query(Init::$conn, $sql);
    }
}

$edit_article = new Edit_article();
?>
<!DOCTYPE html>
<html lang="zh-CN">

<head>
    <?php require('./include/head.php') ?>
    <title><?php echo $edit_article->status_text ?>文章 - <?php echo Config::$site_title ?></title>
    <script>
        const PAGE_NAME = 'edit_book' // 页面标识
    </script>
</head>

<body>
    <?php require('./include/nav.php') ?>
    <div class="container">
        <nav>
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="./">主页</a></li>
                <li class="breadcrumb-item">
                    <a href="<?php echo $edit_article->mode_edit ?
                                    $edit_article->get_article_url() :
                                    $edit_article->get_book_url()
                                ?>">
                        <?php echo $edit_article->mode_edit ? '返回文章' : '返回手册' ?>
                    </a>
                </li>
                <li class="breadcrumb-item active"><?php echo $edit_article->status_text ?>文章</li>
            </ol>
        </nav>
        <form method="POST" id="form">
            <div class="h4 mb-3"><?php echo $edit_article->status_text ?>文章</div>
            <div class="mb-3">
                <label for="articleTitle" class="form-label">文章标题（250字以内）</label>
                <input type="text" class="form-control" name="title" id="articleTitle" placeholder="请输入文章标题" value="<?php echo $edit_article->mode_edit ?
                                                                                                                        htmlspecialchars($edit_article->article_title) :
                                                                                                                        '' ?>" required>
            </div>
            <div class="mb-3">
                <label class="form-label">文章内容（Markdown 格式）</label>
                <div id="editor" style="height: 500px;" class="border shadow-sm border rounded"></div>
            </div>
            <div class="mb-3">
                <label for="bookId" class="form-label">文章所属手册</label>
                <select class="form-select" name="new_book_id" id="bookId">
                    <?php
                    foreach ($edit_article->book_list as $book_info) {
                        $seleted = $book_info['id'] == $edit_article->book_id;
                        echo '
                        <option value="' . $book_info['id'] . '" ' . ($seleted ? 'selected' : '') . '>' .
                            strip_tags($book_info['title']) . '
                        </option>';
                    }
                    ?>
                </select>
            </div>
            <input type="hidden" name="submit" value="1">
            <input type="hidden" name="content" id="articleContent">
            <!-- <input type="submit" id="submit" style="display: none;"> -->
            <button class="btn btn-success mb-3" id="submitChange">提交修改</button>
        </form>
    </div>
    <script>
        // Ctrl + S 保存
        window.addEventListener('keydown', function(event) {
            if (event.ctrlKey && event.keyCode == 83) {
                event.preventDefault()
                document.getElementById('submitChange').click()
            }
        })
    </script>
    <?php require('./include/footer.php') ?>
    <script src="https://cdn.staticfile.org/ace/1.14.0/ace.min.js"></script>
    <script src="https://cdn.staticfile.org/ace/1.14.0/mode-markdown.min.js"></script>
    <script src="https://cdn.staticfile.org/ace/1.14.0/snippets/markdown.min.js"></script>
    <script>
        <?php
        function parse_print($text)
        {
            return json_encode(['text' => $text], JSON_UNESCAPED_UNICODE);
        }
        ?>
        let data = <?php echo $edit_article->mode_edit ? parse_print($edit_article->article_info['content']) : 'null' ?>;
        ace.config.set('basePath', 'https://cdn.staticfile.org/ace/1.14.0/')
        var editor = ace.edit('editor')
        // editor.setTheme("ace/theme/tomorrow");
        if (window.innerWidth > 576) {
            editor.setFontSize(20)
        } else {
            editor.setFontSize(15)
        }
        const mdMode = ace.require("ace/mode/markdown").Mode;
        editor.session.setMode(new mdMode())
        if (data) {
            editor.setValue(data.text)
        }
        editor.gotoLine(1)
        editor.setShowPrintMargin(false)
        editor.session.setUseWrapMode(true)
        editor.session.setTabSize(2)
        let ContentEle = document.getElementById('articleContent')
        let formEle = document.getElementById('form')
        let submitEle = document.getElementById('submit')
        // 初始化 input 属性值
        ContentEle.value = data ? data.text : ''
        editor.session.on('change', function(delta) {
            ContentEle.value = editor.getValue()
        })
    </script>
</body>

</html>