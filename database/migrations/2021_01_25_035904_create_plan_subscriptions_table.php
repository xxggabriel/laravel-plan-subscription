<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePlanSubscriptionsTable extends Migration
{
    public function up()
    {
        Schema::create('plan_subscriptions', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('plan_id')->unsigned();
            $table->string('slug');
            $table->string('name');
            $table->string('description')->nullable();
            $table->dateTime('trial_ends_at')->nullable();
            $table->dateTime('starts_at')->nullable();
            $table->dateTime('ends_at')->nullable();
            $table->dateTime('cancels_at')->nullable();
            $table->dateTime('canceled_at')->nullable();
            $table->string('timezone')->nullable();
            $table->timestamps();
            $table->softDeletes();
            $table->integer('status')->default(1);
            $table->dateTime('payment_failed_at')->nullable();

            $table->foreign('plan_id')
                ->references('id')
                ->on('plans')
                ->onDelete('cascade')
                ->onUpdate('cascade');

            $table->foreign('account_id')
                ->references('id')
                ->on('accounts')
                ->onDelete('cascade');
        });
    }

    public function down()
    {
        Schema::dropIfExists('plan_subscriptions');
    }
}
