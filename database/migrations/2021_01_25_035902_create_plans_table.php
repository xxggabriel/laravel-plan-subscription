<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePlansTable extends Migration
{
    public function up()
    {
        Schema::create('plans', function (Blueprint $table) {

            $table->increments('id');
            $table->string('slug');
            $table->string('name');
            $table->string('description')->nullable();
            $table->boolean('is_active')->default(true);
            $table->decimal('price')->default(0.00);
            $table->decimal('signup_fee')->default(0.00)->nullable();
            $table->string('currency', 'BRL');
            $table->smallInteger('trial_period')->unsigned()->default(0)->nullable();
            $table->string('trial_interval')->default('day')->nullable();
            $table->smallInteger('invoice_period')->unsigned()->default(0);
            $table->string('invoice_interval')->default('month');
            $table->smallInteger('grace_period')->unsigned()->default(0);
            $table->string('grace_interval')->default('day');
            $table->tinyInteger('prorate_day')->unsigned()->nullable();
            $table->tinyInteger('prorate_period')->unsigned()->nullable();
            $table->tinyInteger('prorate_extend_due')->unsigned()->nullable();
            $table->smallInteger('active_subscribers_limit')->unsigned()->nullable();
            $table->mediumInteger('sort_order')->unsigned()->default(0);

            $table->text('differential')->nullable();
            $table->timestamps();
            $table->softDeletes();


        });
    }

    public function down()
    {
        Schema::dropIfExists('plans');
    }
}
