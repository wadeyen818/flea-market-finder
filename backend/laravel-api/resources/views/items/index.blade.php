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

    .items-list {
        margin-top: 24px;
        display: grid;
        grid-template-columns: repeat(3, minmax(0, 1fr));
        gap: 16px;
    }

    @media (max-width: 800px) {
        .items-list {
            grid-template-columns: repeat(2, minmax(0, 1fr));
        }
    }

    @media (max-width: 540px) {
        .items-list {
            grid-template-columns: 1fr;
        }
    }

    .item-card {
        background-color: #FFFFFF;
        border: 1px solid #CDD9D0;
        border-radius: 12px;
        overflow: hidden;
    }

    .item-content {
        padding: 20px;
    }

    .item-image {
        display: block;
        width: 100%;
        height: 220px;
        object-fit: contain;
        background-color: #EDF3EE;
    }

    .item-title {
        margin: 0 0 12px;
        font-size: 20px;
    }

    .item-store-row {
        display: flex;
        align-items: center;
        gap: 6px;
    }

    .item-store {
        margin: 0;
        color: #6B7870;
        font-size: 14px;
    }

    .item-price-row {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-top: 24px;
    }

    .item-price {
        margin: 0;
        color: #A94F24;
        font-size: 22px;
        font-weight: 700;
    }

    .item-icon {
        width: 20px;
        height: 20px;
        object-fit: contain;
        flex-shrink: 0;
    }
</style>
<script src="https://cdn.jsdelivr.net/npm/axios/dist/axios.min.js"></script>

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
            <button id="search-button" type="button">搜尋</button>
        </div>
        <div id="items-list" class="items-list"></div>
    </div>

    <script>
        const searchButton = document.getElementById("search-button");
        searchButton.addEventListener("click", function() {
            axios.get("/api/items")
                .then(function(response) {
                    const body = response.data;
                    if (body.status !== "success") {
                        throw new Error(body.message || "取得商品列表失敗");
                    }

                    // 取得商品資料
                    const items = body.data;
                    const itemsList = document.getElementById("items-list");

                    // 清空商品
                    itemsList.replaceChildren();

                    items.forEach(item => {
                        // 建立一張新的卡片
                        const card = document.createElement("article");
                        card.classList.add("item-card");

                        // 建立圖片區，包含圖片元素
                        const itemImage = document.createElement("img");
                        itemImage.classList.add("item-image");
                        itemImage.src = item.image_url;
                        itemImage.alt = item.title;

                        // 建立文字區，包含名稱、價格、門市元素
                        const itemContent = document.createElement("div");
                        itemContent.classList.add("item-content");
                        const title = document.createElement("h2");
                        title.classList.add("item-title");
                        const storeRow = document.createElement("div");
                        storeRow.classList.add("item-store-row");
                        const store = document.createElement("p");
                        const storeIcon = document.createElement("img");
                        storeIcon.src = "/images/icons/location.png";
                        storeIcon.alt = "";
                        const priceRow = document.createElement("div");
                        priceRow.classList.add("item-price-row");
                        const price = document.createElement("p");
                        const eyeIcon = document.createElement("img");
                        eyeIcon.src = "/images/icons/eye.png";
                        eyeIcon.alt = "";

                        store.classList.add("item-store");
                        price.classList.add("item-price");
                        storeIcon.classList.add("item-icon");
                        eyeIcon.classList.add("item-icon");

                        // 填入這筆商品的資料
                        title.textContent = item.title;
                        store.textContent = `門市：${item.store_name}`;
                        price.textContent = `NT$${item.price}`;

                        // 把這些元素加入文字區
                        itemContent.appendChild(title);
                        storeRow.appendChild(storeIcon);
                        storeRow.appendChild(store);
                        itemContent.appendChild(storeRow);
                        priceRow.appendChild(price);
                        priceRow.appendChild(eyeIcon);
                        itemContent.appendChild(priceRow);

                        // 把圖片區與文字區加入卡片
                        card.appendChild(itemImage);
                        card.appendChild(itemContent);

                        // 把卡片加入列表
                        itemsList.appendChild(card);
                    });
                })
                .catch(function(error) {
                    if (error.response) {
                        console.error("Server responded with:", error.response.status);
                    } else {
                        console.error("Error:", error.message);
                    }
                });
        });
    </script>

</body>

</html>
