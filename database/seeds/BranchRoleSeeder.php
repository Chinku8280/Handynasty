<?php

use App\Role;
use Illuminate\Database\Seeder;

class BranchRoleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // Check if the "Branch" role already exists
        $branchRole = Role::where('name', 'Branch')->first();

        // If the role doesn't exist, create it
        if (!$branchRole) {
            $role = new Role();
            $role->name = 'Branch';
            $role->display_name = 'Branch';
            $role->description = 'Role for branch users';
            $role->save();
        }
    }
}
