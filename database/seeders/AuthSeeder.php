<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\PermissionRegistrar;

class AuthSeeder extends Seeder
{
    public function run(): void
    {
        DB::transaction(function () {
            // Always clear cached roles/permissions before making changes
            app(PermissionRegistrar::class)->forgetCachedPermissions();

            $guard = 'web';

            // 1) Define canonical permission set
            $permissions = [
                'view_dashboard',
                'generate_reports',
                'export_data',
                'manage_users',
                'manage_roles',
                'manage_settings',
            ];

            foreach ($permissions as $name) {
                Permission::firstOrCreate(['name' => $name, 'guard_name' => $guard]);
            }

            // 2) Create roles
            $roles = [
                'admin'   => ['view_dashboard', 'generate_reports', 'export_data', 'manage_users', 'manage_roles', 'manage_settings'],
                'manager' => ['view_dashboard', 'generate_reports'],
                'staff'   => ['view_dashboard'],
                'user'    => ['generate_reports'],
            ];

            foreach ($roles as $roleName => $perms) {
                $role = Role::firstOrCreate(['name' => $roleName, 'guard_name' => $guard]);

                // Role-permission mapping is authoritative here
                $role->syncPermissions($perms);
            }

            // 3) Ensure users exist
            $users = [
                [
                    'name'  => 'Super Admin',
                    'email' => 'superadmin@example.com',
                    'role'  => 'admin',
                ],
                [
                    'name'  => 'Ops Manager',
                    'email' => 'manager1@example.com',
                    'role'  => 'manager',
                ],
                [
                    'name'  => 'Support Staff',
                    'email' => 'staff1@example.com',
                    'role'  => 'staff',
                ],
                [
                    'name'  => 'user',
                    'email' => 'user@example.com',
                    'role'  => 'user', // minimal role; we’ll augment via direct permissions
                ],
                [
                    'name'  => 'Security Agent',
                    'email' => 'security@example.com',
                    'role'  => 'manager', // we’ll grant an extra direct permission beyond role
                ],
            ];

            foreach ($users as $u) {
                /** @var \App\Models\User $user */
                $user = User::firstOrCreate(
                    ['email' => $u['email']],
                    [
                        'name' => $u['name'],
                        // Set a default password for local dev; replace in prod
                        'password' => bcrypt('password'),
                        'role' => $u['role'],
                    ]
                );

                // Assign the role idempotently
                $user->syncRoles([$u['role']]);
            }

            // 4) Direct user permissions (these populate model_has_permissions)
            $SuperAdmin   = User::where('email', 'superadmin@example.com')->firstOrFail();
            $OpsManager   = User::where('email', 'manager1@example.com')->firstOrFail();
            $SupportStaff   = User::where('email', 'staff1@example.com')->firstOrFail();
            $user   = User::where('email', 'user@example.com')->firstOrFail();
            $SecurityAgent  = User::where('email', 'security@example.com')->firstOrFail();
            // $manager   = User::where('email', 'manager@example.com')->firstOrFail();

            // Give auditor a special capability without changing roles
            $user->givePermissionTo('export_data');

            // Give Tech Lead elevated capability beyond manager role
            $SecurityAgent->givePermissionTo('manage_users');

            // Example: one manager gets export_data even if you later remove it from the manager role
            $OpsManager->givePermissionTo('export_data');

            // Clear cache again so the app immediately sees the latest mapping
            app(PermissionRegistrar::class)->forgetCachedPermissions();
        });
    }
}
