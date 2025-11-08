<?php

namespace App\Services;

use App\Models\ContactMessage;
use App\Repositories\ContactMessageRepositoryInterface;

class ContactService
{
    /**
     * @param ContactMessageRepositoryInterface $repo
     */
    public function __construct(
        private ContactMessageRepositoryInterface $repo
    ) {}

    /**
     * @param array $data
     * @return ContactMessage
     */
    public function store(array $data): ContactMessage
    {
        return $this->repo->create($data);
    }
}
