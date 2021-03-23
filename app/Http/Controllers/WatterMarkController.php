<?php

namespace App\Http\Controllers;

use App\Http\Requests\WatterMarkRequest;
use App\Http\Resources\WatterMarkResource;
use App\Services\WatterMarkService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

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
        $this->watterMarkService->addWatterMark($request->file('image'));
        return response()->json(WatterMarkResource::make());
    }
}
