<?php

use App\ConfigTableName;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::connection('mysql')->create(ConfigTableName::$STORE_ITEM, function (Blueprint $table) {
            $table->comment('商品目前狀態及來源資料');

            $table->bigIncrements('item_id')->comment('商品主鍵');
            $table->unsignedBigInteger('store_id')->nullable(false)->comment('商品所在分店主鍵，關聯 store.store_id');
            $table->string('external_item_id', 50)->nullable(true)->comment('來源平台提供的商品識別碼；來源未提供時允許為 NULL');
            $table->char('identity_hash', 64)->charset('ascii')->collation('ascii_bin')->nullable(false)->comment('商品穩定識別用 SHA-256，用於判斷是否為同一件商品');
            $table->char('current_content_hash', 64)->charset('ascii')->collation('ascii_bin')->nullable(true)->comment('商品目前內容的 SHA-256，用於判斷名稱、價格或狀態是否改變');
            $table->string('item_name', 100)->nullable(false)->comment('來源網站顯示的商品名稱');
            $table->decimal('current_price', 12, 2)->nullable(false)->comment('目前最後一次觀察到的商品售價');
            $table->decimal('original_price', 12, 2)->nullable(true)->comment('來源網站顯示的原價或劃線價格；未提供時為 NULL');
            $table->char('currency_code', 3)->nullable(false)->default('TWD')->comment('ISO 4217 幣別代碼，預設為新臺幣 TWD');
            $table->text('detail_url')->nullable(false)->comment('移除非必要追蹤參數後，仍可正常開啟的來源商品詳細頁網址');
            $table->dateTime('source_listed_at', 6)->nullable(true)->comment('來源網站提供的商品上架時間；未提供或無法確認時為 NULL');
            $table->dateTime('first_seen_at', 6)->nullable(false)->comment('系統第一次發現並收錄此商品的時間，作為跨平台日期查詢依據');
            $table->dateTime('last_seen_at', 6)->nullable(false)->comment('系統最近一次在搜尋結果或詳細頁看見此商品的時間');
            $table->dateTime('created_at', 6)->useCurrent()->comment('資料建立時間，統一保存 UTC');
            $table->dateTime('updated_at', 6)->useCurrent()->useCurrentOnUpdate()->comment('資料最後更新時間，統一保存 UTC');

            $table->unique(['identity_hash'], 'uk_item_identity_hash');

            $table->index(['store_id'], 'idx_item_store_id');

            $table->index(['first_seen_at', 'item_id'], 'idx_item_first_seen_item_id');

            $table->foreign('store_id', 'fk_item_store')
                ->references('store_id')->on(ConfigTableName::$STORE)
                ->onUpdate('restrict')->onDelete('restrict');
        });

        // Laravel Schema Builder 沒有 CHECK 方法；僅此處使用原生 SQL。
        DB::connection('mysql')->statement('ALTER TABLE ' . ConfigTableName::$STORE_ITEM . ' ADD CONSTRAINT chk_item_current_price CHECK ( current_price >= 0 )');
        DB::connection('mysql')->statement('ALTER TABLE ' . ConfigTableName::$STORE_ITEM . '  ADD CONSTRAINT chk_item_original_price CHECK ( original_price IS NULL OR original_price >= 0 )');
        DB::connection('mysql')->statement('ALTER TABLE ' . ConfigTableName::$STORE_ITEM . '  ADD CONSTRAINT chk_item_seen_time CHECK ( last_seen_at >= first_seen_at )');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::connection('mysql')->dropIfExists(ConfigTableName::$STORE_ITEM);
    }
};
