<?php

use Illuminate\Support\Facades\Route;

Route::get('/items', function () {
    return response()->json([
        'status' => 'success',
        'message' => '取得商品列表成功',
        'data' => [
            [
                'item_id' => 1,
                'title' => '柯南漫畫',
                'price' => 150,
                'store_name' => '新莊店',
                'image_url' => '/images/items/test-image.png'
            ],
            [
                'item_id' => 2,
                'title' => '多啦A夢漫畫',
                'price' => 80,
                'store_name' => '板橋店',
                'image_url' => '/images/items/test-image.png'
            ],
            [
                'item_id' => 3,
                'title' => '二手漫畫套書',
                'price' => 300,
                'store_name' => '中和店',
                'image_url' => '/images/items/test-image.png'
            ],
        ],
    ]);
});
