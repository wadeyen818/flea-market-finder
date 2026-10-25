<?php

use App\ConfigTableName;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::connection('mysql')->create(ConfigTableName::$SEARCH_KEYWORD, function (Blueprint $table) {
            $table->comment('爬蟲排程使用的搜尋關鍵字');

            $table->bigIncrements('search_keyword_id')->comment('搜尋關鍵字主鍵');
            $table->string('keyword', 20)->nullable(false)->comment('爬蟲實際送往來源平台的搜尋關鍵字');
            $table->boolean('is_active')->nullable(false)->default(true)->comment('是否啟用此關鍵字的爬蟲排程');
            $table->dateTime('created_at', 6)->useCurrent()->comment('資料建立時間，統一保存 UTC');
            $table->dateTime('updated_at', 6)->useCurrent()->useCurrentOnUpdate()->comment('資料最後更新時間，統一保存 UTC');

            $table->unique(['keyword'], 'uk_search_keyword_keyword');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::connection('mysql')->dropIfExists(ConfigTableName::$SEARCH_KEYWORD);
    }
};
