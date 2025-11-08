<?php

namespace App\Repositories;

use App\Models\Campaign;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;

interface CampaignRepositoryInterface
{
    /**
     * @param int $perPage
     * @return LengthAwarePaginator
     */
    public function paginate(int $perPage): LengthAwarePaginator;

    /**
     * @param array $data
     * @return Campaign
     */
    public function create(array $data): Campaign;

    /**
     * @param int $id
     * @return Campaign
     */
    public function find(int $id): Campaign;

    /**
     * @param Campaign $campaign
     * @param array    $data
     * @return Campaign
     */
    public function update(Campaign $campaign, array $data): Campaign;

    /**
     * @param int $days
     * @return Collection
     */
    public function metricsRows(int $days): Collection;
}
