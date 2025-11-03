<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('products', function (Blueprint $t) {
            $t->id();
            $t->string('name');
            $t->string('slug')->unique();
            $t->text('description')->nullable();
            $t->unsignedInteger('price_cents');
            $t->unsignedInteger('sale_price_cents')->nullable();
            $t->boolean('is_active')->default(true)->index();
            $t->timestamps();
        });

        Schema::create('product_images', function (Blueprint $t) {
            $t->id();
            $t->foreignId('product_id')->constrained()->cascadeOnDelete();
            $t->string('url');
            $t->unsignedSmallInteger('position')->default(0)->index();
            $t->timestamps();
        });

        Schema::create('product_variants', function (Blueprint $t) {
            $t->id();
            $t->foreignId('product_id')->constrained()->cascadeOnDelete();
            $t->string('sku')->unique();
            $t->json('options')->nullable();
            $t->unsignedInteger('price_cents');
            $t->integer('stock')->default(0)->index();
            $t->timestamps();
        });

        Schema::create('categories', function (Blueprint $t) {
            $t->id();
            $t->string('name');
            $t->string('slug')->unique();
            $t->timestamps();
        });

        Schema::create('product_category', function (Blueprint $t) {
            $t->id();
            $t->foreignId('product_id')->constrained()->cascadeOnDelete();
            $t->foreignId('category_id')->constrained()->cascadeOnDelete();
            $t->unique(['product_id','category_id']);
        });

        Schema::create('carts', function (Blueprint $t) {
            $t->id();
            $t->foreignId('user_id')->nullable()->constrained()->nullOnDelete();
            $t->uuid('cart_token')->unique();
            $t->timestamps();
        });

        Schema::create('cart_items', function (Blueprint $t) {
            $t->id();
            $t->foreignId('cart_id')->constrained()->cascadeOnDelete();
            $t->foreignId('product_id')->constrained()->cascadeOnDelete();
            $t->foreignId('product_variant_id')->nullable()->constrained()->nullOnDelete();
            $t->unsignedInteger('price_cents');
            $t->unsignedInteger('qty');
            $t->timestamps();
            $t->unique(['cart_id','product_id','product_variant_id']);
        });

        Schema::create('orders', function (Blueprint $t) {
            $t->id();
            $t->string('number')->unique();
            $t->foreignId('user_id')->nullable()->constrained()->nullOnDelete();
            $t->string('status')->default('pending')->index();
            $t->unsignedInteger('subtotal_cents');
            $t->unsignedInteger('discount_cents')->default(0);
            $t->unsignedInteger('shipping_cents')->default(0);
            $t->unsignedInteger('tax_cents')->default(0);
            $t->unsignedInteger('total_cents');
            $t->string('payment_method')->nullable();
            $t->string('payment_ref')->nullable();
            $t->timestamps();
        });

        Schema::create('order_items', function (Blueprint $t) {
            $t->id();
            $t->foreignId('order_id')->constrained()->cascadeOnDelete();
            $t->foreignId('product_id')->constrained()->cascadeOnDelete();
            $t->foreignId('product_variant_id')->nullable()->constrained()->nullOnDelete();
            $t->string('name');
            $t->string('sku')->nullable();
            $t->unsignedInteger('price_cents');
            $t->unsignedInteger('qty');
            $t->unsignedInteger('subtotal_cents');
            $t->timestamps();
        });

        Schema::create('addresses', function (Blueprint $t) {
            $t->id();
            $t->morphs('addressable'); 
            $t->string('type');
            $t->string('name');
            $t->string('line1');
            $t->string('line2')->nullable();
            $t->string('city');
            $t->string('postcode');
            $t->string('country')->default('GB');
            $t->timestamps();
        });

        Schema::create('wishlists', function (Blueprint $t) {
            $t->id();
            $t->foreignId('user_id')->constrained()->cascadeOnDelete();
            $t->timestamps();
        });

        Schema::create('wishlist_items', function (Blueprint $t) {
            $t->id();
            $t->foreignId('wishlist_id')->constrained()->cascadeOnDelete();
            $t->foreignId('product_id')->constrained()->cascadeOnDelete();
            $t->timestamps();
            $t->unique(['wishlist_id','product_id']);
        });

        Schema::create('coupons', function (Blueprint $t) {
            $t->id();
            $t->string('code')->unique();
            $t->enum('type', ['percent','fixed']);
            $t->unsignedInteger('value');
            $t->dateTime('starts_at')->nullable();
            $t->dateTime('ends_at')->nullable();
            $t->unsignedInteger('max_uses')->nullable();
            $t->unsignedInteger('uses')->default(0);
            $t->timestamps();
        });

        Schema::create('campaigns', function (Blueprint $t) {
            $t->id();
            $t->string('name');
            $t->string('subject');
            $t->text('html');
            $t->string('segment')->nullable();
            $t->enum('status', ['draft','scheduled','sending','sent'])->default('draft')->index();
            $t->dateTime('scheduled_at')->nullable()->index();
            $t->unsignedInteger('sent_count')->default(0);
            $t->timestamps();
        });

        Schema::create('campaign_sends', function (Blueprint $t) {
            $t->id();
            $t->foreignId('campaign_id')->constrained()->cascadeOnDelete();
            $t->foreignId('user_id')->nullable()->constrained()->nullOnDelete();
            $t->string('email')->index();
            $t->enum('status', ['queued','sent','failed'])->default('queued')->index();
            $t->string('provider_id')->nullable();
            $t->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('campaign_sends');
        Schema::dropIfExists('campaigns');

        Schema::dropIfExists('wishlist_items');
        Schema::dropIfExists('wishlists');

        Schema::dropIfExists('addresses');

        Schema::dropIfExists('order_items');
        Schema::dropIfExists('orders');

        Schema::dropIfExists('cart_items');
        Schema::dropIfExists('carts');

        Schema::dropIfExists('product_category');
        Schema::dropIfExists('product_variants');
        Schema::dropIfExists('product_images');
        Schema::dropIfExists('products');

        Schema::dropIfExists('categories');

        Schema::dropIfExists('coupons');
    }
};
