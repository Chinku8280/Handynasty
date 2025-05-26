<?php

use App\BusinessService;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;

class AlterImageColumnToTextInBusinessServicesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        DB::statement('ALTER TABLE business_services MODIFY COLUMN image TEXT');

        $services = BusinessService::select('id', 'image')->whereNotNull('image')->get();

        if ($services->count() > 0) {
            foreach ($services as $service) {
                $service_image_arr = [];

                // make directory if not exists
                if (!File::isDirectory(public_path('/user-uploads/service/'.$service->id))) {
                    File::makeDirectory(public_path('/user-uploads/service/'.$service->id));
                    // move file to new service folder
                    File::move(public_path('/user-uploads/service/'.$service->image), public_path('/user-uploads/service/'.$service->id.'/'.$service->image));
                }

                array_push($service_image_arr, $service->image);
                $service->image = json_encode($service_image_arr);
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
        //
    }
}
