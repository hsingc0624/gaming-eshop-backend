<?php

namespace App\Http\Controllers;

use App\Http\Requests\Contact\StoreContactMessageRequest;
use App\Services\ContactService;
use Illuminate\Http\JsonResponse;

class ContactController extends Controller
{
    /**
     * @param ContactService $service
     */
    public function __construct(
        private ContactService $service
    ) {}

    /**
     * @param StoreContactMessageRequest $r
     * @return JsonResponse
     */
    public function store(StoreContactMessageRequest $r): JsonResponse
    {
        $msg = $this->service->store($r->validated());

        return response()->json(
            ['ok' => true, 'id' => $msg->id],
            201
        );
    }
}
