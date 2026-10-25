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
        Schema::connection('mysql')->create(ConfigTableName::$STORE_ITEM_PRICE_HISTORY, function (Blueprint $table) {
            $table->comment('商品價格變動歷史；僅在價格實際不同時新增');

            $table->bigIncrements('id')->comment('商品價格異動紀錄主鍵');
            $table->unsignedBigInteger('item_id')->nullable(false)->comment('發生價格異動的商品主鍵，關聯 item.item_id');
            $table->decimal('old_price', 12, 2)->nullable(false)->comment('價格異動前的售價');
            $table->decimal('new_price', 12, 2)->nullable(false)->comment('價格異動後的售價');
            $table->dateTime('detected_at', 6)->nullable(false)->comment('系統觀察到價格變動的時間，不代表店家實際調價時間');
            $table->dateTime('created_at', 6)->useCurrent()->comment('異動紀錄建立時間，統一保存 UTC');

            $table->index(['item_id', 'detected_at'], 'idx_item_price_history_item_detected');

            $table->foreign('item_id', 'fk_item_price_history_item')
                ->references('item_id')->on(ConfigTableName::$STORE_ITEM)
                ->onUpdate('restrict')->onDelete('cascade');
        });

        // Laravel Schema Builder 沒有 CHECK 方法；僅此處使用原生 SQL。
        DB::connection('mysql')->statement('ALTER TABLE ' . ConfigTableName::$STORE_ITEM_PRICE_HISTORY . ' ADD CONSTRAINT chk_item_price_history_old_price CHECK ( old_price >= 0 )');
        DB::connection('mysql')->statement('ALTER TABLE ' . ConfigTableName::$STORE_ITEM_PRICE_HISTORY . ' ADD CONSTRAINT chk_item_price_history_new_price CHECK ( new_price >= 0 )');
        DB::connection('mysql')->statement('ALTER TABLE ' . ConfigTableName::$STORE_ITEM_PRICE_HISTORY . ' ADD CONSTRAINT chk_item_price_changed CHECK ( old_price <> new_price )');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::connection('mysql')->dropIfExists(ConfigTableName::$STORE_ITEM_PRICE_HISTORY);
    }
};
