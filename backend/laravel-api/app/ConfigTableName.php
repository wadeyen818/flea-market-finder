<?php

namespace App;

/**資料表名稱 */
class ConfigTableName
{
    /**跳蚤平台主檔 */
    public static $PLATFORM = "platform";

    /**標準縣市資料主檔 */
    public static $ADMINISTRATIVE_CITY = "administrative_city";

    /**標準鄉鎮市區資料主檔 */
    public static $ADMINISTRATIVE_DISTRICT = "administrative_district";

    /**跳蚤平台分店資料 */
    public static $STORE = "store";

    /**分店目前已知地址 */
    public static $STORE_ADDRESS = "store_address";

    /**商品目前狀態及來源資料 */
    public static $STORE_ITEM = "store_item";

    /**商品圖片 */
    public static $STORE_ITEM_IMAGE = "store_item_image";

    /**商品價格變動歷史 */
    public static $STORE_ITEM_PRICE_HISTORY = "store_item_price_history";

    /**爬蟲排程使用的搜尋關鍵字 */
    public static $SEARCH_KEYWORD = "search_keyword";

    /**商品與搜尋關鍵字的多對多關聯及匹配時間 */
    public static $STORE_ITEM_KEYWORD = "store_item_keyword";

    /**爬蟲每次執行的摘要紀錄 */
    public static $CRAWL_RUN = "crawl_run";
}
