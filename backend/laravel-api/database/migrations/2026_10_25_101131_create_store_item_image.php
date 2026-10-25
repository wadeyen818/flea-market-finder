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
        Schema::connection('mysql')->create(ConfigTableName::$STORE_ITEM_IMAGE, function (Blueprint $table) {
            $table->comment('商品多圖、來源網址及實體圖片副本保存資訊');

            $table->bigIncrements('item_image_id')->comment('商品圖片主鍵');
            $table->unsignedBigInteger('item_id')->nullable(false)->comment('圖片所屬商品主鍵，關聯 item.item_id');
            $table->string('source_image_url', 600)->nullable(false)->comment('來源平台提供的圖片網址；不代表系統保存副本的位置');
            $table->char('image_identity_hash', 64)->charset('ascii')->collation('ascii_bin')->nullable(false)->comment('正規化來源圖片網址的 SHA-256，用於同商品來源圖片去重');
            $table->unsignedSmallInteger('image_order')->nullable(false)->comment('圖片顯示順序，從 1 開始；1 視為主圖');
            $table->string('domain', 255)->nullable(true)->comment('圖片副本固定存取網域，不含協定與路徑；無固定網域時為 NULL');
            $table->string('bucket_name', 100)->nullable(false)->comment('圖片副本實際保存的 S3 bucket 名稱');
            $table->string('bucket_key', 255)->nullable(false)->comment('圖片副本在 S3 中的 object key');
            $table->string('path', 600)->nullable(true)->comment('圖片副本完整固定存取網址，包含協定；無固定網址時為 NULL，不保存暫時簽名網址');
            $table->string('extension', 10)->nullable(false)->comment('圖片副本的副檔名，小寫且不含點，例如 jpg、png、webp');
            $table->unsignedBigInteger('file_size_bytes')->nullable(true)->comment('成功保存的圖片檔案大小，單位 byte；尚未取得時為 NULL');
            $table->unsignedInteger('width')->nullable(true)->comment('圖片寬度，單位 pixel；尚未取得時為 NULL');
            $table->unsignedInteger('height')->nullable(true)->comment('圖片高度，單位 pixel；尚未取得時為 NULL');
            $table->char('content_sha256', 64)->nullable(true)->charset('ascii')->collation('ascii_bin')->comment('成功保存的圖片檔案內容 SHA-256；不代表圖片視覺相似度');
            $table->dateTime('downloaded_at', 6)->nullable(false)->comment('最近一次成功從來源下載此圖片副本的時間，統一保存 UTC');
            $table->dateTime('created_at', 6)->useCurrent()->comment('資料建立時間，統一保存 UTC');
            $table->dateTime('updated_at', 6)->useCurrent()->useCurrentOnUpdate()->comment('資料最後更新時間，統一保存 UTC');

            $table->unique(['item_id', 'image_identity_hash'], 'uk_item_images_source_hash');

            $table->unique(['item_id', 'image_order'], 'uk_item_images_order');

            $table->foreign('item_id', 'fk_item_images_item')
                ->references('item_id')->on(ConfigTableName::$STORE_ITEM)
                ->onUpdate('restrict')->onDelete('restrict');
        });

        // Laravel Schema Builder 沒有 CHECK 方法；僅此處使用原生 SQL。
        DB::connection('mysql')->statement('ALTER TABLE ' . ConfigTableName::$STORE_ITEM_IMAGE . ' ADD CONSTRAINT chk_item_images_order CHECK ( image_order > 0 )');
        DB::connection('mysql')->statement('ALTER TABLE ' . ConfigTableName::$STORE_ITEM_IMAGE . ' ADD CONSTRAINT chk_item_images_file_size CHECK ( file_size_bytes IS NULL OR file_size_bytes > 0 )');
        DB::connection('mysql')->statement('ALTER TABLE ' . ConfigTableName::$STORE_ITEM_IMAGE . ' ADD CONSTRAINT chk_item_images_width CHECK ( width IS NULL OR width > 0 )');
        DB::connection('mysql')->statement('ALTER TABLE ' . ConfigTableName::$STORE_ITEM_IMAGE . ' ADD CONSTRAINT chk_item_images_height CHECK ( height IS NULL OR height > 0 )');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::connection('mysql')->dropIfExists(ConfigTableName::$STORE_ITEM_IMAGE);
    }
};
