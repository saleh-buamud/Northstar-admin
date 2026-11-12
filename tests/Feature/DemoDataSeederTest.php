<?php

use App\Models\Category;
use App\Models\Permission;
use App\Models\Product;
use App\Models\Role;
use App\Models\User;
use Database\Seeders\DemoDataSeeder;
use Illuminate\Foundation\Testing\LazilyRefreshDatabase;
use Illuminate\Support\Facades\DB;

uses(LazilyRefreshDatabase::class);

it('creates realistic related demo records once without changing super-admin permissions', function () {
    $superAdminRole = Role::factory()->create([
        'name' => 'Protected Super Admin',
        'slug' => 'super-admin',
        'is_system' => true,
    ]);
    $customPermission = Permission::query()->create([
        'name' => 'inventory.audit',
        'group' => 'inventory',
        'description' => 'Audit inventory records',
        'is_system' => false,
    ]);
    $superAdminRole->permissions()->attach($customPermission);
    $administrator = User::factory()->create();
    $administrator->roles()->attach($superAdminRole);
    $preservedPermissionIds = $superAdminRole->permissions()->pluck('permissions.id')->all();

    $this->seed(DemoDataSeeder::class);

    expect(Category::query()->where('demo_key', 'like', 'northstar-demo-category-%')->count())->toBe(25)
        ->and(Product::query()->where('demo_key', 'like', 'northstar-demo-product-%')->count())->toBe(150)
        ->and(User::query()->where('demo_key', 'like', 'northstar-demo-user-%')->count())->toBe(80)
        ->and(Product::query()->where('demo_key', 'like', 'northstar-demo-product-%')->where('status', 'draft')->count())->toBeGreaterThan(0)
        ->and(Product::query()->where('demo_key', 'like', 'northstar-demo-product-%')->where('stock_quantity', '<=', 5)->count())->toBeGreaterThan(0)
        ->and(DB::table('sales')->where('demo_key', 'like', 'northstar-demo-sale-%')->count())->toBeGreaterThan(0);

    $product = Product::query()->where('demo_key', 'northstar-demo-product-00001')->firstOrFail();
    $this->assertModelExists($product->category);
    $this->assertDatabaseHas('sales', ['demo_key' => 'northstar-demo-sale-0001']);
    $this->assertDatabaseHas('roles', ['slug' => 'customer']);
    expect($superAdminRole->fresh()->permissions()->pluck('permissions.id')->all())->toBe($preservedPermissionIds)
        ->and($administrator->fresh()->hasRole('super-admin'))->toBeTrue();

    $counts = collect(['categories', 'products', 'users', 'roles', 'permissions', 'role_user', 'permission_role', 'sales', 'activity_logs'])
        ->mapWithKeys(fn (string $table): array => [$table => DB::table($table)->count()]);

    $this->seed(DemoDataSeeder::class);

    expect(collect($counts)->mapWithKeys(fn (int $count, string $table): array => [$table => DB::table($table)->count()])->all())
        ->toBe($counts->all());
});
