<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Permission;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class RolePermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $userPermissions = [
            'view role',
            'create role',
            'update role',
            'delete role',
            'view permission',
            'create permission',
            'update permission',
            'delete permission',
            'view user',
            'create user',
            'update user',
            'delete user',
        ];

        // Create permissions with the 'api' guard
        foreach ($userPermissions as $permission) {
            Permission::updateOrCreate(
                ['name' => $permission],
                [
                    'name' => $permission,
                    'guard_name' => 'api', // Ensure 'api' guard is used
                ]
            );
        }

        // Create roles with the 'api' guard
        Role::create(['name' => 'super-admin', 'guard_name' => 'api']);
        Role::create(['name' => 'admin', 'guard_name' => 'api']);

        $this->assignPermissionsToRoles();
        $this->createAndAssignUsers();
    }

    protected function assignPermissionsToRoles()
    {
        // Define which permissions to assign
        $rolePermissions = [
            'super-admin' => Permission::pluck('name')->toArray(),
            'admin' => ['create user', 'view user', 'update user'],
        ];

        // Assign permissions for each role with the 'api' guard
        foreach ($rolePermissions as $roleName => $permissions) {
            $role = Role::where('name', $roleName)->where('guard_name', 'api')->first(); // Use 'api' guard
            if ($role) {
                $role->givePermissionTo($permissions);
            }
        }
    }

    protected function createAndAssignUsers()
    {
        $usersData = [
            [
                'model' => User::class,
                'name' => 'مجلس الخدمة العامة الاتحادي',
                'email' => 'superadmin@gmail.com',
                'role' => 'super-admin',
            ],
            [
                'model' => User::class,
                'name' => 'root',
                'email' => 'manger@admin.com',
                'role' => 'admin',
            ],

        ];

        foreach ($usersData as $data) {
            $user = $data['model']::create([
                'name' => $data['name'],
                'email' => $data['email'],
                'password' => Hash::make('12345678'),
            ]);

            // Assign roles with the 'api' guard
            $user->assignRole(Role::findByName($data['role'], 'api')); // Use 'api' guard
        }
    }
}
