<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Repositories\ProductRepositoryInterface;
use App\Repositories\EloquentProductRepository;
use App\Repositories\UserRepositoryInterface;
use App\Repositories\CartRepositoryInterface;
use App\Repositories\EloquentUserRepository;
use App\Repositories\EloquentCartRepository;
use App\Repositories\CampaignRepositoryInterface;
use App\Repositories\EloquentCampaignRepository;
use App\Repositories\ContactMessageRepositoryInterface;
use App\Repositories\EloquentContactMessageRepository;
use App\Repositories\OrderRepositoryInterface;
use App\Repositories\EloquentOrderRepository;
use App\Repositories\CheckoutRepositoryInterface;
use App\Repositories\EloquentCheckoutRepository;


/** 
 * @class RepositoryServiceProvider 
 */
class RepositoryServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->bind(ProductRepositoryInterface::class, EloquentProductRepository::class);
        $this->app->bind(UserRepositoryInterface::class, EloquentUserRepository::class);
        $this->app->bind(CartRepositoryInterface::class, EloquentCartRepository::class);
        $this->app->bind(CampaignRepositoryInterface::class, EloquentCampaignRepository::class);
        $this->app->bind(ContactMessageRepositoryInterface::class, EloquentContactMessageRepository::class);
        $this->app->bind(OrderRepositoryInterface::class, EloquentOrderRepository::class);
        $this->app->bind(CheckoutRepositoryInterface::class, EloquentCheckoutRepository::class);
    }
}
