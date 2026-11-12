<?php

namespace Database\Seeders;

use App\Models\ActivityLog;
use App\Models\Category;
use App\Models\Permission;
use App\Models\Product;
use App\Models\Role;
use App\Models\Sale;
use App\Models\User;
use App\Services\PermissionCatalog;
use Carbon\Carbon;
use Faker\Generator;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Seeder;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class DemoDataSeeder extends Seeder
{
    private const CATEGORY_NAMES = [
        'Audio & Headphones',
        'Cameras & Optics',
        'Computers',
        'Computer Accessories',
        'Smartphones',
        'Tablets',
        'Smart Home',
        'Gaming',
        'Office Supplies',
        'Furniture',
        'Lighting',
        'Kitchen Appliances',
        'Home Organization',
        'Outdoor Equipment',
        'Fitness & Wellness',
        'Personal Care',
        'Travel Accessories',
        'Books & Media',
        'Toys & Games',
        'Baby & Kids',
        'Pet Supplies',
        'Automotive Accessories',
        'Wearables',
        'Networking',
        'Storage & Drives',
    ];

    private const PRODUCT_TYPES = [
        'Wireless Headphones',
        'Smart Speaker',
        'Laptop Stand',
        'USB-C Hub',
        'Mirrorless Camera',
        'Mechanical Keyboard',
        'Portable SSD',
        'Desk Lamp',
        'Fitness Watch',
        'Travel Charger',
        'Bluetooth Mouse',
        'Tablet Cover',
        'Wi-Fi Router',
        'Coffee Grinder',
        'Air Purifier',
    ];

    /**
     * @var array<string, array{name: string, permissions: list<string>}>
     */
    private const ROLE_DEFINITIONS = [
        'super-admin' => ['name' => 'Super Admin', 'permissions' => []],
        'admin' => ['name' => 'Admin', 'permissions' => []],
        'manager' => ['name' => 'Manager', 'permissions' => ['dashboard.view', 'products.view', 'products.create', 'products.edit', 'users.view']],
        'editor' => ['name' => 'Editor', 'permissions' => ['dashboard.view', 'products.view', 'products.create', 'products.edit']],
        'user' => ['name' => 'User', 'permissions' => ['dashboard.view', 'products.view']],
        'customer' => ['name' => 'Customer', 'permissions' => ['dashboard.view', 'products.view']],
    ];

    public function run(): void
    {
        DB::transaction(function (): void {
            $faker = fake('en_US');
            $permissions = $this->seedPermissions();
            $roles = $this->seedRoles($permissions);
            $users = $this->seedUsers($faker, $roles);
            $actor = $users->first(fn (User $user): bool => $user->is_active);
            $categories = $this->seedCategories($faker, $actor);
            $products = $this->seedProducts($faker, $categories, $actor);
            $this->seedSales($faker, $users, $actor);

            $this->command?->info(sprintf(
                'Demo data is ready: %d categories, %d products, and %d users.',
                $categories->count(),
                $products->count(),
                $users->count(),
            ));
        });
    }

    /**
     * @return array<string, Permission>
     */
    private function seedPermissions(): array
    {
        $permissions = [];

        foreach (app(PermissionCatalog::class)->defaults() as $name => $attributes) {
            $permissions[$name] = Permission::query()->firstOrCreate(
                ['name' => $name],
                [...$attributes, 'is_system' => true],
            );
        }

        return $permissions;
    }

    /**
     * @param  array<string, Permission>  $permissions
     * @return array<string, Role>
     */
    private function seedRoles(array $permissions): array
    {
        $allPermissionNames = array_keys($permissions);
        $roles = [];

        foreach (self::ROLE_DEFINITIONS as $slug => $definition) {
            $role = Role::query()
                ->where('slug', $slug)
                ->orWhere('name', $definition['name'])
                ->first();

            if ($role === null) {
                $role = Role::query()->create([
                    'name' => $definition['name'],
                    'slug' => $slug,
                    'is_system' => true,
                ]);
                $permissionNames = in_array($slug, ['super-admin', 'admin'], true)
                    ? $allPermissionNames
                    : $definition['permissions'];
                $role->permissions()->sync(collect($permissionNames)->map(
                    fn (string $name): int => $permissions[$name]->id,
                ));
            }

            $roles[$slug] = $role;
        }

        return $roles;
    }

    /**
     * @param  array<string, Role>  $roles
     * @return Collection<int, User>
     */
    private function seedUsers(Generator $faker, array $roles): Collection
    {
        $users = collect();
        $roleSlugs = ['admin', 'manager', 'editor', 'customer', 'customer', 'user'];

        for ($index = 1; $index <= 80; $index++) {
            $demoKey = sprintf('northstar-demo-user-%03d', $index);
            $user = User::withTrashed()->where('demo_key', $demoKey)->first();

            if ($user === null) {
                $email = sprintf('demo.user.%03d@northstar.test', $index);

                if (User::withTrashed()->where('email', $email)->exists()) {
                    $email = sprintf('demo.user.%03d+%s@northstar.test', $index, Str::lower(Str::random(8)));
                }

                $createdAt = now()->subDays($faker->numberBetween(0, 730));
                $user = User::factory()->create([
                    'name' => $faker->name(),
                    'email' => $email,
                    'demo_key' => $demoKey,
                    'password' => Str::random(48),
                    'phone_number' => $faker->phoneNumber(),
                    'avatar_path' => 'images/demo-avatar.svg',
                    'is_active' => $index % 5 !== 0,
                    'locale' => $index % 4 === 0 ? 'ar' : 'en',
                    'created_at' => $createdAt,
                    'updated_at' => $createdAt,
                ]);
                $user->roles()->attach($roles[$roleSlugs[($index - 1) % count($roleSlugs)]]);
                $this->recordActivity('user.created', $user, null, $createdAt);
            }

            $users->push($user);
        }

        return $users;
    }

    /**
     * @return Collection<int, Category>
     */
    private function seedCategories(Generator $faker, ?User $actor): Collection
    {
        $categories = collect();

        foreach (self::CATEGORY_NAMES as $index => $name) {
            $demoKey = sprintf('northstar-demo-category-%02d', $index + 1);
            $slug = 'northstar-demo-'.Str::slug($name);
            $category = Category::withTrashed()->where('demo_key', $demoKey)->first();

            if ($category === null) {
                if (Category::withTrashed()->where('slug', $slug)->exists()) {
                    $slug .= '-'.Str::lower(Str::random(8));
                }

                $categoryName = 'Northstar Demo: '.$name;
                if (Category::withTrashed()->where('name', $categoryName)->exists()) {
                    $categoryName .= ' '.Str::upper(Str::random(6));
                }

                $createdAt = now()->subDays($faker->numberBetween(0, 650));
                $category = Category::factory()->create([
                    'name' => $categoryName,
                    'slug' => $slug,
                    'demo_key' => $demoKey,
                    'description' => $faker->sentence(),
                    'status' => $index % 8 === 6 ? 'inactive' : 'active',
                    'created_at' => $createdAt,
                    'updated_at' => $createdAt,
                ]);
                $this->recordActivity('category.created', $category, $actor, $createdAt);
            }

            $categories->push($category);
        }

        return $categories;
    }

    /**
     * @param  Collection<int, Category>  $categories
     * @return Collection<int, Product>
     */
    private function seedProducts(Generator $faker, Collection $categories, ?User $actor): Collection
    {
        $products = collect();
        $categories = $categories->values();

        for ($index = 1; $index <= 150; $index++) {
            $demoKey = sprintf('northstar-demo-product-%05d', $index);
            $sku = sprintf('NSD-%05d', $index);
            $product = Product::withTrashed()->where('demo_key', $demoKey)->first();

            if ($product === null) {
                if (Product::withTrashed()->where('sku', $sku)->exists()) {
                    $sku .= '-'.Str::upper(Str::random(6));
                }

                $category = $categories[($index - 1) % $categories->count()];
                $price = $faker->randomFloat(2, 8, 2500);
                $salePrice = $index % 4 === 0
                    ? round($price * $faker->numberBetween(70, 90) / 100, 2)
                    : null;
                $createdAt = now()->subDays($faker->numberBetween(0, 730));
                $stock = match ($index % 10) {
                    0 => 0,
                    1 => 2,
                    2 => 5,
                    3 => 8,
                    default => $faker->numberBetween(12, 250),
                };
                $status = match ($index % 10) {
                    7, 8 => 'inactive',
                    9 => 'draft',
                    default => 'active',
                };

                $product = Product::factory()->create([
                    'category_id' => $category->id,
                    'demo_key' => $demoKey,
                    'name' => ucfirst($faker->words(2, true)).' '.self::PRODUCT_TYPES[($index - 1) % count(self::PRODUCT_TYPES)],
                    'sku' => $sku,
                    'description' => $faker->sentence().' Selected for the '.$category->name.' collection.',
                    'price' => $price,
                    'sale_price' => $salePrice,
                    'stock_quantity' => $stock,
                    'image_path' => 'images/demo-product.svg',
                    'status' => $status,
                    'created_at' => $createdAt,
                    'updated_at' => $createdAt,
                ]);
                $this->recordActivity('product.created', $product, $actor, $createdAt);
            }

            $products->push($product);
        }

        return $products;
    }

    /**
     * @param  Collection<int, User>  $users
     */
    private function seedSales(Generator $faker, Collection $users, ?User $actor): void
    {
        $creator = $users->first();

        for ($index = 1; $index <= 60; $index++) {
            $reference = sprintf('NSD-SALE-%04d', $index);
            $demoKey = sprintf('northstar-demo-sale-%04d', $index);

            if (Sale::query()->where('demo_key', $demoKey)->exists()) {
                continue;
            }

            $product = Product::query()->where('demo_key', sprintf('northstar-demo-product-%05d', $index))->first();

            if ($product === null || $product->status !== 'active' || $product->stock_quantity < 1) {
                continue;
            }
            if (Sale::query()->where('reference', $reference)->exists()) {
                $reference .= '-'.Str::upper(Str::random(6));
            }

            $quantity = min($product->stock_quantity, $faker->numberBetween(1, 3));
            $unitPrice = (float) ($product->sale_price ?? $product->price);
            $soldAt = now()->subDays($faker->numberBetween(0, 180));
            $sale = Sale::query()->create([
                'product_id' => $product->id,
                'created_by' => $creator?->id,
                'reference' => $reference,
                'demo_key' => $demoKey,
                'quantity' => $quantity,
                'unit_price' => $unitPrice,
                'total' => round($unitPrice * $quantity, 2),
                'sold_at' => $soldAt,
                'created_at' => $soldAt,
                'updated_at' => $soldAt,
            ]);
            $product->decrement('stock_quantity', $quantity);
            $this->recordActivity('sale.recorded', $sale, $actor, $soldAt);
        }
    }

    private function recordActivity(string $action, Model $subject, ?User $actor, Carbon $occurredAt): void
    {
        ActivityLog::query()->create([
            'user_id' => $actor?->id,
            'action' => $action,
            'subject_type' => $subject::class,
            'subject_id' => $subject->getKey(),
            'properties' => ['source' => 'demo-seeder'],
            'created_at' => $occurredAt,
            'updated_at' => $occurredAt,
        ]);
    }
}
