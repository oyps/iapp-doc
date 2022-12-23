<?php
require('./include/config.php');
require('./include/init.php');
require('./include/public_fun.php');
/**
 * 文章页面
 */
class Article extends Public_fun
{
    public function __construct()
    {
        $this->get_article_id();
        $this->get_article_info();
        $this->get_book_info();
        $this->if_has_login();
    }
}
$article = new Article();
function parse_content($text)
{
    $text = str_replace("\n", '', htmlspecialchars(strip_tags(mb_substr($text, 0, 200))));
    // 隐藏 Markdown 字符
    $text = preg_replace('/[#[\]!<>*`-]/', '', $text);
    return $text;
}

?>
<!DOCTYPE html>
<html lang="zh-CN">

<head>
    <?php require('./include/head.php') ?>
    <title><?php echo $article->parse_value($article->article_title) ?> - <?php echo $article->parse_value($article->book_info['title']) ?> - <?php echo Config::$site_title ?></title>
    <script src="https://cdn.staticfile.org/marked/4.2.4/marked.min.js"></script>
    <?php $sub_content = parse_content($article->article_info['content']) ?>
    <meta name="description" content="<?php echo $article->parse_value($article->article_title) ?> <?php echo $sub_content ?> | <?php echo $article->parse_value($article->book_info['title']) ?>">
    <meta name="keywords" content="<?php echo $article->parse_value($article->article_title) ?>, <?php echo $article->parse_value($article->book_info['title']) ?>">
    <link rel="stylesheet" href="css/prism-default.css">
    <link rel="stylesheet" href="css/article.css">
    <script>
        const PAGE_NAME = 'book' // 页面标识
    </script>
</head>

<body>
    <?php require('./include/nav.php') ?>
    <div class="container">
        <nav class="mb-4">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="./" class="text-decoration-none">主页</a></li>
                <?php
                $book_url = Config::$site_path . '/book.php?book_id=' . $article->book_info['id'];
                $book_url_static = Config::$site_path . '/book_' . $article->book_info['id'] . '.html';
                $url = Config::$url_static ? $book_url_static : $book_url;
                ?>
                <li class="breadcrumb-item"><a href="<?php echo $url ?>" class="text-decoration-none"><?php echo strip_tags($article->book_info['title']) ?></a></li>
                <li class="breadcrumb-item active"><?php echo strip_tags($article->article_title) ?></li>
            </ol>
        </nav>
        <div class="h2 mb-3 fw-bold"><?php echo $article->article_title ?></div>
        <div class="text-muted"><?php echo $article->article_info['update_time'] ?> 最后更新</div>
        <hr>
        <div id="content" class="mb-3">
            <?php
            
            ?>
        </div>
        <?php
        if ($article->has_login) {
            echo '
        <div class="pb-3 sticky-bottom">
            <a class="btn btn-primary btn-sm me-2" href="edit_article.php?action=edit&article_id=' . $article->article_info['id'] . '">编辑文章</a>
            <a class="btn btn-danger btn-sm" onclick="delete_article(' . $article->book_info['id'] . ', ' . $article->article_info['id'] . ')">删除文章</a>
        </div>
        <script>
            function delete_article(id) {
                let url = "' . 'delete_article.php?book_id=' . $article->book_info['id'] . '&article_id=' . $article->article_info['id'] . '";
                if (confirm(\'确定要删除该文章？\')) {
                    location.href = url
                }
            }
        </script>';
        }
        ?>
    </div>
    <script>
        <?php
        function parse_print($text)
        {
            return json_encode(['text' => $text], JSON_UNESCAPED_UNICODE);
        }
        ?>
        // let data = <?php echo parse_print($article->article_info['content']) . PHP_EOL ?>
        // document.getElementById('content').innerHTML = marked.parse(data.text);
    </script>
    <script src="js/prism.js"></script>
    <script>
        // 手动触发
        Prism.highlightAll()
        let codeEles = document.querySelectorAll('pre code')
        if (codeEles) {
            codeEles.forEach(ele => {
                ele.contentEditable = true
            })
        }
        let tableEles = document.querySelectorAll('#content table')
        if (tableEles) {
            tableEles.forEach(ele => {
                ele.classList.add('table')
                ele.classList.add('table-bordered')
                ele.style.width = 'auto'
                ele.style.maxWidth = '100%'
            })
        }
    </script>
    <?php require('./include/footer.php') ?>
</body>

</html>