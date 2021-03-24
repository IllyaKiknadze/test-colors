<?php

namespace App\Http\Controllers;

use App\Http\Requests\WatterMarkRequest;
use App\Http\Resources\WatterMarkResource;
use App\Services\WatterMarkService;
use Illuminate\Http\JsonResponse;


class WatterMarkController extends Controller
{
    /**
     * @var WatterMarkService
     */
    private $watterMarkService;

    public function __construct(WatterMarkService $watterMarkService)
    {
        $this->watterMarkService = $watterMarkService;
    }

    public function addWatterMark(WatterMarkRequest $request): JsonResponse
    {
        if ($image = $this->watterMarkService->storeImage($request->file('image'))) {
            $mainColor = $this->watterMarkService->defineColor($image);
            $path      = $this->watterMarkService->addWatterMark($mainColor, $image);
            return response()->json(WatterMarkResource::make(['path' => $path]));
        }

        return response()->json(['message' => 'File can\'t be stored'], 500);
    }
}
