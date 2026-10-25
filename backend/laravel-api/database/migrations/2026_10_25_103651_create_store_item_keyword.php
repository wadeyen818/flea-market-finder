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
        Schema::connection('mysql')->create(ConfigTableName::$STORE_ITEM_KEYWORD, function (Blueprint $table) {
            $table->comment('商品與搜尋關鍵字的多對多關聯及匹配時間');

            $table->unsignedBigInteger('item_id')->comment('商品主鍵，關聯 item.item_id');
            $table->unsignedBigInteger('search_keyword_id')->comment('搜尋關鍵字主鍵，關聯 search_keyword.search_keyword_id');
            $table->dateTime('first_matched_at', 6)->nullable(false)->comment('此商品第一次被該關鍵字搜尋到的時間');
            $table->dateTime('last_matched_at', 6)->nullable(false)->comment('此商品最近一次被該關鍵字搜尋到的時間');

            $table->primary(['item_id', 'search_keyword_id']);

            $table->index(['search_keyword_id', 'first_matched_at', 'item_id'], 'idx_item_keyword_keyword_time_item');

            $table->foreign('item_id', 'fk_item_keyword_item')
                ->references('item_id')->on(ConfigTableName::$STORE_ITEM)
                ->onUpdate('restrict')->onDelete('cascade');

            $table->foreign('search_keyword_id', 'fk_item_keyword_search_keyword')
                ->references('search_keyword_id')->on(ConfigTableName::$SEARCH_KEYWORD)
                ->onUpdate('restrict')->onDelete('restrict');
        });

        // Laravel Schema Builder 沒有 CHECK 方法；僅此處使用原生 SQL。
        DB::connection('mysql')->statement('ALTER TABLE ' . ConfigTableName::$STORE_ITEM_KEYWORD . ' ADD CONSTRAINT chk_item_keyword_matched_time CHECK ( last_matched_at >= first_matched_at )');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::connection('mysql')->dropIfExists(ConfigTableName::$STORE_ITEM_KEYWORD);
    }
};
