<?php

namespace App\Repositories;

use App\Models\ContactMessage;

interface ContactMessageRepositoryInterface
{
    /**
     * @param array $data
     * @return ContactMessage
     */
    public function create(array $data): ContactMessage;
}
