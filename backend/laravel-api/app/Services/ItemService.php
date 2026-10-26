<?php

namespace App\Services;

use App\Repositories\ItemRepository;

class ItemService
{
    public static function FetchList(string $searchKeyWord = "", int $perPage)
    {
        return ItemRepository::FetchList($searchKeyWord, $perPage);
    }
}
