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
        Schema::connection('mysql')->create(ConfigTableName::$CRAWL_RUN, function (Blueprint $table) {
            $table->comment('爬蟲每次執行的摘要紀錄，不保存逐件商品明細');

            $table->bigIncrements('crawl_run_id')->comment('爬蟲執行紀錄主鍵');
            $table->unsignedBigInteger('platform_id')->nullable(false)->comment('本次執行的平台主鍵，關聯 platform.platform_id');
            $table->unsignedBigInteger('search_keyword_id')->nullable(false)->comment('本次執行使用的搜尋關鍵字主鍵');
            $table->dateTime('started_at', 6)->nullable(false)->comment('爬蟲工作開始時間，統一保存 UTC');
            $table->dateTime('finished_at', 6)->nullable(true)->comment('爬蟲工作結束時間；執行中或異常中斷時可能為 NULL');
            $table->string('run_status', 20)->nullable(false)->comment('執行狀態：RUNNING、SUCCESS、PARTIAL_SUCCESS 或 FAILED');
            $table->unsignedInteger('scanned_page_count')->nullable(false)->default(0)->comment('本次執行完成掃描的搜尋結果頁數');
            $table->unsignedInteger('found_item_count')->nullable(false)->default(0)->comment('本次搜尋結果找到且於本次執行內去重後的商品數量');
            $table->unsignedInteger('new_item_count')->nullable(false)->default(0)->comment('本次執行首次發現並新增至系統的商品數量');
            $table->unsignedInteger('changed_item_count')->nullable(false)->default(0)->comment('本次執行發現名稱、價格或其他業務內容變動的既有商品數量');
            $table->unsignedInteger('unchanged_item_count')->nullable(false)->default(0)->comment('本次執行再次找到但業務內容沒有變化的既有商品數量');
            $table->unsignedInteger('failed_item_count')->nullable(false)->default(0)->comment('本次執行解析或寫入失敗的商品數量');
            $table->char('error_code', 4)->nullable(true)->charset('ascii')->collation('ascii_bin')->comment('背景工作內部錯誤代碼；成功或執行中時為 NULL');
            $table->text('error_message')->nullable(true)->comment('背景工作內部錯誤摘要；不直接提供給一般使用者');
            $table->dateTime('created_at', 6)->useCurrent()->comment('執行紀錄建立時間，統一保存 UTC');

            $table->index(['platform_id', 'started_at'], 'idx_crawl_run_platform_started');

            $table->index(['search_keyword_id', 'started_at'], 'idx_crawl_run_keyword_started');

            $table->index(['run_status', 'started_at'], 'idx_crawl_run_status_started');

            $table->foreign('platform_id', 'fk_crawl_run_platform')
                ->references('platform_id')->on(ConfigTableName::$PLATFORM)
                ->onUpdate('restrict')->onDelete('restrict');

            $table->foreign('search_keyword_id', 'fk_crawl_run_search_keyword')
                ->references('search_keyword_id')->on(ConfigTableName::$SEARCH_KEYWORD)
                ->onUpdate('restrict')->onDelete('restrict');
        });

        // Laravel Schema Builder 沒有 CHECK 方法；僅此處使用原生 SQL。
        DB::connection('mysql')->statement('ALTER TABLE ' . ConfigTableName::$CRAWL_RUN . ' ADD CONSTRAINT chk_crawl_run_time CHECK ( finished_at IS NULL OR finished_at >= started_at )');
        DB::connection('mysql')->statement('ALTER TABLE ' . ConfigTableName::$CRAWL_RUN . ' ADD CONSTRAINT chk_crawl_run_status CHECK ( run_status IN ( \'RUNNING\', \'SUCCESS\', \'PARTIAL_SUCCESS\', \'FAILED\' ) )');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::connection('mysql')->dropIfExists('crawl_run');
    }
};
