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
        Schema::connection('mysql')->create(ConfigTableName::$STORE_ADDRESS, function (Blueprint $table) {
            $table->comment('分店目前已知地址；不保存無法確認的實際搬遷日期');

            $table->bigIncrements('address_id')->comment('地址主鍵');
            $table->unsignedBigInteger('store_id')->comment('地址所屬分店主鍵，關聯 store.store_id');
            $table->string('district_code', 20)->nullable(true)->comment('正規化後的行政區代碼；地址無法解析時允許為 NULL');
            $table->string('source_address_text', 500)->nullable(false)->comment('來源網站提供的完整地址原文，不進行修改');
            $table->string('street_address', 255)->nullable(true)->comment('解析後移除縣市與行政區的街道路段及門牌');
            $table->char('address_hash', 64)->charset('ascii')->collation('ascii_bin')->nullable(false)->comment('正規化地址內容的 SHA-256 十六進位字串，用於判斷地址是否改變');
            $table->decimal('latitude', 10, 7)->nullable(true)->comment('地址緯度；尚未進行地理編碼時允許為 NULL');
            $table->decimal('longitude', 10, 7)->nullable(true)->comment('地址經度；尚未進行地理編碼時允許為 NULL');
            $table->dateTime('first_observed_at', 6)->nullable(false)->comment('系統第一次觀察到目前地址的時間，不代表實際搬遷日期');
            $table->dateTime('last_verified_at', 6)->nullable(false)->comment('系統最近一次確認來源仍顯示目前地址的時間');
            $table->dateTime('address_changed_at', 6)->nullable(true)->comment('系統最近一次發現地址內容改變的時間，不代表實際搬遷日期');
            $table->dateTime('created_at', 6)->useCurrent()->comment('資料建立時間，統一保存 UTC');
            $table->dateTime('updated_at', 6)->useCurrent()->useCurrentOnUpdate()->comment('資料最後更新時間，統一保存 UTC');

            $table->unique(['store_id'], 'uk_address_store_id');

            $table->index(['district_code'], 'idx_address_district_code');

            $table->foreign('store_id', 'fk_address_store')
                ->references('store_id')->on(ConfigTableName::$STORE)
                ->onUpdate('restrict')->onDelete('cascade');

            $table->foreign('district_code', 'fk_address_district')
                ->references('district_code')->on(ConfigTableName::$ADMINISTRATIVE_DISTRICT)
                ->onUpdate('restrict')->onDelete('restrict');
        });

        // Laravel Schema Builder 沒有 CHECK 方法；僅此處使用原生 SQL。
        DB::connection('mysql')->statement('ALTER TABLE ' . ConfigTableName::$STORE_ADDRESS . ' ADD CONSTRAINT chk_address_observed_time CHECK ( last_verified_at >= first_observed_at )');
        DB::connection('mysql')->statement('ALTER TABLE ' . ConfigTableName::$STORE_ADDRESS . ' ADD CONSTRAINT chk_address_changed_time CHECK ( address_changed_at IS NULL OR address_changed_at >= first_observed_at )');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::connection('mysql')->dropIfExists(ConfigTableName::$STORE_ADDRESS);
    }
};
