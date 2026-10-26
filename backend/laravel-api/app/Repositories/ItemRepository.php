<?php

namespace App\Repositories;

use App\ConfigTableName;
use Illuminate\Support\Facades\DB;

class ItemRepository
{
    public static function FetchList(string $searchKeyWord = "", int $perPage = 10)
    {
        // 連線與表名
        $dbConnection = "mysql";
        $TABLE_STORE_ITEM = ConfigTableName::$STORE_ITEM;
        $TABLE_STORE = ConfigTableName::$STORE;
        $TABLE_PLATFORM = ConfigTableName::$PLATFORM;
        $TABLE_STORE_ITEM_KEYWORD = ConfigTableName::$STORE_ITEM_KEYWORD;
        $TABLE_SEARCH_KEYWORD = ConfigTableName::$SEARCH_KEYWORD;
        $TABLE_STORE_ITEM_IMAGE = ConfigTableName::$STORE_ITEM_IMAGE;
        $TABLE_STORE_ADDRESS = ConfigTableName::$STORE_ADDRESS;

        $query = DB::connection($dbConnection)
            ->table($TABLE_STORE_ITEM . " AS si")
            ->join($TABLE_STORE . " AS s", 's.store_id', '=', 'si.store_id')
            ->join($TABLE_PLATFORM . " AS p", 'p.platform_id', '=', 's.platform_id')

            ->leftJoin($TABLE_STORE_ITEM_IMAGE . " AS sii", function ($join) {
                $join->on('sii.item_id', '=', 'si.item_id')
                    ->where('sii.image_order', '=', 1);
            })
            ->leftJoin($TABLE_STORE_ADDRESS . " AS sa", 'sa.store_id', '=', 's.store_id');
        if ($searchKeyWord !== "") {
            $query = $query
                ->join($TABLE_STORE_ITEM_KEYWORD . " AS sik", 'sik.item_id', '=', 'si.item_id')
                ->join($TABLE_SEARCH_KEYWORD . " AS sk", 'sk.search_keyword_id', '=', 'sik.search_keyword_id')
                ->where('sk.keyword', '=', $searchKeyWord);
        }

        $paginator = $query->select(
            [
                "p.platform_name",
                "s.store_name",
                "si.item_id",
                "si.external_item_id",
                "si.item_name",
                "si.current_price",
                "si.currency_code",
                "sa.source_address_text",
                "sii.source_image_url"
            ]
        )->orderBy('si.first_seen_at', 'DESC')
            ->orderBy('si.item_id', 'DESC')
            ->paginate($perPage);

        return $paginator;
    }
}
