<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('orders', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id');
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            $table->unsignedBigInteger('paymentmethode_Id');
            $table->foreign('paymentmethode_Id')->references('id')->on('payment_methodes')->onDelete('cascade');
            $table->unsignedBigInteger('methodelivraison_id');
            $table->foreign('methodelivraison_id')->references('id')->on('livraisons')->onDelete('cascade');
            $table->unsignedBigInteger('addresse_id');
            $table->foreign('addresse_id')->references('id')->on('addresses')->onDelete('cascade');
            $table->string('statut')->default('en cours');
            $table->integer('prixTotal');
            $table->boolean('ispaid')->default(false);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('orders');
    }
};
