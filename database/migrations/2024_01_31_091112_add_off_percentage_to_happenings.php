<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddOffPercentageToHappenings extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('happenings', function (Blueprint $table) {
            $table->decimal('off_percentage', 5, 2)->nullable()->before('created_at');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('happenings', function (Blueprint $table) {
            $table->dropColumn('off_percentage');
        });
    }
}
