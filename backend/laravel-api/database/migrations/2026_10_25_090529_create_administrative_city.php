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
        Schema::connection('mysql')->create(ConfigTableName::$ADMINISTRATIVE_CITY, function (Blueprint $table) {
            $table->comment('標準縣市資料主檔');

            $table->string('city_code', 20)->comment('政府或行政區資料來源提供的縣市代碼');
            $table->string('city_name', 25)->nullable(false)->comment('標準縣市名稱，例如臺北市、臺中市');
            $table->boolean('is_active')->nullable(false)->default(true)->comment('該縣市是否為目前有效的行政區資料');
            $table->unsignedInteger('sort_order')->nullable(false)->default(0)->comment('縣市顯示順序；0 表示尚未設定');
            $table->dateTime('created_at', 6)->useCurrent()->comment('資料建立時間，統一保存 UTC');
            $table->dateTime('updated_at', 6)->useCurrent()->useCurrentOnUpdate()->comment('資料最後更新時間，統一保存 UTC');

            $table->primary(['city_code']);

            $table->index(['city_name'], 'idx_city_name');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::connection('mysql')->dropIfExists(ConfigTableName::$ADMINISTRATIVE_CITY);
    }
};
