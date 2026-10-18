# Flea Market Finder

跨平台二手商品搜尋與追蹤平台。透過 Python 爬蟲收集商品資料，搭配後端 API 與網頁介面，協助使用者搜尋與查看商品；後續規劃特定商品追蹤、圖片保存及圖像辨識。

## 目前階段

目前已完成搜尋首頁、搜尋結果頁與商品卡片的 UI 初稿，並提出商品列表及商品資訊 API 契約草案。程式初始化、資料庫串接與各項功能的實作進度，將隨開發更新；下列規劃不代表功能已完成。

第一階段目標為 **MySQL → Laravel API → 原生 JavaScript 頁面**。先使用測試資料驗證搜尋、商品資訊及分頁，再逐步整合爬蟲與背景服務。

## 專案結構

採用單一 Git repository 管理多個獨立服務。以下為預定結構，目前只需建立正在開發的目錄。

```text
flea-market-finder/
├── backend/
│   ├── laravel-api/          # 第一階段 PHP API
│   └── spring-boot-api/      # 後續 Java API 實作
├── frontend/
│   ├── vanilla-js/           # HTML、CSS、原生 JavaScript
│   └── vue-app/              # 後續 Vue 前端
├── crawler/                 # Python 商品爬蟲
├── background-service/      # Java 背景處理
├── docs/                    # 架構、資料庫與 API 文件
├── .gitignore
└── README.md
```

各服務分別安裝依賴與啟動；IDE 可以只開啟對應子目錄。Git 由最外層統一管理，子目錄不另建 repository。

## 開發環境

| 項目 | 目前環境或規劃 |
| --- | --- |
| PHP | 本機 8.2.12 |
| Composer | 本機 2.10.3 |
| Laravel | 12.70.0 |
| 資料庫 | MySQL；實際版本與連線設定待建立環境時確認 |
| 第一階段前端 | HTML、CSS、原生 JavaScript Fetch API |
| 後續後端 | Java 17、Spring Boot，實際框架版本待確認 |
| 資料收集 | Python，依賴版本由爬蟲專案管理 |

## API 規劃

| 方法 | 路由 | 用途 |
| --- | --- | --- |
| GET | `/api/items` | 商品搜尋、縣市／門市篩選與分頁 |
| GET | `/api/items/{item_id}` | 單一商品資訊與圖片 |

列表草案參數為 `keyword`、`city_code`、`store_id`、`page`、`per_page`。API 外層採用 `{status, message, data}`；精確欄位、驗證及狀態值以後續確認的 API 文件為準。

搜尋 API 查詢已入庫資料，不在每次搜尋時即時啟動爬蟲。前端透過 HTTP API 取得資料，避免與特定後端框架耦合。Laravel 與原生 JS 版本會保留為作品，後續再增加 Spring Boot 與 Vue 實作。

## 本機啟動

目前 repository 尚無已驗證的完整啟動流程。以下是 **Laravel 應用程式建立後** 的預定步驟，並非建立 Laravel 專案的指令；實際流程須隨選定版本與程式碼更新。

1. 進入 `backend/laravel-api/`，執行 `composer install`。
2. 複製該目錄的 `.env.example` 為 `.env`，設定本機資料庫連線。
3. 執行 `php artisan key:generate`。
4. 確認 schema、migration 與測試資料準備完成，再依實際專案文件建立資料庫；不對既有資料庫直接執行破壞性重建。
5. 執行 `php artisan serve` 啟動本機 API。
6. 使用本機 HTTP 靜態伺服器提供 `frontend/vanilla-js/`，並設定前端的 API 基底網址。不同來源時，後端須允許實際前端來源的 CORS，或透過開發代理轉送。

前端伺服器工具與設定檔位置待實作時確定。請勿將資料庫帳密或其他後端機密放入瀏覽器端 JavaScript。

## 圖片保存規劃

來源圖片可能在商品售出或下架後被刪除，因此不能只保存圖片網址。

- 爬取發現新圖片時，即建立圖片下載工作，不等待使用者加入追蹤。
- 保留來源網址，另外記錄副本位置、下載狀態、成功時間與內容雜湊。
- 下載失敗可重試；商品下架時不連帶刪除已保存的圖片與紀錄。
- API 優先提供保存副本的顯示網址，尚未保存時使用來源網址；前端需提供破圖佔位。
- 圖片檔案及下載資料不提交 Git；保存、辨識與展示須考量來源條款和圖片權限。

此流程目前為設計需求，尚未表示已完成實作。

## 版本管理

- 提交原始碼、依賴清單與 lockfile、migration、schema、經整理的測試資料及安全的 `.env.example`。
- 不提交 `.env`、金鑰、依賴目錄、日誌、資料庫檔案及大量下載圖片。
- 保留 Laravel 等子專案原生 `.gitignore`，與根目錄規則共同使用。
- `.gitignore` 不會取消已被追蹤的檔案；首次提交前先檢查 `git status` 與暫存內容。

## 後續方向

- 完成 Laravel API、原生 JS 搜尋與詳情頁串接。
- 整合爬蟲入庫、圖片下載與背景工作。
- 加入 Spring Boot API 與 Vue 前端，驗證相同 API 契約。
- 依部署需求加入 Docker、持久儲存與 CI/CD；目前尚未設定部署流程。
