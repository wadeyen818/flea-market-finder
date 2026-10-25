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
        Schema::connection('mysql')->create(ConfigTableName::$PLATFORM, function (Blueprint $table) {
            $table->comment('跳蚤平台主檔');

            $table->bigIncrements('platform_id')->comment('平台主鍵');
            $table->string('platform_code', 50)->nullable(false)->comment('程式內使用的平台唯一代碼，例如 BBOBO');
            $table->string('platform_name', 25)->nullable(false)->comment('平台顯示名稱');
            $table->string('base_url', 255)->nullable(false)->comment('平台首頁或主要網域網址');
            $table->boolean('is_active')->nullable(false)->default(true)->comment('是否啟用該平台的排程與爬取功能');
            $table->dateTime('created_at', 6)->useCurrent()->comment('資料建立時間，統一保存 UTC');
            $table->dateTime('updated_at', 6)->useCurrent()->useCurrentOnUpdate()->comment('資料最後更新時間，統一保存 UTC');

            $table->unique(['platform_code'], 'uk_platform_code');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::connection('mysql')->dropIfExists(ConfigTableName::$PLATFORM);
    }
};
