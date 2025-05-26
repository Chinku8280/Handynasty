<?php

use App\Module;
use Illuminate\Database\Migrations\Migration;

class CreateCouponAndDealModule extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {

        if(Module::where('name', 'coupon')->get()->count() == 0)
        {
            $module = new Module();
            $module->name = "coupon"; 
            $module->display_name = "Coupon"; 
            $module->description = "modules.module.couponDescription";
            $module->save(); 
        }
        
        if(Module::where('name', 'deal')->get()->count() == 0)
        {
            $module = new Module();
            $module->name = "deal"; 
            $module->display_name = "Deal"; 
            $module->description = "modules.module.dealDescription"; 
            $module->save();  
        }

    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        $modules = Module::select('id', 'name')->whereIn('name', ['deal', 'coupon'])->get();
        foreach ($modules as $module) {
            $module->delete();
        }
    }
}
