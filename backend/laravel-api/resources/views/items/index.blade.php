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

    .pagination {
        display: flex;
        align-items: center;
        justify-content: center;
        flex-wrap: wrap;
        gap: 8px;
        margin-top: 24px;
        margin-bottom: 32px;
    }

    #page-numbers {
        display: flex;
        gap: 8px;
    }

    .pagination button {
        min-width: 40px;
        min-height: 40px;
        padding: 8px 12px;
        border: 1px solid #CCDCD3;
        border-radius: 8px;
        background-color: #FFFFFF;
        color: #245C4F;
        font: inherit;
        cursor: pointer;
    }

    .pagination button:hover:not(:disabled) {
        background-color: #EDF3EE;
    }

    .pagination button:disabled {
        background-color: #F1F3F1;
        color: #929B95;
        cursor: default;
    }

    /* 目前所在頁碼 */
    .pagination button[aria-current="page"] {
        background-color: #245C4F;
        border-color: #245C4F;
        color: #FFFFFF;
    }

    #page-info {
        margin-left: 8px;
        color: #59675F;
        font-size: 14px;
    }

    /* dialog */
    .item-dialog {
        width: min(800px, calc(100% - 32px));
        max-height: 85vh;
        padding: 0;
        box-sizing: border-box;
        overflow-y: auto;
        border: none;
        border-radius: 16px;
        background: #FFFFFF;
        color: #26332D;
    }

    .item-dialog::backdrop {
        background: rgb(0 0 0 / 45%);
    }

    .item-dialog-header {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 16px;
        padding: 20px 24px;
        border-bottom: 1px solid #E1E8E2;
    }

    .item-dialog-header h2 {
        margin: 0;
        font-size: 22px;
        overflow-wrap: anywhere;
    }

    .dialog-close {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        flex-shrink: 0;
        width: 40px;
        height: 40px;
        padding: 0;
        border: none;
        border-radius: 8px;
        background: transparent;
        color: #59675F;
        font-size: 30px;
        cursor: pointer;
    }

    .dialog-close:hover {
        background: #EDF3EE;
    }

    .item-dialog-content {
        display: flex;
        align-items: flex-start;
        gap: 24px;
        padding: 24px;
    }

    .dialog-item-image {
        width: 45%;
        aspect-ratio: 1;
        object-fit: contain;
        border-radius: 12px;
        background: #F7F8F5;
    }

    .dialog-item-details {
        flex: 1;
        min-width: 0;
    }

    .dialog-item-price {
        margin: 0 0 24px;
        color: #B8501B;
        font-size: 28px;
        font-weight: bold;
    }

    .dialog-info {
        display: grid;
        grid-template-columns: auto minmax(0, 1fr);
        gap: 16px;
        margin: 0;
    }

    .dialog-info dt {
        color: #245C4F;
        font-weight: bold;
    }

    .dialog-info dd {
        margin: 0;
        overflow-wrap: anywhere;
    }

    /* 卡片上的眼睛按鈕 */
    .item-detail-button {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        padding: 8px;
        border: none;
        border-radius: 8px;
        background: transparent;
        cursor: pointer;
    }

    .item-detail-button:hover {
        background: #EDF3EE;
    }

    @media (max-width: 600px) {
        .item-dialog-content {
            flex-direction: column;
        }

        .dialog-item-image {
            width: 100%;
            max-height: 300px;
        }

        .dialog-item-details {
            width: 100%;
        }
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

        <nav class ="pagination" aria-label="商品分頁">
            <button type="button" id="prev-page" disabled>上一頁</button>
            <span id="page-numbers"></span>
            <button type="button" id="next-page" disabled>下一頁</button>

            <span id="page-info">尚未查詢</span>
        </nav>
    </div>

    <dialog id="item-dialog" class="item-dialog" aria-labelledby="dialog-item-name">
        <div class="item-dialog-header">
            <h2 id="dialog-item-name">商品資訊</h2>

            <button type="button" id="close-item-dialog" class="dialog-close" aria-label="關閉商品資訊">
                ×
            </button>
        </div>

        <div class="item-dialog-content">
            <img id="dialog-item-image" class="dialog-item-image" alt="">

            <div class="dialog-item-details">
                <p id="dialog-item-price" class="dialog-item-price"></p>

                <dl class="dialog-info">
                    <dt>平台</dt>
                    <dd id="dialog-item-platform"></dd>

                    <dt>門市</dt>
                    <dd id="dialog-item-store"></dd>

                    <dt>地址</dt>
                    <dd id="dialog-item-address"></dd>
                </dl>
            </div>
        </div>
    </dialog>

    <script>
        const searchButton = document.getElementById("search-button");
        const prevButton = document.getElementById("prev-page");
        const nextButton = document.getElementById("next-page");

        // 保存本次已送出的搜尋條件
        let currentKeyword = "";
        let currentPage = 1;

        searchButton.addEventListener("click", function() {
            currentKeyword = document.getElementById("keyword").value.trim();

            // 每次重新搜尋都回到第一頁
            loadItems(1);
        });

        //分頁按鈕實作
        prevButton.addEventListener("click", function() {
            loadItems(currentPage - 1);
        });
        nextButton.addEventListener("click", function() {
            loadItems(currentPage + 1);
        });

        function loadItems(page = 1) {
            const params = {
                per_page: 2,
                page: page
            };
            if (currentKeyword !== "") {
                params.search_key_word = currentKeyword
                console.log(params.search_key_word);
            }

            return axios.get("/api/items", {
                    params
                })
                .then(function(response) {
                    const body = response.data;
                    console.log(body);
                    if (body.status !== "success") {
                        throw new Error(body.message || "取得商品列表失敗");
                    }

                    // 取得商品資料
                    const items = body.data.data;
                    const itemsList = document.getElementById("items-list");

                    // 取得分頁資料
                    const pagination = body.data;
                    const prevButton = document.getElementById("prev-page");
                    const nextButton = document.getElementById("next-page");
                    const pageInfo = document.getElementById("page-info");

                    // API 成功回應後，才更新目前頁碼
                    currentPage = pagination.current_page;

                    // 第一頁時，停用上一頁
                    prevButton.disabled = pagination.current_page <= 1;

                    // 最後一頁時，停用下一頁
                    nextButton.disabled = pagination.current_page >= pagination.last_page;

                    pageInfo.textContent = pagination.total === 0 ? "查無商品" :
                        `第 ${pagination.current_page}／${pagination.last_page} 頁`;

                    renderPageNumbers(pagination);

                    // 清空商品
                    itemsList.replaceChildren();

                    items.forEach(item => {
                        // 建立一張新的卡片
                        const card = document.createElement("article");
                        card.classList.add("item-card");

                        // 建立圖片區，包含圖片元素
                        const itemImage = document.createElement("img");
                        itemImage.classList.add("item-image");

                        // 處理無圖片的情形
                        if (item.source_image_url) {
                            itemImage.src = item.source_image_url;
                        } else {
                            itemImage.src = "/images/items/test-image.png";
                            itemImage.alt = "暫無商品圖片";
                        }

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

                        // eye icon button
                        const detailButton = document.createElement("button");
                        detailButton.type = "button";
                        detailButton.classList.add("item-detail-button");
                        detailButton.setAttribute(
                            "aria-label",
                            `查看商品資訊：${item.item_name}`
                        );

                        detailButton.appendChild(eyeIcon);

                        detailButton.addEventListener("click", function() {
                            openItemDialog(item);
                        });

                        // 填入這筆商品的資料
                        title.textContent = item.item_name;
                        store.textContent = `${item.platform_name} | ${item.store_name}`;
                        price.textContent =
                            `NT$${Number(item.current_price).toLocaleString("zh-TW")}`;

                        // 把這些元素加入文字區
                        itemContent.appendChild(title);
                        storeRow.appendChild(storeIcon);
                        storeRow.appendChild(store);
                        itemContent.appendChild(storeRow);
                        priceRow.appendChild(price);
                        priceRow.appendChild(detailButton);
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
        }

        function renderPageNumbers(pagination) {
            const pageNumbers = document.getElementById("page-numbers");

            pageNumbers.replaceChildren();

            if (pagination.total === 0) {
                return;
            }

            for (let page = 1; page <= pagination.last_page; page++) {
                const button = document.createElement("button");
                button.type = "button";
                button.textContent = page;

                if (page === pagination.current_page) {
                    button.disabled = true;
                    button.setAttribute("aria-current", "page");
                }

                button.addEventListener("click", function() {
                    loadItems(page);
                });

                pageNumbers.appendChild(button);
            }
        }

        const itemDialog = document.getElementById("item-dialog");
        const closeItemDialog = document.getElementById("close-item-dialog");

        closeItemDialog.addEventListener("click", function() {
            itemDialog.close();
        });

        function openItemDialog(item) {
            document.getElementById("dialog-item-name").textContent =
                item.item_name;

            document.getElementById("dialog-item-price").textContent =
                `NT$${Number(item.current_price).toLocaleString("zh-TW")}`;

            document.getElementById("dialog-item-platform").textContent =
                item.platform_name;

            document.getElementById("dialog-item-store").textContent =
                item.store_name;

            document.getElementById("dialog-item-address").textContent =
                item.source_address_text || "尚無地址資料";

            const image = document.getElementById("dialog-item-image");
            image.src =
                item.source_image_url || "/images/items/test-image.png";
            image.alt = item.item_name;

            itemDialog.showModal();
        }
    </script>

</body>

</html>
