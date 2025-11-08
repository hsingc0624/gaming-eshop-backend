<?php

namespace App\Repositories;

use App\Models\Campaign;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

class EloquentCampaignRepository implements CampaignRepositoryInterface
{
    /**
     * @inheritDoc
     */
    public function paginate(int $perPage): LengthAwarePaginator
    {
        return Campaign::query()
            ->latest()
            ->paginate($perPage);
    }

    /**
     * @inheritDoc
     */
    public function create(array $data): Campaign
    {
        return Campaign::create($data);
    }

    /**
     * @inheritDoc
     */
    public function find(int $id): Campaign
    {
        return Campaign::findOrFail($id);
    }

    /**
     * @inheritDoc
     */
    public function update(Campaign $campaign, array $data): Campaign
    {
        $campaign->update($data);

        return $campaign->fresh();
    }

    /**
     * @inheritDoc
     */
    public function metricsRows(int $days): Collection
    {
        return DB::table('campaigns')
            ->selectRaw('DATE(created_at) as d, COALESCE(SUM(sent_count),0) as s')
            ->where('created_at', '>=', now()->subDays($days))
            ->groupBy('d')
            ->orderBy('d')
            ->get();
    }
}
