<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\ItemService;
use App\Util;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;

class ItemController extends Controller
{
    public function index(Request $request)
    {
        $success = false;
        $errorCode = "0000";
        $message = "";
        $outputData = null;

        try {
            $validated = Validator::make($request->query(), [
                'search_key_word' => ['nullable', 'string', 'max:20'],
                'page' => ['sometimes', 'integer', 'min:1'],
                'per_page' => ['sometimes', 'integer', 'min:1', 'max:50'],
            ])->validate();

            $keyword = trim($validated['search_key_word'] ?? '');
            $perPage = (int) ($validated['per_page'] ?? 12);

            // 取得列表
            $outputData = ItemService::FetchList($keyword, $perPage);
            $success = true;

            return Util::api_response($success, $errorCode, $message, $outputData);
        } catch (ValidationException $e) {
            $errorCode = "1001";
            $message = collect($e->errors())->flatten()->first();
            return Util::api_response(
                $success,
                $errorCode,
                $message,
                $outputData,
                422
            );
        } catch (\Exception $e) {
            $success = false;
            $errorCode = $e->getCode();
            $message = $e->getMessage();

            return Util::api_response($success, $errorCode, $message, $outputData, 500);
        }
    }
}
