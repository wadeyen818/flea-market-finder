<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>跳蚤商品搜尋</title>
</head>
<style>
    body {
        margin: 0;
        background-color: #F7F8F5;
        color: #26332D;
        font-family: "Noto Sans TC", "PingFang TC",
            "Microsoft JhengHei", sans-serif;
        line-height: 1.6;
    }

    input,
    button,
    select {
        font: inherit;
    }

    .site-header {
        background-color: #245C4F;
        color: #FFFFFF;
        display: flex;
        justify-content: space-between;
        border: 1px solid #777;
        padding: 20px;
        align-items: center;
    }

    .search-bar {
        background-color: #EDF3EE;
        display: flex;
        align-items: center;
        padding: 20px;
        gap: 12px;
        border: 1px solid #ddd;
        border-radius: 12px;
    }

    .search-bar input,
    .search-bar button {
        box-sizing: border-box;
        height: 44px;
        border-radius: 8px;
    }

    .search-bar input {
        background-color: #FFFFFF;
        color: #26332D;
        border: 1px solid #CDD9D0;
        padding: 0 12px;
        flex: 1;
        min-width: 0;
    }

    .search-bar button {
        background-color: #245C4F;
        color: #FFFFFF;
        border: 1px solid #245C4F;
        padding: 0 20px;
        cursor: pointer;
        transition: background-color 0.2s;
    }

    .search-bar button:hover {
        /* 滑過時加深 */
        background-color: #194538;
    }

    .search-bar input::placeholder {
        color: #6B7870;
        opacity: 1;
    }

    .site-header a {
        color: #FFFFFF;
        text-decoration: none;
    }

    .site-header a:hover {
        text-decoration: underline;
    }

    .inner {
        margin: 0 auto;
        width: 90%;
        max-width: 1000px;
    }
</style>

<body>
    <header class="site-header">
        <span>跳蚤商品搜尋平台</span>
        <nav>
            <a href="/items">首頁</a>
        </nav>
    </header>
    <div class="inner">
        <p>找找附近的二手好物</p>
        <div class="search-bar">
            <label for="keyword">關鍵字</label>
            <input id="keyword" type="text" placeholder="請輸入">
            <button>搜尋</button>
        </div>
    </div>
</body>

<script type="text/javascript">
    // alert("Hello");
</script>

</html>
