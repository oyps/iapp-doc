<nav class="navbar navbar-expand-sm bg-light shadow-sm sticky-top mb-4">
    <div class="container-md">
        <a class="navbar-brand" href="#">iApp 手册网</a>
        <button class="navbar-toggler" data-bs-toggle="collapse" data-bs-target="#navbarSupportedContent">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarSupportedContent">
            <ul class="navbar-nav me-auto mb-2 mb-sm-0">
                <li class="nav-item">
                    <a class="nav-link nav-item-home" href="/">主页</a>
                </li>
                <li class="nav-item nav-item-about">
                    <a class="nav-link" href="/about.php">关于</a>
                </li>
                <li class="nav-item nav-item-upload">
                    <a class="nav-link" href="/control/upload.php">上传</a>
                </li>
            </ul>
            <div class="d-flex">
                <input class="form-control me-2" type="search" placeholder="请输入搜索关键词">
                <button class="btn btn-outline-success text-nowrap">搜索</button>
            </div>
        </div>
    </div>
</nav>
<script>
    // 控制导航条超链接聚焦
    if (typeof PAGE_NAME !== 'undefined') {
        let navEle = document.querySelector(`.nav-item-${PAGE_NAME} .nav-link`)
        if (navEle) navEle.classList.add('active')
    }
</script>