<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class Add7ColumnsToDealsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('deals', function (Blueprint $table) 
        {
            if (!Schema::hasColumn('deals', 'location_id')) {
                $table->integer('location_id')->after('slug');
            }
            if (!Schema::hasColumn('deals', 'deal_type')) {
                $table->string('deal_type')->after('location_id');
            }
            if (!Schema::hasColumn('deals', 'discount_type')) {
                $table->string('discount_type');
            }
            if (!Schema::hasColumn('deals', 'percentage')) {
                $table->integer('percentage')->nullable();
            }
            if (!Schema::hasColumn('deals', 'deal_applied_on')) {
                 $table->string('deal_applied_on');
            }
            if (!Schema::hasColumn('deals', 'max_order_per_customer')) {
                $table->integer('max_order_per_customer')->nullable();
            }
            if (!Schema::hasColumn('deals', 'image')) {
                $table->string('image')->nullable();
            }
            if (!Schema::hasColumn('deals', 'open_time')) {
                $table->time('open_time');
            }
            if (!Schema::hasColumn('deals', 'close_time')) {
                $table->time('close_time');
            }
            if (Schema::hasColumn('deals', 'timing')) {
                $table->dropColumn('timing');
            } 
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('deals', function (Blueprint $table) 
        {
            if (Schema::hasColumn('deals', 'location_id')) {
                $table->dropColumn('location_id');
            }  
            if (Schema::hasColumn('deals', 'deal_type')) {
                $table->dropColumn('deal_type');
            }
            if (Schema::hasColumn('deals', 'discount_type')) {
                $table->dropColumn('discount_type');
            }
            if (Schema::hasColumn('deals', 'percentage')) {
                $table->dropColumn('percentage');
            }
            if (Schema::hasColumn('deals', 'deal_applied_on')) {
                $table->dropColumn('deal_applied_on');
            }
            if (Schema::hasColumn('deals', 'max_order_per_customer')) {
                $table->dropColumn('max_order_per_customer');
            }
            if (Schema::hasColumn('deals', 'image')) {
                $table->dropColumn('image');
            }
            if (Schema::hasColumn('deals', 'open_time')) {
                $table->dropColumn('open_time');
            }
            if (Schema::hasColumn('deals', 'close_time')) {
                $table->dropColumn('close_time');
            }
        });
    }
}
