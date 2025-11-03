<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Migrations\Migration;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('contact_messages', function (Blueprint $t) {
            $t->id();
            $t->string('name', 120);
            $t->string('email', 255);
            $t->text('message');
            $t->timestamps();
        });
    }
    public function down(): void
    {
        Schema::dropIfExists('contact_messages');
    }
};
