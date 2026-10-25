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
        Schema::connection('mysql')->create(ConfigTableName::$ADMINISTRATIVE_DISTRICT, function (Blueprint $table) {
            $table->comment('標準鄉鎮市區資料主檔');

            $table->string('district_code', 20)->comment('政府或外部行政區資料來源提供的行政區代碼');
            $table->string('city_code', 20)->nullable(false)->comment('所屬縣市代碼，關聯 city.city_code');
            $table->string('district_name', 25)->nullable(false)->comment('標準行政區名稱，例如豐原區、新莊區');
            $table->boolean('is_active')->nullable(false)->default(true)->comment('該行政區是否為目前有效的行政區資料');
            $table->unsignedInteger('sort_order')->nullable(false)->default(0)->comment('同一縣市內的行政區顯示順序；0 表示尚未設定');
            $table->dateTime('created_at', 6)->useCurrent()->comment('資料建立時間，統一保存 UTC');
            $table->dateTime('updated_at', 6)->useCurrent()->useCurrentOnUpdate()->comment('資料最後更新時間，統一保存 UTC');

            $table->primary(['district_code']);

            $table->index(['city_code', 'district_name'], 'idx_district_city_name');

            $table->foreign('city_code', 'fk_district_city')
                ->references('city_code')->on(ConfigTableName::$ADMINISTRATIVE_CITY)
                ->onUpdate('restrict')->onDelete('restrict');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::connection('mysql')->dropIfExists(ConfigTableName::$ADMINISTRATIVE_DISTRICT);
    }
};
