<?php

use App\Module;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateCouponsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('coupons', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedInteger('category_id')->nullable();
            $table->foreign('category_id')->references('id')->on('categories')->onUpdate('cascade')->onDelete('cascade');
            $table->string('title');
            $table->string('image')->nullable();
            $table->dateTime('start_date_time')->nullable();
            $table->dateTime('end_date_time')->nullable();
            $table->integer('uses_limit')->nullable();
            $table->integer('used_time')->nullable();
            $table->double('amount')->nullable();
            $table->integer('percent')->nullable();
            $table->integer('minimum_purchase_amount')->default(0);
            $table->enum('type', ['percent_wise','amount_wise','percent_minimum_amount',])->default('percent_wise');
            $table->text('days')->nullable();
            $table->enum('status',['active', 'inactive', 'expire'])->default('active');
            $table->text('description')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('coupons');
    }
}
