<?php

use App\UniversalSearch;
use App\User;
use Illuminate\Database\Migrations\Migration;

class ChangeCustomersToEmployeesInUniversalSearchesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        $searches = UniversalSearch::where('searchable_type', 'Customer')->get();
        foreach ($searches as $search) {
            $user = User::select('id')->where('id', $search->searchable_id)->first();
            if (is_null($user)) {
                $search->delete();
            }
            else {
                if (!$user->is_admin && !$user->is_customer) {
                    $search->searchable_type = 'Employee';
                    $search->route_name = 'admin.employee.edit';

                    $search->save();
                }
                else {
                    if ($user->is_admin) {
                        $search->delete();
                    }
                }
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
