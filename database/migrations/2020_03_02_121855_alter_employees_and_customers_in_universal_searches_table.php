<?php

use App\UniversalSearch;
use App\User;
use Illuminate\Database\Migrations\Migration;

class AlterEmployeesAndCustomersInUniversalSearchesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        $searches = UniversalSearch::whereIn('searchable_type', ['Customer', 'Employee'])->get();
        foreach ($searches as $search) {
            $user = User::select('id')->where('id', $search->searchable_id)->first();

            $search->searchable_type = $user->is_customer ? 'Customer' : 'Employee';
            $search->route_name = $user->is_customer ? 'admin.customers.show' : 'admin.employee.edit';

            $search->save();
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
