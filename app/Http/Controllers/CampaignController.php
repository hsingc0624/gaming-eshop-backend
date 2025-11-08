<?php

namespace App\Http\Controllers;

use App\Http\Requests\Campaign\IndexCampaignRequest;
use App\Http\Requests\Campaign\StoreCampaignRequest;
use App\Http\Requests\Campaign\ScheduleCampaignRequest;
use App\Http\Requests\Campaign\SendTestRequest;
use App\Http\Requests\Campaign\MetricsRequest;
use App\Services\CampaignService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Response;

class CampaignController extends Controller
{
    /**
     * @param CampaignService $service
     */
    public function __construct(
        private CampaignService $service
    ) {}

    /**
     * @param IndexCampaignRequest $r
     * @return JsonResponse
     */
    public function index(IndexCampaignRequest $r): JsonResponse
    {
        $perPage = (int) ($r->integer('per_page') ?: 20);

        return response()->json(
            $this->service->index($perPage)
        );
    }

    /**
     * @param StoreCampaignRequest $r
     * @return JsonResponse
     */
    public function store(StoreCampaignRequest $r): JsonResponse
    {
        $c = $this->service->store($r->validated());

        return response()->json($c, 201);
    }

    /**
     * @param int                    $id
     * @param ScheduleCampaignRequest $r
     * @return JsonResponse
     */
    public function schedule(int $id, ScheduleCampaignRequest $r): JsonResponse
    {
        $c = $this->service->schedule($id, $r->validated()['scheduled_at']);

        return response()->json($c);
    }

    /**
     * @param int               $id
     * @param SendTestRequest   $r
     * @return Response
     */
    public function sendTest(int $id, SendTestRequest $r): Response
    {
        $this->service->sendTest($id, $r->validated()['email']);

        return response()->noContent();
    }

    /**
     * @param MetricsRequest $r
     * @return JsonResponse
     */
    public function metrics(MetricsRequest $r): JsonResponse
    {
        $days = (int) ($r->integer('days') ?: 30);

        return response()->json(
            $this->service->metrics($days)
        );
    }
}
