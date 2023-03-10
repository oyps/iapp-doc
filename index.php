<?php
require('./include/config.php');
require('./include/init.php');
require('./include/public_fun.php');
/**
 * 主页
 */
class Index extends Public_fun
{
    /**
     * 手册列表
     */
    public array $book_list;
    public function __construct()
    {
        $this->get_book_list();
    }
    public function get_book_list()
    {
        $table = Config::$table['book'];
        $sql = "SELECT * FROM `$table` ORDER BY UNIX_TIMESTAMP(`update_time`) DESC, `id`";
        $result = mysqli_query(Init::$conn, $sql);
        $list = mysqli_fetch_all($result, MYSQLI_ASSOC);
        $this->book_list = $list;
    }
}
$index = new Index();
?>
<!DOCTYPE html>
<html lang="zh-CN">

<head>
    <?php require('./include/head.php') ?>
    <title><?php echo Config::$site_title ?> - 鹏优创</title>
    <meta name="description" content="<?php echo Config::$site_title ?>，<?php echo Config::$description ?>">
    <meta name="og:description" content="<?php echo Config::$site_title ?>，<?php echo Config::$description ?>">
    <script>
        const PAGE_NAME = 'home' // 页面标识
    </script>
    <style>
        .bookListItem {
            border-left: 5px solid var(--bs-secondary);
            border-bottom: 3px solid var(--bs-secondary);
        }
    </style>
</head>

<body>
    <?php require('./include/nav.php') ?>
    <div class="container">
        <div class="row mb-3">
            <?php
            foreach ($index->book_list as $book_info) {
                echo '
            <div class="col-xxl-3 col-xl-4 col-lg-4 col-md-6 mb-3">
                <a title="' . strip_tags($book_info['title']) . '"
                    class="bookListItem justify-content-between card card-body shadow-sm h-100"
                    href="' . $index->get_book_url($book_info['id']) . '" role="button">
                    <div class="text-center"><img src="icon/readme.svg" width="55" class="mb-2"></div>
                    <div class="h5 text-truncate mb-3 text-center">' . $book_info['title'] . '</div>
                    <div class="mb-2 limit-line-4 text-muted text-justify">'
                    . strip_tags(mb_substr($book_info['intro'], 0, 80)) . '</div>
                    <div class="text-muted small text-center">' . $book_info['update_time'] . ' 最后更新</div>
                </a>
            </div>';
            }
            ?>
        </div>
    </div>
    <?php require('./include/footer.php') ?>
</body>

</html>
