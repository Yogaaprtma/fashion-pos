<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Role;
use App\Models\Permission;

class RolePermissionSeeder extends Seeder
{
    public function run(): void
    {
        // Create Roles
        $roles = [
            ['name' => 'admin', 'display_name' => 'Administrator', 'description' => 'Full akses ke semua fitur sistem'],
            ['name' => 'manajemen', 'display_name' => 'Manajemen / Owner', 'description' => 'Akses laporan keuangan dan dashboard'],
            ['name' => 'supervisor', 'display_name' => 'Supervisor', 'description' => 'Pengawas operasional toko'],
            ['name' => 'kasir', 'display_name' => 'Kasir', 'description' => 'Petugas kasir untuk proses transaksi'],
            ['name' => 'gudang', 'display_name' => 'Admin Gudang', 'description' => 'Pengelola stok dan logistik'],
        ];

        foreach ($roles as $role) {
            Role::updateOrCreate(['name' => $role['name']], $role);
        }

        // Create Permissions
        $permissions = [
            // POS
            ['name' => 'pos.access', 'module' => 'pos', 'display_name' => 'Akses POS Kasir'],
            ['name' => 'pos.void', 'module' => 'pos', 'display_name' => 'Void Transaksi'],
            ['name' => 'pos.return', 'module' => 'pos', 'display_name' => 'Proses Retur'],
            ['name' => 'pos.discount', 'module' => 'pos', 'display_name' => 'Berikan Diskon'],
            ['name' => 'pos.session', 'module' => 'pos', 'display_name' => 'Kelola Sesi Kasir'],
            // Inventory
            ['name' => 'inventory.view', 'module' => 'inventory', 'display_name' => 'Lihat Inventori'],
            ['name' => 'inventory.manage', 'module' => 'inventory', 'display_name' => 'Kelola Produk & Stok'],
            ['name' => 'inventory.opname', 'module' => 'inventory', 'display_name' => 'Stock Opname'],
            // Purchase
            ['name' => 'purchase.view', 'module' => 'purchase', 'display_name' => 'Lihat Purchase Order'],
            ['name' => 'purchase.manage', 'module' => 'purchase', 'display_name' => 'Kelola Purchase Order'],
            // Reports
            ['name' => 'report.sales', 'module' => 'report', 'display_name' => 'Laporan Penjualan'],
            ['name' => 'report.financial', 'module' => 'report', 'display_name' => 'Laporan Keuangan'],
            ['name' => 'report.export', 'module' => 'report', 'display_name' => 'Export Laporan'],
            // Customers
            ['name' => 'customer.manage', 'module' => 'customer', 'display_name' => 'Kelola Pelanggan'],
            // Assets
            ['name' => 'asset.view', 'module' => 'asset', 'display_name' => 'Lihat Aset'],
            ['name' => 'asset.manage', 'module' => 'asset', 'display_name' => 'Kelola Aset'],
            // Users
            ['name' => 'user.manage', 'module' => 'user', 'display_name' => 'Kelola Pengguna'],
            // Settings
            ['name' => 'setting.manage', 'module' => 'setting', 'display_name' => 'Kelola Pengaturan'],
        ];

        foreach ($permissions as $perm) {
            Permission::updateOrCreate(['name' => $perm['name']], $perm);
        }

        // Assign Permissions to Roles
        $admin = Role::where('name', 'admin')->first();
        $manajemen = Role::where('name', 'manajemen')->first();
        $supervisor = Role::where('name', 'supervisor')->first();
        $kasir = Role::where('name', 'kasir')->first();
        $gudang = Role::where('name', 'gudang')->first();

        $allPermissions = Permission::all();
        $admin->permissions()->sync($allPermissions->pluck('id'));

        $manajemenPerms = Permission::whereIn('name', [
            'report.sales', 'report.financial', 'report.export',
            'inventory.view', 'purchase.view', 'asset.view',
        ])->pluck('id');
        $manajemen->permissions()->sync($manajemenPerms);

        $supervisorPerms = Permission::whereIn('name', [
            'pos.access', 'pos.void', 'pos.return', 'pos.discount', 'pos.session',
            'inventory.view', 'inventory.opname',
            'report.sales', 'purchase.view', 'asset.view', 'customer.manage'
        ])->pluck('id');
        $supervisor->permissions()->sync($supervisorPerms);

        $kasirPerms = Permission::whereIn('name', [
            'pos.access', 'pos.return', 'pos.discount', 'pos.session',
            'inventory.view', 'customer.manage',
        ])->pluck('id');
        $kasir->permissions()->sync($kasirPerms);

        $gudangPerms = Permission::whereIn('name', [
            'inventory.view', 'inventory.manage', 'inventory.opname',
            'purchase.view', 'purchase.manage',
        ])->pluck('id');
        $gudang->permissions()->sync($gudangPerms);
    }
}
