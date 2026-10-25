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
        Schema::connection('mysql')->create(ConfigTableName::$STORE, function (Blueprint $table) {
            $table->comment('平台分店資料');

            $table->bigIncrements('store_id')->comment('分店主鍵');
            $table->unsignedBigInteger('platform_id')->nullable(false)->comment('所屬平台主鍵，關聯 platform.platform_id');
            $table->string('store_name', 25)->nullable(false)->comment('分店名稱，用於商品顯示與分店篩選');
            $table->boolean('is_active')->nullable(false)->default(true)->comment('該分店資料目前是否有效');
            $table->dateTime('created_at', 6)->useCurrent()->comment('資料建立時間，統一保存 UTC');
            $table->dateTime('updated_at', 6)->useCurrent()->useCurrentOnUpdate()->comment('資料最後更新時間，統一保存 UTC');

            $table->index(['platform_id'], 'idx_store_platform_id');

            $table->index(['store_name'], 'idx_store_name');

            $table->foreign('platform_id', 'fk_store_platform')
                ->references('platform_id')->on(ConfigTableName::$PLATFORM)
                ->onUpdate('restrict')->onDelete('restrict');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::connection('mysql')->dropIfExists(ConfigTableName::$STORE);
    }
};
