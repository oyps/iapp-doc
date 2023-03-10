<?php
require('./include/config.php');
require('./include/init.php');
/**
 * 管理员登录页面
 */
class Login
{
    /**是否已经登录 */
    public bool $has_login;
    public function __construct()
    {
        $action = $_POST['action'] ?? '';
        if ($action == 'login') {
            $password = $_POST['password'] ?? '';
        } else {
            $password = $_COOKIE['password'] ?? '';
        }
        $this->has_login = $password == Config::$admin['password'];
        setcookie('password', $password, time() + 3600 * 24 * 365, '/');
        if ($action == 'login') {
            header('location:login.php');
            die();
        }
    }
}

$login = new Login();
?>

<!DOCTYPE html>
<html lang="zh-CN">

<head>
    <?php require('./include/head.php') ?>
    <title>管理员 - <?php echo Config::$site_title ?></title>
    <script>
        const PAGE_NAME = 'login' // 页面标识
    </script>
</head>

<body>
    <?php require('./include/nav.php') ?>
    <div class="container">
        <nav>
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="./">主页</a></li>
                <li class="breadcrumb-item active">管理员</li>
            </ol>
        </nav>
        <?php if ($login->has_login) {
            echo '
        <div>
            <a class="btn btn-success me-2 mb-3" href="upload.php">上传手册</a>
            <a class="btn btn-primary me-2 mb-3" href="edit_book.php?action=add">新建手册</a>
            <a class="btn btn-warning me-2 mb-3" href="output.php">导出数据库</a>
            <a class="btn btn-danger mb-3" href="logout.php">退出登录</a>
        </div>';
        } else {
            echo '
        <div class="row">
            <div class="col-xxl-3 col-xl-4 col-lg-5 mx-auto">
                <div class="h4 text-center mb-3">管理员登录</div>
                <form method="POST">
                    <div class="input-group mb-3">
                        <input type="text" name="password" class="form-control border-primary" placeholder="请输入管理员密码" onfocus="this.type=\'password\'">
                        <input type="hidden" name="action" value="login">
                        <button class="btn btn-primary">登录</button>
                    </div>
                </form>
            </div>
        </div>';
        }
        ?>
    </div>
    <?php require('./include/footer.php') ?>
</body>

</html>