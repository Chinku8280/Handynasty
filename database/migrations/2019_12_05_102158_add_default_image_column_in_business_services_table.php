<?php

use App\BusinessService;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddDefaultImageColumnInBusinessServicesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('business_services', function (Blueprint $table) {
            $table->string('default_image')->nullable()->after('image');
        });

        $services = BusinessService::select('id', 'image')->whereNotNull('image')->get();

        if ($services->count() > 0) {
            foreach ($services as $service) {
                $service->default_image = $service->image;
                $service->save();
            }
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('business_services', function (Blueprint $table) {
            //
        });
    }
}
