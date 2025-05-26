<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddCustomCssColumnInFrontAndAdminThemeSettingsTables extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('front_theme_settings', function (Blueprint $table) {
            $table->longText('custom_css')->nullable()->after('secondary_color');
        });

        Schema::table('theme_settings', function (Blueprint $table) {
            $table->longText('custom_css')->nullable()->after('topbar_text_color');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        if (Schema::hasColumn('front_theme_settings', 'custom_css')) {
            Schema::table('front_theme_settings', function (Blueprint $table) {
                $table->dropColumn('custom_css');
            });
        }

        if (Schema::hasColumn('theme_settings', 'custom_css')) {
            Schema::table('theme_settings', function (Blueprint $table) {
                $table->dropColumn('custom_css');
            });
        }
    }
}
