<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class PermissionSeeder extends Seeder
{
    public function run(): void
    {
        DB::table('role_has_permissions')->delete();

        // Reset cached roles and permissions
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        $menuMaster = ['master', 'master-user', 'master-role'];
        $menuTambah = ['tambah', 'tambah-konser', 'tambah-tiket'];
        $menuWebsite = ['website', 'setting'];
        $custom = ['transaksi', 'input'];

        $permissionsByRole = [
            'admin' => ['dashboard', ...$menuMaster, ...$menuWebsite, ...$menuTambah, ...$custom],
        ];

        $insertPermissions = fn ($role) => collect($permissionsByRole[$role])
            ->map(function ($name) {
                $check = Permission::whereName($name)->first();

                if (!$check) {
                    return Permission::create([
                        'name' => $name,
                        'guard_name' => 'api',
                    ])->id;
                }

                return $check->id;
            })
            ->toArray();

        $permissionIdsByRole = [
            'admin' => $insertPermissions('admin')
        ];

        foreach ($permissionIdsByRole as $role => $permissionIds) {
            $role = Role::whereName($role)->first();

            DB::table('role_has_permissions')
                ->insert(
                    collect($permissionIds)->map(fn ($id) => [
                        'role_id' => $role->id,
                        'permission_id' => $id
                    ])->toArray()
                );
        }

        $permissions = ['kelola', 'riwayat', 'pesanan', 'transaksi', 'produk', 'laporan'];
foreach ($permissions as $name) {
    Permission::firstOrCreate([
        'name' => $name,
        'guard_name' => 'api',
    ]);
}

    }
    // public function run(): void
    // {
    //     $permissions = ['''kelola', 'riwayat', 'pesanan', 'transaksi', 'produk', 'laporan']; // tambahkan semua permission yang kamu pakai

    //     foreach ($permissions as $name) {
    //         Permission::firstOrCreate([
    //             'name' => $name,
    //             'guard_name' => 'api',
    //         ]);
    //     }
    // }
}
